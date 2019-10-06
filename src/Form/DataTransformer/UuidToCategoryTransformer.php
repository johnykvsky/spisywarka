<?php
namespace App\Form\DataTransformer;

use App\Entity\Category;
use App\Entity\ItemCategory;
use App\Repository\CategoryRepository;
use App\Repository\Exception\CategoryNotFoundException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Ramsey\Uuid\Exception\InvalidUuidStringException;

class UuidToCategoryTransformer implements DataTransformerInterface
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Transforms an object (categories) to a UuidInterface
     *
     * @param  Category[]|null $categories
     * @return array
     */
    public function reverseTransform($categories)
    {
        if (null == $categories) {
            return [];
        }

        $result = [];

        foreach ($categories as $category) {
            $result[] = $category->getId();
        }

        return $result;
    }

    /**
     * Transforms a UuidInterface to an object (Category).
     *
     * @param  ItemCategory[]|null $itemCategories
     * @return Category[]|null
     * @throws TransformationFailedException if object (Category) is not found.
     */
    public function transform($itemCategories)
    {
        if (!$itemCategories) {
            return null;
        }

        $result = [];
        foreach ($itemCategories as $itemCategory) {
            try {
                $category = $this->categoryRepository->find($itemCategory->getCategory()->getId());
            } catch (CategoryNotFoundException $e) {
                throw new TransformationFailedException(sprintf('Category "%s" not found !', $itemCategory->getCategory()->getId()->toString()));
            } catch (InvalidUuidStringException $e) {
                throw new TransformationFailedException(sprintf('Invalid UUID "%s" !', $itemCategory->getCategory()->getId()->toString()));
            }

            if (null === $category) {
                throw new TransformationFailedException(sprintf('An category with id "%s" does not exist!', $itemCategory->getCategory()->getId()->toString()));
            }

            $result[] = $category;
        }

        return $result;
    }
}