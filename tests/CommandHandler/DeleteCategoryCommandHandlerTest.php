<?php

namespace App\Tests\CommandHandler;

use App\Command\DeleteCategoryCommand;
use App\CommandHandler\DeleteCategoryCommandHandler;
use App\CommandHandler\Exception\CategoryNotDeletedException;
use App\Entity\Category;
use App\Repository\Exception\CategoryNotFoundException;
use App\Repository\CategoryRepository;
use App\Tests\Mothers\CategoryMother;
use Assert\AssertionFailedException;
use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Psr\Log\LoggerInterface;

Class DeleteCategoryCommandHandlerTest extends TestCase
{
    /**
     * @throws AssertionFailedException
     * @throws Exception
     */
    public function test_delete_category(): void
    {
        $categoryMock = CategoryMother::random();
        $id = $categoryMock->getId();

        $repository = $this->createMock(CategoryRepository::class);
        $repository->method('getCategory')->with($id)->willReturn($categoryMock);
        $repository->expects(self::once())
            ->method('delete')
            ->with(self::callback(
                static function (Category $category) use ($id) {
                    self::assertSame($id, $category->getId());
                    return true;
                }
            ));

        $eventBus = $this->createMock(MessageBusInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $command = new DeleteCategoryCommand($id);
        $handler = new DeleteCategoryCommandHandler($eventBus, $repository, $logger);
        $handler($command);
    }

    /**
     * @throws CategoryNotFoundException
     * @throws CategoryNotDeletedException
     * @throws Exception
     */
    public function test_throws_CategoryNotFoundException_when_invalid_uuid(): void
    {
        $this->expectException(CategoryNotDeletedException::class);

        $id = Uuid::uuid4();
        $repository = $this->createMock(CategoryRepository::class);
        $repository->method('getCategory')->with($id)->willThrowException(new CategoryNotFoundException());
        $eventBus = $this->createMock(MessageBusInterface::class);
        $command = new DeleteCategoryCommand($id);
        $logger = $this->createMock(LoggerInterface::class);
        $handler = new DeleteCategoryCommandHandler($eventBus, $repository, $logger);
        $handler($command);
    }
}