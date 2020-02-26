<?php
namespace App\Service;

use App\Entity\Collection;
use App\DTO\CollectionDTO;
use App\Command\CommandInterface;
use App\Command\CreateCollectionCommand;
use App\Command\UpdateCollectionCommand;
use Ramsey\Uuid\Uuid;
use App\Traits\CommandInstanceTrait;

class CollectionService
{
    use CommandInstanceTrait;

    /**
     * @param Collection $collection
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
     * @param CollectionDTO $collectionDTO
     * @return CreateCollectionCommand|UpdateCollectionCommand
     */
    public function getCommand(CollectionDTO $collectionDTO):  CommandInterface
    {
        $command = $this->getCommandInstance($collectionDTO->getId(), 'Collection');
        return $command->newInstanceArgs([
            $collectionDTO->getId() ?? Uuid::uuid4(),
            $collectionDTO->getName(),
            $collectionDTO->getDescription()
        ]);
    }
}