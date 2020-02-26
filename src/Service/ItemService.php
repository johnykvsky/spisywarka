<?php
namespace App\Service;

use App\Entity\Item;
use App\DTO\ItemDTO;
use App\Command\CommandInterface;
use App\Command\CreateItemCommand;
use App\Command\UpdateItemCommand;
use Ramsey\Uuid\Uuid;
use App\Traits\CommandInstanceTrait;

class ItemService
{
    use CommandInstanceTrait;

    /**
     * @param Item $item
     * @return ItemDTO
     */
    public function fillItemDTO(Item $item): ItemDTO
    {
        return new ItemDTO(
            $item->getId()->toString(),
            $item->getName(),
            $item->getCategory()->getId()->toString(),
            $item->getYear(),
            $item->getFormat(),
            $item->getAuthor(),
            $item->getPublisher(),
            $item->getDescription(),
            $item->getStore(),
            $item->getUrl(),
            $item->getCollections()->toArray()
        );
    }

    /**
     * @param ItemDTO $itemDTO
     * @return CreateItemCommand|UpdateItemCommand
     */
    public function getCommand(ItemDTO $itemDTO):  CommandInterface
    {
        $command = $this->getCommandInstance($itemDTO->getId(), 'Item');
        return $command->newInstanceArgs([
            $itemDTO->getId() ?? Uuid::uuid4(),
            $itemDTO->getName(),
            $itemDTO->getCategoryId(),
            $itemDTO->getYear(),
            $itemDTO->getFormat(),
            $itemDTO->getAuthor(),
            $itemDTO->getPublisher(),
            $itemDTO->getDescription(),
            $itemDTO->getStore(),
            $itemDTO->getUrl(),
            $itemDTO->getCollections()
        ]);
    }
}