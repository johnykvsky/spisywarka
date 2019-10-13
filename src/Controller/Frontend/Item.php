<?php

namespace App\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ItemRepository;
use Ramsey\Uuid\Uuid;

class Item extends AbstractController
{
    /**
     * @var ItemRepository
     */
    private $repository;

    /**
     * @param ItemRepository $repository
     */
    public function __construct(
        ItemRepository $repository
    )
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/item/{id}/{slug}", name="item", methods={"GET"})
     * @param string $id
     * @param string $slug
     * @return Response
     */
    public function item(string $id, string $slug): Response
    {
        try {
            $uuid = Uuid::fromString($id);
            $item = $this->repository->getItem($uuid);
        } catch (\Exception $e) {
                $error = $e->getMessage();
        }

        return $this->render('frontend/item.html.twig', [
            'item' => $item ?? null,
            'error' => $error ?? null
        ]);
    }
}
