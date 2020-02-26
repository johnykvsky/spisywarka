<?php
namespace App\Controller\Security;

use App\DTO\UserRegistrationDTO;
use App\Form\Type\UserRegistrationType;
use App\Repository\UserRepository;
use App\Command\RegisterUserCommand;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\FormError;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Service\TokenStorageService;

class RegistrationController extends Controller
{
    /**
     * @var MessageBusInterface
     */
    private $commandBus;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @param MessageBusInterface $commandBus
     * @param LoggerInterface $logger
     * @param UserRepository $repository
     */
    public function __construct(
        MessageBusInterface $commandBus,
        LoggerInterface $logger,
        UserRepository $repository
    )
    {
        $this->commandBus = $commandBus;
        $this->logger = $logger;
        $this->repository = $repository;
    }

    /**
     * @Route("/register", name="register", methods={"GET", "POST"})
     */
    public function register(Request $request, TokenStorageService $tokenStorageService)
    {
        $userRegistrationDTO = new UserRegistrationDTO;
        $form = $this->createForm(UserRegistrationType::class, $userRegistrationDTO);
        $form->handleRequest($request);
        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $userRegistrationDTO = $form->getData();
                $command = new RegisterUserCommand(
                    Uuid::uuid4(),
                    $userRegistrationDTO->getFirstName(),
                    $userRegistrationDTO->getLastName(),
                    $userRegistrationDTO->getEmail(),
                    $userRegistrationDTO->getPlainPassword()
                );

                $this->commandBus->dispatch($command);
                $user = $this->repository->getUser($command->getId());
                $this->addFlash('success', "Your accound was created");
                $tokenStorageService->storeToken($user);
                return $this->redirectToRoute('admin_dashboard');
            }
        } catch (\Exception $e) {
            $this->addFlash('danger','Error while registering');
            $error = new FormError("There is an error: ".$e->getMessage());
            $form->addError($error);
        }

        return $this->render(
            'security/register.html.twig',['form' => $form->createView()]
        );
    }
}