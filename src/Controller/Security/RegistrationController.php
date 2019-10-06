<?php
namespace App\Controller\Security;

use App\Entity\User;
use App\Form\Type\UserRegistrationType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Ramsey\Uuid\Uuid;
use App\Entity\Enum\UserStatusEnum;

class RegistrationController extends Controller
{
    /**
     * @Route("/register", name="register", methods={"GET", "POST"})
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $encoder,
        TokenStorageInterface $tokenStorage,
        UserRepository $userRepository,
        SessionInterface $session
    ) {
        $user = new User(Uuid::uuid4());
        $form = $this->createForm(UserRegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $encoder->encodePassword($user, $user->getPlainPassword() ?? '');
            $user->setPassword($password);
            $user->setStatus(UserStatusEnum::active());
            $userRepository->save($user);

            $this->addFlash('success', "Your accound was created");
            
            $token = new UsernamePasswordToken($user, $password, 'main');
            $tokenStorage->setToken($token);
            $session->set('_security_main', serialize($token));
            
            return $this->redirectToRoute('admin_dashboard');
        }
        return $this->render(
            'security/register.html.twig',['form' => $form->createView()]
        );
    }
}