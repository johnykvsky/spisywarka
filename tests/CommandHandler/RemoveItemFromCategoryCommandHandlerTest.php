<?php

namespace App\Tests\CommandHandler;

use App\Command\RemoveItemFromCategoryCommand;
use App\CommandHandler\RemoveItemFromCategoryCommandHandler;
use App\CommandHandler\Exception\ItemNotUpdatedException;
use App\Entity\ItemCategory;
use App\Repository\ItemCategoryRepository;
use App\Repository\ItemRepository;
use App\Repository\CategoryRepository;
use App\Tests\Mothers\CategoryMother;
use App\Tests\Mothers\ItemCategoryMother;
use App\Tests\Mothers\ItemMother;
use Assert\AssertionFailedException;
use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Tests\Fixtures\ChildDummyMessage;
use Psr\Log\LoggerInterface;

Class RemoveItemFromCategoryCommandHandlerTest extends TestCase
{
    /**
     * @throws AssertionFailedException
     * @throws Exception
     */
    public function test_remove_item_from_category(): void
    {
        $categoryMock = CategoryMother::random();
        $itemMock = ItemMother::random();
        $itemCategoryMock = ItemCategoryMother::given($itemMock, $categoryMock);
        $itemMock->addCategory($itemCategoryMock);

        $itemCategoryMock = new ItemCategory($itemMock, $categoryMock);

        $repository = $this->createMock(ItemCategoryRepository::class);
        $categoryRepository = $this->createMock(CategoryRepository::class);
        $itemRepository = $this->createMock(ItemRepository::class);

        $repository->method('findItemCategory')->with($itemMock, $categoryMock)->willReturn($itemCategoryMock);
        $categoryRepository->method('getCategory')->with($categoryMock->getId())->willReturn($categoryMock);
        $itemRepository->method('getItem')->with($itemMock->getId())->willReturn($itemMock);
        
        $repository->expects(self::once())
            ->method('delete')
            ->with(self::callback(
                static function (ItemCategory $itemCategory) use ($itemCategoryMock) {
                    self::assertSame($itemCategoryMock->getItem()->getId(), $itemCategory->getItem()->getId());
                    self::assertSame($itemCategoryMock->getCategory()->getId(), $itemCategory->getCategory()->getId());
                    return true;
                }
            ));

        $eventBus = $this->createMock(MessageBusInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $command = new RemoveItemFromCategoryCommand($itemMock->getId(), $categoryMock->getId());
        $handler = new RemoveItemFromCategoryCommandHandler($eventBus, $repository, $logger, $itemRepository, $categoryRepository);
        $handler($command);
    }
}