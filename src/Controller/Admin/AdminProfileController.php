<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\DTO\ProfileDTO;
use App\Form\Type\UserProfileType;
use App\Command\UpdateUserProfileCommand;

class AdminProfileController extends AbstractController
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
     * @Route("/admin/profile", name="admin_profile", methods={"GET","POST"})
     * 
     * @param Request $rawRequest
     * @return Response|RedirectResponse
     */
    public function profile(Request $rawRequest): Response
    {
        /** @var User */
        $user = $this->getUser();

        if (empty($user)) {
            return $this->redirectToRoute('logout');
        }

        $profileDTO = new ProfileDTO(
            $user->getFirstName(),
            $user->getLastName(),
            $user->getEmail(),
            null
        );
        $form = $this->createForm(\App\Form\Type\UserProfileType::class, $profileDTO);
        $form->handleRequest($rawRequest);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $profileDTO = $form->getData();
                $command = new UpdateUserProfileCommand(
                    $user->getId(),
                    $profileDTO->getFirstName(),
                    $profileDTO->getLastName(),
                    $profileDTO->getEmail(),
                    $profileDTO->getPlainPassword()
                );
                $this->commandBus->dispatch($command);
                $this->addFlash('success','Your changes were saved!');
                return $this->redirectToRoute('admin_profile');
            }
        } catch (\Exception $e) {
            $this->addFlash('danger','Error while saving changes');
            $error = new FormError("There is an error: ".$e->getMessage());
            $form->addError($error);
        }

        return $this->render('admin/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
