<?php

namespace App\Tests\CommandHandler;

use App\Command\CreateCollectionCommand;
use App\CommandHandler\CreateCollectionCommandHandler;
use App\CommandHandler\Exception\CollectionNotCreatedException;
use App\Entity\Collection;
use App\Repository\CollectionRepository;
use App\Tests\Mothers\CollectionMother;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\MessageBusInterface;
use Psr\Log\LoggerInterface;

class CreateCollectionCommandHandlerTest extends TestCase
{
    /**
     * @throws CollectionNotCreatedException
     * @throws \Assert\AssertionFailedException
     */
    public function test_collection_created(): void
    {
        $collectionyMock = CollectionMother::random();

        $repository = $this->createMock(CollectionRepository::class);
        $repository
            ->expects(self::once())
            ->method('save')
            ->with(
                self::callback(
                    function (Collection $collection) use ($collectionyMock) {
                        self::assertSame($collectionyMock->getId(), $collection->getId());
                        self::assertSame($collectionyMock->getName(), $collection->getName());
                        self::assertSame($collectionyMock->getDescription(), $collection->getDescription());

                        return true;
                    }
                )
            );

        $command = new CreateCollectionCommand(
            $collectionyMock->getId(), $collectionyMock->getName(), $collectionyMock->getDescription()
        );
        
        $eventBus = $this->createMock(MessageBusInterface::class);
        //$eventBus->expects(self::once())->method('dispatch')->withAnyParameters();
        
        $logger = $this->createMock(LoggerInterface::class);
        
        $handler = new CreateCollectionCommandHandler($eventBus, $repository, $logger);
        
        $handler($command);
    }
}