<?php

namespace App\Tests\CommandHandler;

use App\Command\AddItemToCategoryCommand;
use App\CommandHandler\AddItemToCategoryCommandHandler;
use App\Entity\Item;
use App\Entity\Category;
use App\Entity\ItemCategory;
use App\Repository\CategoryRepository;
use App\CommandHandler\Exception\ItemNotUpdatedException;
use App\Repository\ItemCategoryRepository;
use App\Repository\ItemRepository;
use App\Tests\Mothers\ItemMother;
use App\Tests\Mothers\CategoryMother;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Tests\Fixtures\ChildDummyMessage;
use Psr\Log\LoggerInterface;

class AddItemToCategoryCommandHandlerTest extends TestCase
{
    /**
     * @throws ItemNotUpdatedException
     * @throws \Assert\AssertionFailedException
     */
    public function test_add_item_to_category(): void
    {
        $itemMock = ItemMother::random();
        $categoryMock = CategoryMother::random();

        $repository = $this->createMock(ItemCategoryRepository::class);
        $categoryRepository = $this->createMock(CategoryRepository::class);
        $itemRepository = $this->createMock(ItemRepository::class);
        $itemRepository->method('getItem')->willReturn($itemMock);
        $categoryRepository->method('getCategory')->willReturn($categoryMock);

        $itemCategoryMock = new ItemCategory($itemMock, $categoryMock);

        $repository
            ->expects(self::once())
            ->method('save')
            ->with(
                self::callback(
                    function (ItemCategory $itemCategory) use ($itemCategoryMock) {
                        self::assertSame($itemCategoryMock->getItem()->getId()->toString(), $itemCategory->getItem()->getId()->toString());
                        self::assertSame($itemCategoryMock->getCategory()->getId()->toString(), $itemCategory->getCategory()->getId()->toString());
                        return true;
                    }
                )
            );

        $command = new AddItemToCategoryCommand(
            $itemMock->getId(), 
            $categoryMock->getId()
        );
        
        $logger = $this->createMock(LoggerInterface::class);
        $eventBus = $this->createMock(MessageBusInterface::class);
        
        $handler = new AddItemToCategoryCommandHandler($eventBus, $repository, $logger, $itemRepository, $categoryRepository);
        
        $handler($command);
    }
}