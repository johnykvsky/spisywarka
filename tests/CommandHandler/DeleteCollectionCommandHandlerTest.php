<?php

namespace App\Tests\CommandHandler;

use App\Command\DeleteCollectionCommand;
use App\CommandHandler\DeleteCollectionCommandHandler;
use App\CommandHandler\Exception\CollectionNotDeletedException;
use App\Entity\Collection;
use App\Repository\Exception\CollectionNotFoundException;
use App\Repository\CollectionRepository;
use App\Tests\Mothers\CollectionMother;
use Assert\AssertionFailedException;
use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Tests\Fixtures\ChildDummyMessage;
use Psr\Log\LoggerInterface;

Class DeleteCollectionCommandHandlerTest extends TestCase
{
    /**
     * @throws AssertionFailedException
     * @throws Exception
     */
    public function test_delete_collection(): void
    {
        $collectionMock = CollectionMother::random();
        $id = $collectionMock->getId();

        $repository = $this->createMock(CollectionRepository::class);
        $repository->method('getCollection')->with($id)->willReturn($collectionMock);
        $repository->expects(self::once())
            ->method('delete')
            ->with(self::callback(
                static function (Collection $collection) use ($id) {
                    self::assertSame($id, $collection->getId());
                    return true;
                }
            ));

        $eventBus = $this->createMock(MessageBusInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $command = new DeleteCollectionCommand($id);
        $handler = new DeleteCollectionCommandHandler($eventBus, $repository, $logger);
        $handler($command);
    }

    /**
     * @throws CollectionNotFoundException
     * @throws CollectionNotDeletedException
     * @throws Exception
     */
    public function test_throws_CollectionNotFoundException_when_invalid_uuid(): void
    {
        $this->expectException(CollectionNotDeletedException::class);

        $id = Uuid::uuid4();
        $repository = $this->createMock(CollectionRepository::class);
        $repository->method('getCollection')->with($id)->willThrowException(new CollectionNotFoundException());
        $eventBus = $this->createMock(MessageBusInterface::class);
        $command = new DeleteCollectionCommand($id);
        $logger = $this->createMock(LoggerInterface::class);
        $handler = new DeleteCollectionCommandHandler($eventBus, $repository, $logger);
        $handler($command);
    }
}