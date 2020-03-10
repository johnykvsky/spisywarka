<?php
namespace App\Controller\Security;

use App\Command\ResetPasswordCommand;
use App\Command\ResetPasswordConfirmationCommand;
use App\Form\Type\NewPasswordType;
use App\Form\Type\PasswordRequestType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;

class ResettingController extends Controller
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
     * @Route("/reset_password", name="reset_password", methods={"GET", "POST"})
     */
    public function resetPassword(Request $request)
    {
        $form = $this->createForm(PasswordRequestType::class);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $command = new ResetPasswordCommand(
                    $form->get('email')->getData()
                );

                $this->commandBus->dispatch($command);
                $this->addFlash('success', "An email has been sent to your address");
                return $this->redirectToRoute('reset_password');
            }
        } catch (\Exception $e) {
            $this->addFlash('danger','Error while resetting password');
            $error = new FormError("There is an error: ".$e->getMessage());
            $form->addError($error);
        }

        return $this->render('security/reset-password.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/reset_password/confirm/{token}", name="reset_password_confirm", methods={"GET", "POST"})
     */
    public function resetPasswordCheck(Request $request, string $token) 
    {
        $token = filter_var($token, FILTER_SANITIZE_STRING);

        $form = $this->createForm(NewPasswordType::class);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $command = new ResetPasswordConfirmationCommand(
                    $token,
                    $form->get('password')->getData()
                );

                $this->commandBus->dispatch($command);
                $this->addFlash('success', "Your new password has been set");
                return $this->redirectToRoute('homepage');
            }
        } catch (\Exception $e) {
            $this->addFlash('danger','Error while resetting password');
            $error = new FormError("There is an error: ".$e->getMessage());
            $form->addError($error);
        }

        return $this->render('security/reset-password-confirm.html.twig', ['form' => $form->createView()]);
    }
}