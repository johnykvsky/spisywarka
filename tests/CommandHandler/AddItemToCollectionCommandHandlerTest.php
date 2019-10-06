<?php

namespace App\Tests\CommandHandler;

use App\Command\AddItemToCollectionCommand;
use App\CommandHandler\AddItemToCollectionCommandHandler;
use App\Entity\Item;
use App\Entity\Collection;
use App\Entity\ItemCollection;
use App\Repository\CollectionRepository;
use App\CommandHandler\Exception\ItemNotUpdatedException;
use App\Repository\ItemCollectionRepository;
use App\Repository\ItemRepository;
use App\Tests\Mothers\ItemMother;
use App\Tests\Mothers\CollectionMother;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Tests\Fixtures\ChildDummyMessage;
use Psr\Log\LoggerInterface;

class AddItemToCollectionCommandHandlerTest extends TestCase
{
    /**
     * @throws ItemNotUpdatedException
     * @throws \Assert\AssertionFailedException
     */
    public function test_add_item_to_collection(): void
    {
        $itemMock = ItemMother::random();
        $collectionMock = CollectionMother::random();

        $repository = $this->createMock(ItemCollectionRepository::class);
        $collectionRepository = $this->createMock(CollectionRepository::class);
        $itemRepository = $this->createMock(ItemRepository::class);
        $itemRepository->method('getItem')->willReturn($itemMock);
        $collectionRepository->method('getCollection')->willReturn($collectionMock);

        $itemCollectionMock = new ItemCollection($itemMock, $collectionMock);

        $repository
            ->expects(self::once())
            ->method('save')
            ->with(
                self::callback(
                    function (ItemCollection $itemCollection) use ($itemCollectionMock) {
                        self::assertSame($itemCollectionMock->getItem()->getId()->toString(), $itemCollection->getItem()->getId()->toString());
                        self::assertSame($itemCollectionMock->getCollection()->getId()->toString(), $itemCollection->getCollection()->getId()->toString());
                        return true;
                    }
                )
            );

        $command = new AddItemToCollectionCommand(
            $itemMock->getId(), 
            $collectionMock->getId()
        );
        
        $logger = $this->createMock(LoggerInterface::class);
        $eventBus = $this->createMock(MessageBusInterface::class);
        
        $handler = new AddItemToCollectionCommandHandler($eventBus, $repository, $logger, $itemRepository, $collectionRepository);
        
        $handler($command);
    }
}