<?php

namespace App\Controller\Admin;

use App\Repository\ItemRepository;
use App\Repository\CollectionRepository;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Command\RemoveItemFromCollectionCommand;

class AdminCollectionItemsController extends AbstractController
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
     * @var CollectionRepository
     */
    private $collectionRepository;

    /**
     * @param MessageBusInterface $commandBus
     * @param LoggerInterface $logger
     * @param ItemRepository $repository
     * @param CollectionRepository $collectionRepository
     */
    public function __construct(
        MessageBusInterface $commandBus,
        LoggerInterface $logger,
        ItemRepository $repository,
        CollectionRepository $collectionRepository
    )
    {
        $this->commandBus = $commandBus;
        $this->logger = $logger;
        $this->repository = $repository;
        $this->collectionRepository = $collectionRepository;
    }

    /** 
    * @Route("/admin/collection-items/{id}", name="admin_collection_items", methods={"GET"})
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */ 
    public function collectionItemsList(string $id, PaginatorInterface $paginator, Request $request): Response
    {         
        $collectionId = Uuid::fromString($id);
        $collection = $this->collectionRepository->getCollection($collectionId);
        return $this->render('collection/items_list.html.twig', [ 
            'collection' => $collection,
            'pagination' => $paginator->paginate(
             $this->repository->listAllItemsInCollection($collectionId), $request->query->getInt('page', 1),10) 
        ]); 
    }

    /**
     * 
     * @Route("/admin/delete/item-collection/{itemId}/{collectionId}", name="admin_delete_collection_item", methods={"GET"})
     * @param string $itemId
     * @param string $collectionId
     * @param Request $rawRequest
     * @return Response|RedirectResponse
     */
    public function deleteCollection(string $itemId, string $collectionId, Request $rawRequest): Response
    {
        try {
            $itemId = Uuid::fromString($itemId);
            $collectionId = Uuid::fromString($collectionId);
            $command = new RemoveItemFromCollectionCommand($itemId, $collectionId);
            $this->commandBus->dispatch($command);
            $this->addFlash('success','Item removed from collection deleted!');
        } catch (\Exception $e) {
            $this->addFlash('danger','Error while removing item from collection: '.$e->getMessage());
        }

        return $this->redirectToRoute('admin_collections');
    }
}
