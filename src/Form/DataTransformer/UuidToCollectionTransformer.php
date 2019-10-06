<?php
namespace App\Form\DataTransformer;

use App\Entity\Collection;
use App\Entity\ItemCollection;
use App\Repository\CollectionRepository;
use App\Repository\Exception\CollectionNotFoundException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Ramsey\Uuid\Exception\InvalidUuidStringException;

class UuidToCollectionTransformer implements DataTransformerInterface
{
    private $collectionRepository;

    public function __construct(CollectionRepository $collectionRepository)
    {
        $this->collectionRepository = $collectionRepository;
    }

    /**
     * Transforms an object (collections) to a UuidInterface
     *
     * @param  Collection[]|null $collections
     * @return array
     */
    public function reverseTransform($collections)
    {
        if (null == $collections) {
            return [];
        }

        $result = [];

        foreach ($collections as $collection) {
            $result[] = $collection->getId();
        }

        return $result;
    }

    /**
     * Transforms a UuidInterface to an object (Collection).
     *
     * @param  ItemCollection[]|null $itemCollections
     * @return Collection[]|null
     * @throws TransformationFailedException if object (Collection) is not found.
     */
    public function transform($itemCollections)
    {
        if (!$itemCollections) {
            return null;
        }

        $result = [];
        foreach ($itemCollections as $itemCollection) {
            $collectionId = $itemCollection->getCollection()->getId();
            try {
                $collection = $this->collectionRepository->find($collectionId);
            } catch (CollectionNotFoundException $e) {
                throw new TransformationFailedException(sprintf('Collection "%s" not found !', $collectionId->toString()));
            } catch (InvalidUuidStringException $e) {
                throw new TransformationFailedException(sprintf('Invalid UUID "%s" !', $collectionId->toString()));
            }

            if (null === $collection) {
                throw new TransformationFailedException(sprintf('An collection with id "%s" does not exist!', $collectionId->toString()));
            }

            $result[] = $collection;
        }

        return $result;
    }
}