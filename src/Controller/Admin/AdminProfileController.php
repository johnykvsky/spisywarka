<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
//use App\CommandHandler\Exception\ItemNotDeletedException;
use Symfony\Component\Form\FormError;
use Knp\Component\Pager\PaginatorInterface; 
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\FormInterface;
use Swagger\Annotations as SWG;
use App\DTO\UserDTO;
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
        $user = $this->getUser();

        if (!$user) {
            //return $this->redirectToRoute('logout');
        }

        $userDTO = new UserDTO($user->getFirstName(), $user->getLastName(), $user->getEmail(), null);
        $form = $this->createForm(\App\Form\Type\UserProfileType::class, $userDTO);
        $form->handleRequest($rawRequest);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $userDTO = $form->getData();
                $command = new UpdateUserProfileCommand(
                    $user->getId(),
                    $userDTO->getFirstName(),
                    $userDTO->getLastName(),
                    $userDTO->getEmail(),
                    $userDTO->getPlainPassword()
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

        return $this->render('security/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param string $id
     * @return FormInterface
     */
    private function getForm(UuidInterface $id): FormInterface
    {
        $user = $this->repository->getIUser($id);
        $userDTO = $this->itemService->fillItemDTO($item);
        return $this->createForm(\App\Form\Type\ItemType::class, $itemDTO);
    }
}
