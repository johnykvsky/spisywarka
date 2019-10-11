<?php
namespace App\Form\DataTransformer;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\Exception\CategoryNotFoundException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Exception\InvalidUuidStringException;

class UuidToCategoryTransformer implements DataTransformerInterface
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Transforms an object (category) to a UuidInterface
     *
     * @param  Category|null $category
     * @return UuidInterface|string
     */    
    public function reverseTransform($category)
    {
        if (null === $category) {
            return '';
        }

        return $category->getId();
    }

    /**
     * Transforms a UuidInterface to an object (Category).
     *
     * @param  string|null $categoryId
     * @return Category|null
     * @throws TransformationFailedException if object (Category) is not found.
     */
    public function transform($categoryId)
    {
        if (!$categoryId) {
            return null;
        }

        try {
            $category = $this->categoryRepository->getCategory(Uuid::fromString($categoryId));
        } catch (CategoryNotFoundException $e) {
            throw new TransformationFailedException(sprintf('Category "%s" not found !', $categoryId));
        } catch (InvalidUuidStringException $e) {
            throw new TransformationFailedException(sprintf('Invalid UUID "%s" !', $categoryId));
        }

        if (null === $category) {
            throw new TransformationFailedException(sprintf('Category with id "%s" does not exist!', $categoryId));
        }

        return $category;
    }
}