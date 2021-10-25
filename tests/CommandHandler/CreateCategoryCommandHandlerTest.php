<?php

namespace App\Tests\CommandHandler;

use App\Command\CreateCategoryCommand;
use App\CommandHandler\CreateCategoryCommandHandler;
use App\CommandHandler\Exception\CategoryNotCreatedException;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Tests\Mothers\CategoryMother;
use App\Tests\Mothers\UserMother;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\MessageBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

class CreateCategoryCommandHandlerTest extends TestCase
{
    /**
     * @throws CategoryNotCreatedException
     * @throws \Assert\AssertionFailedException
     */
    public function test_category_created(): void
    {
        $categoryMock = CategoryMother::random();

        $repository = $this->createMock(CategoryRepository::class);
        $repository
            ->expects(self::once())
            ->method('save')
            ->with(
                self::callback(
                    function (Category $category) use ($categoryMock) {
                        self::assertSame($categoryMock->getId(), $category->getId());
                        self::assertSame($categoryMock->getName(), $category->getName());
                        self::assertSame($categoryMock->getDescription(), $category->getDescription());

                        return true;
                    }
                )
            );

        $user = UserMother::random();
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $command = new CreateCategoryCommand(
            $categoryMock->getId(), $categoryMock->getName(), $categoryMock->getDescription(), $user->getId()
        );
        
        $eventBus = $this->createMock(MessageBusInterface::class);
        //$eventBus->expects(self::once())->method('dispatch')->withAnyParameters();
        
        $logger = $this->createMock(LoggerInterface::class);
        
        $handler = new CreateCategoryCommandHandler($eventBus, $repository, $logger, $security);
        
        $handler($command);
    }
}