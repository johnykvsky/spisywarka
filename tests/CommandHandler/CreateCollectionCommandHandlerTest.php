<?php

namespace App\Tests\CommandHandler;

use App\Command\CreateCollectionCommand;
use App\CommandHandler\CreateCollectionCommandHandler;
use App\CommandHandler\Exception\CollectionNotCreatedException;
use App\Entity\Collection;
use App\Repository\CollectionRepository;
use App\Tests\Mothers\CollectionMother;
use App\Tests\Mothers\UserMother;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\MessageBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

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

        $user = UserMother::random();
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $command = new CreateCollectionCommand(
            $collectionyMock->getId(), $collectionyMock->getName(), $collectionyMock->getDescription(), $user->getId()
        );
        
        $eventBus = $this->createMock(MessageBusInterface::class);
        //$eventBus->expects(self::once())->method('dispatch')->withAnyParameters();
        
        $logger = $this->createMock(LoggerInterface::class);
        
        $handler = new CreateCollectionCommandHandler($eventBus, $repository, $logger, $security);
        
        $handler($command);
    }
}