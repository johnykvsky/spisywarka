<?php

namespace App\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ItemRepository;
use App\Repository\CollectionRepository;
use Ramsey\Uuid\Uuid;

class Collection extends AbstractController
{
    /**
     * @var ItemRepository
     */
    private $repository;
    /**
     * @var CollectionRepository
     */
    private $collectionRepository;

    /**
     * @param ItemRepository $repository
     * @param CollectionRepository $collectionRepository
     */
    public function __construct(
        ItemRepository $repository,
        CollectionRepository $collectionRepository
    )
    {
        $this->repository = $repository;
        $this->collectionRepository = $collectionRepository;
    }

    /**
     * @Route("/collection/{id}/{slug}", name="collection", methods={"GET"})
     * @param string $id
     * @param string $slug
     * @return Response
     */
    public function collection(string $id, string $slug): Response
    {
        try {
            $uuid = Uuid::fromString($id);
            $collection = $this->collectionRepository->getCollection($uuid);
            $items = $this->repository->getItemsInCollection($uuid);
        } catch (\Exception $e) {
                $error = $e->getMessage();
        }

        return $this->render('frontend/collection.html.twig', [
            'items' => $items ?? null,
            'collection' => $collection ?? null,
            'error' => $error ?? null
        ]);
    }
}
