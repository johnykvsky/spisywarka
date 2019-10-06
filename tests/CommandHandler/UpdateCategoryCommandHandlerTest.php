<?php

namespace App\Tests\CommandHandler;

use App\Command\UpdateCategoryCommand;
use App\CommandHandler\UpdateCategoryCommandHandler;
use App\CommandHandler\Exception\CategoryNotUpdatedException;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Tests\Mothers\CategoryMother;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\MessageBusInterface;
use Psr\Log\LoggerInterface;

class UpdateCategoryCommandHandlerTest extends TestCase
{
    /**
     * @throws CategoryNotUpdatedException
     * @throws \Assert\AssertionFailedException
     */
    public function test_category_updated(): void
    {
        $categoryMock = CategoryMother::random();

        $repository = $this->createMock(CategoryRepository::class);
        $repository->method('getCategory')->willReturn($categoryMock);
        $repository
            ->expects(self::once())
            ->method('save')
            ->with(
                self::callback(
                    function (Category $category) use ($categoryMock) {
                        self::assertSame($categoryMock->getId()->toString(), $category->getId()->toString());
                        self::assertSame($categoryMock->getName(), $category->getName());
                        self::assertSame($categoryMock->getDescription(), $category->getDescription());

                        return true;
                    }
                )
            );

        $command = new UpdateCategoryCommand(
            $categoryMock->getId(), $categoryMock->getName(), $categoryMock->getDescription()
        );
        
        $eventBus = $this->createMock(MessageBusInterface::class);
        //$eventBus->expects(self::once())->method('dispatch')->withAnyParameters();
        
        $logger = $this->createMock(LoggerInterface::class);
        
        $handler = new UpdateCategoryCommandHandler($eventBus, $repository, $logger);
        
        $handler($command);
    }
}