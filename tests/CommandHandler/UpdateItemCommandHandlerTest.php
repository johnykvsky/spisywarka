<?php

namespace App\Tests\CommandHandler;

use App\Command\UpdateItemCommand;
use App\CommandHandler\UpdateItemCommandHandler;
use App\CommandHandler\Exception\ItemNotUpdatedException;
use App\Entity\Item;
use App\Repository\ItemRepository;
use App\Repository\CollectionRepository;
use App\Repository\CategoryRepository;
use App\Repository\ItemCollectionRepository;
use App\Tests\Mothers\ItemMother;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Tests\Fixtures\ChildDummyMessage;
use Psr\Log\LoggerInterface;

class UpdateItemCommandHandlerTest extends TestCase
{
    /**
     * @throws ItemNotUpdatedException
     * @throws \Assert\AssertionFailedException
     */
    public function test_item_created(): void
    {
        $itemMock = ItemMother::random();

        $repository = $this->createMock(ItemRepository::class);
        $repository->method('getItem')->willReturn($itemMock);
        $categoryRepository = $this->createMock(CategoryRepository::class);
        $collectionRepository = $this->createMock(CollectionRepository::class);
        $itemCollectionRepository = $this->createMock(ItemCollectionRepository::class);
        $categoryRepository->method('getCategory')->willReturn($itemMock->getCategory());

        $repository
            ->expects(self::once())
            ->method('save')
            ->with(
                self::callback(
                    function (Item $item) use ($itemMock) {
                        self::assertSame($itemMock->getId(), $item->getId());
                        self::assertSame($itemMock->getName(), $item->getName());
                        self::assertSame($itemMock->getCategory()->getId()->toString(), $item->getCategory()->getId()->toString());   
                        self::assertSame($itemMock->getYear(), $item->getYear());
                        self::assertSame($itemMock->getFormat(), $item->getFormat());
                        self::assertSame($itemMock->getAuthor(), $item->getAuthor());
                        self::assertSame($itemMock->getPublisher(), $item->getPublisher());
                        self::assertSame($itemMock->getDescription(), $item->getDescription());
                        self::assertSame($itemMock->getStore(), $item->getStore());
                        self::assertSame($itemMock->getUrl(), $item->getUrl());

                        return true;
                    }
                )
            );

        $command = new UpdateItemCommand(
            $itemMock->getId(), $itemMock->getName(), $itemMock->getCategory()->getId(), $itemMock->getYear(), $itemMock->getFormat(),
            $itemMock->getAuthor(), $itemMock->getPublisher(), $itemMock->getDescription(),
            $itemMock->getStore(), $itemMock->getUrl(), null
            );
        
        $eventBus = $this->createMock(MessageBusInterface::class);
        //$eventBus->expects(self::once())->method('dispatch')->withAnyParameters();
        
        $logger = $this->createMock(LoggerInterface::class);
        
        $handler = new UpdateItemCommandHandler($eventBus, $repository, $logger, $categoryRepository, $itemCollectionRepository, $collectionRepository);
        
        $handler($command);
    }
}