<?php
namespace App\Service;

use App\Entity\Category;
use App\DTO\CategoryDTO;
use App\Command\CommandInterface;
use App\Command\CreateCategoryCommand;
use App\Command\UpdateCategoryCommand;
use Ramsey\Uuid\Uuid;

class CategoryService
{
    /**
     * @return CategoryDTO
     */
    public function fillCategoryDTO(Category $category): CategoryDTO
    {
        return new CategoryDTO(
            $category->getId()->toString(),
            $category->getName(),
            $category->getDescription()
        );
    }

    /**
     * @return CreateCategoryCommand|UpdateCategoryCommand
     */
    public function getCommand(CategoryDTO $categoryDTO):  CommandInterface
    {
        if (empty($categoryDTO->getId())) {
            return new CreateCategoryCommand(
                Uuid::uuid4(),
                $categoryDTO->getName(),
                $categoryDTO->getDescription()
            );
        } else {
            return new UpdateCategoryCommand(
                $categoryDTO->getId(),
                $categoryDTO->getName(),
                $categoryDTO->getDescription()
            );
        }
    }
}