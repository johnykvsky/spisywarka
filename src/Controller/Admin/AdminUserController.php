<?php

namespace App\Controller\Admin;

use App\Command\DeleteUserCommand;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormError;
use Knp\Component\Pager\PaginatorInterface; 
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Service\UserService;
use Symfony\Component\Form\FormInterface;
use Swagger\Annotations as SWG;
use App\Traits\RequestQueryTrait;

class AdminUserController extends AbstractController
{
    use RequestQueryTrait;

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
     * @var UserService
     */

    private $userService;
    /**
     * @param MessageBusInterface $commandBus
     * @param LoggerInterface $logger
     * @param UserRepository $repository
     * @param UserService $userService
     */
    public function __construct(
        MessageBusInterface $commandBus,
        LoggerInterface $logger,
        UserRepository $repository,
        UserService $userService
    )
    {
        $this->commandBus = $commandBus;
        $this->logger = $logger;
        $this->repository = $repository;
        $this->userService = $userService;
    }

    /**
     * @Route("/admin/user/{id}", name="admin_user", defaults={"id"=null}, methods={"GET","POST"})
     * 
     * @param string|null $id
     * @param Request $rawRequest
     * @return Response|RedirectResponse
     */
    public function createOrEditUser(?string $id, Request $rawRequest): Response
    {
        $form = $this->getForm($id);
        $form->handleRequest($rawRequest);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $userDTO = $form->getData();
                $userDTO->setId($id);
                $command = $this->userService->getCommand($userDTO);
                $this->commandBus->dispatch($command);
                $this->addFlash('success','Your changes were saved!');
                return $this->redirectToRoute('admin_user', ['id' => $command->getId()]);
            }
        } catch (\Exception $e) {
            $this->addFlash('danger','Error while saving changes');
            $error = new FormError("There is an error: ".$e->getMessage());
            $form->addError($error);
        }

        return $this->render('admin/user/form.html.twig', [
            'form' => $form->createView(),
            'id' => $id
        ]);
    }

    /** 
     * @Route("/admin/users/list", name="admin_users") 
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */ 
    public function usersList(PaginatorInterface $paginator, Request $request): Response 
    {         
        $searchQuery = $request->query->getAlnum('search');

        return $this->render('admin/user/list.html.twig', [ 
            'pagination' => $paginator->paginate(
             $this->repository->listAllUsers($searchQuery), $request->query->getInt('page', 1),10),
             'searchQuery'  => $searchQuery
        ]); 
    }

    /**
     * 
     * @Route("/admin/delete/user/{id}", name="admin_delete_user", methods={"GET"})
     * @param string $id
     * @param Request $rawRequest
     * @return Response|RedirectResponse
     */
    public function deleteUser(string $id, Request $rawRequest): Response
    {
        try {
            $userId = Uuid::fromString($id);
            $command = new DeleteUserCommand($userId);
            $this->commandBus->dispatch($command);
            $this->addFlash('success','User deleted!');
        } catch (\Exception $e) {
            $this->addFlash('danger','Error while deleting user: '.$e->getMessage());
        }

        return $this->redirectToRoute('admin_users');
    }

    /**
     * @param string|null $id
     * @return FormInterface
     */
    private function getForm($id = null): FormInterface
    {
        if (empty($id)) {
            return $this->createForm(\App\Form\Type\UserType::class);
        }

        $user = $this->repository->getUser(Uuid::fromString($id));
        $userDTO = $this->userService->fillUserDTO($user);
        return $this->createForm(\App\Form\Type\UserType::class, $userDTO);
    }
}
