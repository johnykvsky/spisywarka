<?php
namespace App\Service;

use App\Entity\Collection;
use App\DTO\CollectionDTO;
use App\Command\CommandInterface;
use App\Command\CreateCollectionCommand;
use App\Command\UpdateCollectionCommand;
use Ramsey\Uuid\Uuid;

class CollectionService
{
    /**
     * @return CollectionDTO
     */
    public function fillCollectionDTO(Collection $collection): CollectionDTO
    {
        return new CollectionDTO(
            $collection->getId()->toString(),
            $collection->getName(),
            $collection->getDescription()
        );
    }

    /**
     * @return CreateCollectionCommand|UpdateCollectionCommand
     */
    public function getCommand(CollectionDTO $collectionDTO):  CommandInterface
    {
        if (empty($collectionDTO->getId())) {
            return new CreateCollectionCommand(
                Uuid::uuid4(),
                $collectionDTO->getName(),
                $collectionDTO->getDescription()
            );
        } else {
            return new UpdateCollectionCommand(
                $collectionDTO->getId(),
                $collectionDTO->getName(),
                $collectionDTO->getDescription()
            );
        }
    }
}