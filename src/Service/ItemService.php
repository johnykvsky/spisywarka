<?php
namespace App\Service;

use App\Entity\Item;
use App\DTO\ItemDTO;
use App\Command\CommandInterface;
use App\Command\CreateItemCommand;
use App\Command\UpdateItemCommand;
use Ramsey\Uuid\Uuid;

class ItemService
{
    /**
     * @return ItemDTO
     */
    public function fillItemDTO(Item $item): ItemDTO
    {
        return new ItemDTO(
            $item->getId()->toString(),
            $item->getName(),
            $item->getYear(),
            $item->getFormat(),
            $item->getAuthor(),
            $item->getPublisher(),
            $item->getDescription(),
            $item->getStore(),
            $item->getUrl(),
            $item->getCategories()->toArray(),
            $item->getCollections()->toArray()
        );
    }

    /**
     * @return CreateItemCommand|UpdateItemCommand
     */
    public function getCommand(ItemDTO $itemDTO):  CommandInterface
    {
        if (empty($itemDTO->getId())) {
            return new CreateItemCommand(
                Uuid::uuid4(),
                $itemDTO->getName(),
                $itemDTO->getYear(),
                $itemDTO->getFormat(),
                $itemDTO->getAuthor(),
                $itemDTO->getPublisher(),
                $itemDTO->getDescription(),
                $itemDTO->getStore(),
                $itemDTO->getUrl(),
                $itemDTO->getCategories(),
                $itemDTO->getCollections()
            );
        } else {
            return new UpdateItemCommand(
                $itemDTO->getId(),
                $itemDTO->getName(),
                $itemDTO->getYear(),
                $itemDTO->getFormat(),
                $itemDTO->getAuthor(),
                $itemDTO->getPublisher(),
                $itemDTO->getDescription(),
                $itemDTO->getStore(),
                $itemDTO->getUrl(),
                $itemDTO->getCategories(),
                $itemDTO->getCollections()
            );
        }
    }
}