<?php

namespace App\Controller\Admin;

use App\Command\DeleteCategoryCommand;
use App\Repository\CategoryRepository;
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
use App\Service\CategoryService;
use Symfony\Component\Form\FormInterface;
use Swagger\Annotations as SWG;

class AdminCategoryController extends AbstractController
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
     * @var CategoryRepository
     */
    private $repository;
    /**
     * @var CategoryService
     */
    private $categoryService;

    /**
     * @param MessageBusInterface $commandBus
     * @param LoggerInterface $logger
     * @param CategoryRepository $repository
     * @param CategoryService $categoryService
     */
    public function __construct(
        MessageBusInterface $commandBus,
        LoggerInterface $logger,
        CategoryRepository $repository,
        CategoryService $categoryService
    )
    {
        $this->commandBus = $commandBus;
        $this->logger = $logger;
        $this->repository = $repository;
        $this->categoryService = $categoryService;
    }

    /**
     * 
     * @Route("/admin/category/{id}", name="admin_category", defaults={"id"=null}, methods={"GET","POST"})
     * @param string|null $id
     * @param Request $rawRequest
     * @return Response|RedirectResponse
     */
    public function createOrEditCategory(?string $id, Request $rawRequest): Response
    {
        $form = $this->getForm($id);
        $form->handleRequest($rawRequest);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $categoryDTO = $form->getData();
                $categoryDTO->setId($id);
                $command = $this->categoryService->getCommand($categoryDTO);
                $this->commandBus->dispatch($command);
                $this->addFlash('success','Your changes were saved!');
                return $this->redirectToRoute('admin_category', ['id' => $command->getId()]);
            }
        } catch (\Exception $e) {
            $this->addFlash('danger','Error while saving changes');
            $error = new FormError("There is an error: ".$e->getMessage());
            $form->addError($error);
        }

        return $this->render('category/form.html.twig', [
            'form' => $form->createView(),
            'id' => $id,
        ]);
    }

    /** 
     * @Route("/admin/categories/list", name="admin_categories", methods={"GET"}) 
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */ 
    public function categoryList(PaginatorInterface $paginator, Request $request): Response
    {         
        return $this->render('category/list.html.twig', [ 
            'pagination' => $paginator->paginate(
             $this->repository->listAllCategories(), $request->query->getInt('page', 1),10) 
        ]); 
    }

    /**
     * 
     * @Route("/admin/delete/category/{id}", name="admin_delete_category", methods={"GET"})
     * @param string $id
     * @param Request $rawRequest
     * @return Response|RedirectResponse
     */
    public function deleteCategory(string $id, Request $rawRequest): Response
    {
        try {
            $categoryId = Uuid::fromString($id);
            $command = new DeleteCategoryCommand($categoryId);
            $this->commandBus->dispatch($command);
            $this->addFlash('success','Category deleted!');
        } catch (\Exception $e) {
            $this->addFlash('danger','Error while deleting category: '.$e->getMessage());
        }

        return $this->redirectToRoute('admin_categories');
    }

    /** 
     * @Route("/api/categories/autocomplete", name="admin_categories_autocomplete", methods={"GET"}) 
     *
     * @SWG\Tag(name="Autocomplete")
     * @SWG\Get(
     *     @SWG\Parameter(name="q", in="path", type="string", description="Search query")
     * )
     * @SWG\Response(
     *     response="200",
     *     description="Matched categories"
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

        $categories = $this->repository->autocompleteItems($searchQuery);

        foreach ($categories as $category) {
            $result[] = ['id' => $category->getId()->toString(), 'text'=>$category->getName()];
        }

        return $this->json(['categories' => $result], 200);
    }

    /**
     * @param string|null $id
     * @return FormInterface
     */
    private function getForm($id = null): FormInterface
    {
        if (empty($id)) {
            return $this->createForm(\App\Form\Type\CategoryType::class);
        }

        $category = $this->repository->getCategory(Uuid::fromString($id));
        $categoryDTO = $this->categoryService->fillCategoryDTO($category);
        return $this->createForm(\App\Form\Type\CategoryType::class, $categoryDTO);
    }
}
