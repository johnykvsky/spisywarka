<?php

namespace App\Controller\Admin;

use App\Command\DeleteItemCommand;
use App\Repository\ItemRepository;
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
use App\Service\ItemService;
use Symfony\Component\Form\FormInterface;

class AdminItemController extends AbstractController
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
     * @var ItemRepository
     */
    private $repository;
    /**
     * @var ItemService
     */
    private $itemService;

    /**
     * @param MessageBusInterface $commandBus
     * @param LoggerInterface $logger
     * @param ItemRepository $repository
     * @param ItemService $itemService
     */
    public function __construct(
        MessageBusInterface $commandBus,
        LoggerInterface $logger,
        ItemRepository $repository,
        ItemService $itemService
    )
    {
        $this->commandBus = $commandBus;
        $this->logger = $logger;
        $this->repository = $repository;
        $this->itemService = $itemService;
    }

    /**
     * @Route("/admin/item/{id}", name="admin_item", defaults={"id"=null}, methods={"GET","POST"})
     * 
     * @param string|null $id
     * @param Request $rawRequest
     * @return Response|RedirectResponse
     */
    public function createOrEditItem(?string $id, Request $rawRequest): Response
    {
        $form = $this->getForm($id);
        $form->handleRequest($rawRequest);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $itemDTO = $form->getData();
                $itemDTO->setId($id);
                $command = $this->itemService->getCommand($itemDTO);
                $this->commandBus->dispatch($command);
                $this->addFlash('success','Your changes were saved!');
                return $this->redirectToRoute('admin_item', ['id' => $command->getId()]);
            }
        } catch (\Exception $e) {
            $this->addFlash('danger','Error while saving changes');
            $error = new FormError("There is an error: ".$e->getMessage());
            $form->addError($error);
        }

        return $this->render('item/form.html.twig', [
            'form' => $form->createView(),
            'id' => $id
        ]);
    }

    /** 
     * @Route("/admin/items/list", name="admin_items") 
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */ 
    public function itemsList(PaginatorInterface $paginator, Request $request): Response 
    {         
        $searchQuery = $request->query->getAlnum('search');

        return $this->render('item/list.html.twig', [ 
            'pagination' => $paginator->paginate(
             $this->repository->listAllItems($searchQuery), $request->query->getInt('page', 1),10),
             'searchQuery'  => $searchQuery
        ]); 
    }

    /**
     * 
     * @Route("/admin/delete/item/{id}", name="admin_delete_item", methods={"GET"})
     * @param string $id
     * @param Request $rawRequest
     * @return Response|RedirectResponse
     */
    public function deleteItem(string $id, Request $rawRequest): Response
    {
        try {
            $itemId = Uuid::fromString($id);
            $command = new DeleteItemCommand($itemId);
            $this->commandBus->dispatch($command);
            $this->addFlash('success','Item deleted!');
        } catch (\Exception $e) {
            $this->addFlash('danger','Error while deleting item: '.$e->getMessage());
        }

        return $this->redirectToRoute('admin_items');
    }

    /** 
     * @Route("/api/items/autocomplete", name="admin_items_autocomplete") 
     * @param Request $request
     * @return JsonResponse
     */ 
    public function autocompleteAction(Request $request): JsonResponse
    {
        $result = array();
        $searchQuery = $request->query->get('q');
        $searchQuery = urldecode($searchQuery);
        $searchQuery = preg_replace('/[^[:alnum:]]/', '', $searchQuery);

        $items = $this->repository->autocompleteItems($searchQuery);

        foreach ($items as $item) {
            $result[] = ['id' => $item->getId()->toString(), 'text'=>$item->getName()];
        }

        return $this->json(['items' => $result], 200);
    }

    /**
     * @param string|null $id
     * @return FormInterface
     */
    private function getForm($id = null): FormInterface
    {
        if (empty($id)) {
            return $this->createForm(\App\Form\Type\ItemType::class);
        }

        $item = $this->repository->getItem(Uuid::fromString($id));
        $itemDTO = $this->itemService->fillItemDTO($item);
        return $this->createForm(\App\Form\Type\ItemType::class, $itemDTO);
    }
}
