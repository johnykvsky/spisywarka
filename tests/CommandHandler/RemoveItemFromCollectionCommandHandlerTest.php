<?php

namespace App\Tests\CommandHandler;

use App\Command\RemoveItemFromCollectionCommand;
use App\CommandHandler\RemoveItemFromCollectionCommandHandler;
use App\CommandHandler\Exception\ItemNotUpdatedException;
use App\Entity\ItemCollection;
use App\Repository\ItemCollectionRepository;
use App\Repository\ItemRepository;
use App\Repository\CollectionRepository;
use App\Tests\Mothers\CollectionMother;
use App\Tests\Mothers\ItemCollectionMother;
use App\Tests\Mothers\ItemMother;
use Assert\AssertionFailedException;
use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Tests\Fixtures\ChildDummyMessage;
use Psr\Log\LoggerInterface;

Class RemoveItemFromCollectionCommandHandlerTest extends TestCase
{
    /**
     * @throws AssertionFailedException
     * @throws Exception
     */
    public function test_remove_item_from_collection(): void
    {
        $collectionMock = CollectionMother::random();
        $itemMock = ItemMother::random();
        $itemCollectionMock = ItemCollectionMother::given($itemMock, $collectionMock);
        $itemMock->addCollection($itemCollectionMock);

        $itemCollectionMock = new ItemCollection($itemMock, $collectionMock);

        $repository = $this->createMock(ItemCollectionRepository::class);
        $collectionRepository = $this->createMock(CollectionRepository::class);
        $itemRepository = $this->createMock(ItemRepository::class);

        $repository->method('findItemCollection')->with($itemMock, $collectionMock)->willReturn($itemCollectionMock);
        $collectionRepository->method('getCollection')->with($collectionMock->getId())->willReturn($collectionMock);
        $itemRepository->method('getItem')->with($itemMock->getId())->willReturn($itemMock);
        
        $repository->expects(self::once())
            ->method('delete')
            ->with(self::callback(
                static function (ItemCollection $itemCollection) use ($itemCollectionMock) {
                    self::assertSame($itemCollectionMock->getItem()->getId(), $itemCollection->getItem()->getId());
                    self::assertSame($itemCollectionMock->getCollection()->getId(), $itemCollection->getCollection()->getId());
                    return true;
                }
            ));

        $eventBus = $this->createMock(MessageBusInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $command = new RemoveItemFromCollectionCommand($itemMock->getId(), $collectionMock->getId());
        $handler = new RemoveItemFromCollectionCommandHandler($eventBus, $repository, $logger, $itemRepository, $collectionRepository);
        $handler($command);
    }
}