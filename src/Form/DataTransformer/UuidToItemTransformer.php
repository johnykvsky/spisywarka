<?php
namespace App\Form\DataTransformer;

use App\Entity\Item;
use App\Repository\Exception\ItemNotFoundException;
use App\Repository\ItemRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\InvalidUuidStringException;

class UuidToItemTransformer implements DataTransformerInterface
{
    private $itemRepository;

    public function __construct(ItemRepository $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    /**
     * Transforms an object (item) to a UuidInterface
     *
     * @param  Item|null $item
     * @return UuidInterface|string
     */
    public function reverseTransform($item)
    {
        if (null === $item) {
            return '';
        }

        return $item->getId();
    }

    /**
     * Transforms a UuidInterface to an object (Item).
     *
     * @param  string|null $itemId
     * @return Item|null
     * @throws TransformationFailedException if object (Item) is not found.
     */
    public function transform($itemId)
    {
        if (!$itemId) {
            return null;
        }

        try {
            $item = $this->itemRepository->getItem(Uuid::fromString($itemId));
        } catch (ItemNotFoundException $e) {
            throw new TransformationFailedException(sprintf('Item "%s" not found !', $itemId));
        } catch (InvalidUuidStringException $e) {
            throw new TransformationFailedException(sprintf('Invalid UUID "%s" !', $itemId));
        }

        if (null === $item) {
            throw new TransformationFailedException(sprintf('An item with id "%s" does not exist!', $itemId));
        }

        return $item;
    }
}