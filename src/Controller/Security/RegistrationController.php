<?php
namespace App\Controller\Security;

use App\Entity\User;
use App\DTO\UserRegistrationDTO;
use App\Form\Type\UserRegistrationType;
use App\Repository\UserRepository;
use App\Command\RegisterUserCommand;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\FormError;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

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
    public function register(
        Request $request,
        TokenStorageInterface $tokenStorage,
        SessionInterface $session
    ) {
        $userRegistrationDTO = new UserRegistrationDTO;
        $form = $this->createForm(UserRegistrationType::class, $userRegistrationDTO);
        $form->handleRequest($request);
        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $userRegistrationDTO = $form->getData();
                $userId = Uuid::uuid4();
                $command = new RegisterUserCommand(
                    $userId,
                    $userRegistrationDTO->getFirstName(),
                    $userRegistrationDTO->getLastName(),
                    $userRegistrationDTO->getEmail(),
                    $userRegistrationDTO->getPlainPassword()
                );

                $this->commandBus->dispatch($command);
                $user = $this->repository->getUser($userId);
                $this->addFlash('success', "Your accound was created");
                $token = new UsernamePasswordToken($user, $user->getPassword(), 'main');
                $tokenStorage->setToken($token);
                $session->set('_security_main', serialize($token));
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