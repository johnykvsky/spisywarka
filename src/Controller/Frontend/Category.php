<?php

namespace App\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ItemRepository;
use App\Repository\CategoryRepository;
use Ramsey\Uuid\Uuid;

class Category extends AbstractController
{
    /**
     * @var ItemRepository
     */
    private $repository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @param ItemRepository $repository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        ItemRepository $repository,
        CategoryRepository $categoryRepository
    )
    {
        $this->repository = $repository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/category/{id}/{slug}", name="category", methods={"GET"})
     * @param string $id
     * @param string $slug
     * @return Response
     */
    public function category(string $id, string $slug): Response
    {
        try {
            $uuid = Uuid::fromString($id);
            $category = $this->categoryRepository->getCategory($uuid);
            $items = $this->repository->getItemsInCategory($uuid);
        } catch (\Exception $e) {
                $error = $e->getMessage();
        }

        return $this->render('frontend/category.html.twig', [
            'items' => $items ?? null,
            'category' => $category,
            'error' => $error ?? null
        ]);
    }
}
