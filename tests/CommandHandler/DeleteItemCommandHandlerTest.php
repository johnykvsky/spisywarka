<?php

namespace App\Tests\CommandHandler;

use App\Command\DeleteItemCommand;
use App\CommandHandler\DeleteItemCommandHandler;
use App\CommandHandler\Exception\ItemNotDeletedException;
use App\Entity\Item;
use App\Repository\Exception\ItemNotFoundException;
use App\Repository\ItemRepository;
use App\Tests\Mothers\ItemMother;
use Assert\AssertionFailedException;
use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Tests\Fixtures\ChildDummyMessage;
use Psr\Log\LoggerInterface;

Class DeleteItemCommandHandlerTest extends TestCase
{
    /**
     * @throws AssertionFailedException
     * @throws Exception
     */
    public function test_delete_item(): void
    {
        $itemMock = ItemMother::random();
        $id = $itemMock->getId();

        $repository = $this->createMock(ItemRepository::class);
        $repository->method('getItem')->with($id)->willReturn($itemMock);
        $repository->expects(self::once())
            ->method('delete')
            ->with(self::callback(
                static function (Item $item) use ($id) {
                    self::assertSame($id, $item->getId());
                    return true;
                }
            ));

        $eventBus = $this->createMock(MessageBusInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $command = new DeleteItemCommand($id);
        $handler = new DeleteItemCommandHandler($eventBus, $repository, $logger);
        $handler($command);
    }

    /**
     * @throws ItemNotFoundException
     * @throws ItemNotDeletedException
     * @throws Exception
     */
    public function test_throws_ItemNotFoundException_when_invalid_uuid(): void
    {
        $this->expectException(ItemNotDeletedException::class);

        $id = Uuid::uuid4();
        $repository = $this->createMock(ItemRepository::class);
        $repository->method('getItem')->with($id)->willThrowException(new ItemNotFoundException());
        $eventBus = $this->createMock(MessageBusInterface::class);
        $command = new DeleteItemCommand($id);
        $logger = $this->createMock(LoggerInterface::class);
        $handler = new DeleteItemCommandHandler($eventBus, $repository, $logger);
        $handler($command);
    }
}