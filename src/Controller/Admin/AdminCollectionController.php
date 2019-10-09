<?php

namespace App\Controller\Admin;

use App\Command\DeleteCollectionCommand;
use App\Repository\CollectionRepository;
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
use App\Service\CollectionService;
use Symfony\Component\Form\FormInterface;
use Swagger\Annotations as SWG;
use App\Traits\RequestQueryTrait;

class AdminCollectionController extends AbstractController
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
     * @var CollectionRepository
     */
    private $repository;
    /**
     * @var CollectionService
     */
    private $collectionService;

    /**
     * @param MessageBusInterface $commandBus
     * @param LoggerInterface $logger
     * @param CollectionRepository $repository
     * @param CollectionService $collectionService
     */
    public function __construct(
        MessageBusInterface $commandBus,
        LoggerInterface $logger,
        CollectionRepository $repository,
        CollectionService $collectionService
    )
    {
        $this->commandBus = $commandBus;
        $this->logger = $logger;
        $this->repository = $repository;
        $this->collectionService = $collectionService;
    }

    /**
     * 
     * @Route("/admin/collection/{id}", name="admin_collection", defaults={"id"=null}, methods={"GET","POST"})
     * @param string|null $id
     * @param Request $rawRequest
     * @return Response|RedirectResponse
     */
    public function createOrEditCollection(?string $id, Request $rawRequest): Response
    {
        $form = $this->getForm($id);
        $form->handleRequest($rawRequest);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $collectionDTO = $form->getData();
                $collectionDTO->setId($id);
                $command = $this->collectionService->getCommand($collectionDTO);
                $this->commandBus->dispatch($command);
                $this->addFlash('success','Your changes were saved!');
                return $this->redirectToRoute('admin_collection', ['id' => $command->getId()]);
            }
        } catch (\Exception $e) {
            $this->addFlash('danger','Error while saving changes');
            $error = new FormError("There is an error: ".$e->getMessage());
            $form->addError($error);
        }

        return $this->render('collection/form.html.twig', [
            'form' => $form->createView(),
            'id' => $id
        ]);
    }

    /** 
     * @Route("/admin/collections/list", name="admin_collections") 
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */ 
    public function collectionList(PaginatorInterface $paginator, Request $request): Response
    {         
        return $this->render('collection/list.html.twig', [ 
            'pagination' => $paginator->paginate(
             $this->repository->listAllCollections(), $request->query->getInt('page', 1),10) 
        ]); 
    }

    /**
     * 
     * @Route("/admin/delete/collection/{id}", name="admin_delete_collection", methods={"GET"})
     * @param string $id
     * @param Request $rawRequest
     * @return Response|RedirectResponse
     */
    public function deleteCollection(string $id, Request $rawRequest): Response
    {
        try {
            $collectionId = Uuid::fromString($id);
            $command = new DeleteCollectionCommand($collectionId);
            $this->commandBus->dispatch($command);
            $this->addFlash('success','Collection deleted!');
        } catch (\Exception $e) {
            $this->addFlash('danger','Error while deleting collection: '.$e->getMessage());
        }

        return $this->redirectToRoute('admin_collections');
    }

    /** 
     * @Route("/api/collections/autocomplete", name="admin_collections_autocomplete", methods={"GET"}) 
     *
     * @SWG\Tag(name="Autocomplete")
     * @SWG\Get(
     *     @SWG\Parameter(name="q", in="path", type="string", description="Search query")
     * )
     * @SWG\Response(
     *     response="200",
     *     description="Matched collections"
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */ 
    public function autocompleteAction(Request $request): JsonResponse
    {
        $result = array();
        $q = $request->query->get('q');
        $searchQuery = filter_var(urldecode($q), FILTER_SANITIZE_STRING);

        $collections = $this->repository->autocomplete($this->getFromRequest($request, 'q', true));

        foreach ($collections as $collection) {
            $result[] = ['id' => $collection->getId()->toString(), 'text'=>$collection->getName()];
        }

        return $this->json(['collections' => $result], 200);
    }

    /**
     * @param string|null $id
     * @return FormInterface
     */
    private function getForm($id = null): FormInterface
    {
        if (empty($id)) {
            return $this->createForm(\App\Form\Type\CollectionType::class);
        }

        $collection = $this->repository->getCollection(Uuid::fromString($id));
        $collectionDTO = $this->collectionService->fillCollectionDTO($collection);
        return $this->createForm(\App\Form\Type\CollectionType::class, $collectionDTO);
    }
}
