<?php
namespace App\Service;

use App\Entity\Category;
use App\DTO\CategoryDTO;
use App\Command\CommandInterface;
use App\Command\CreateCategoryCommand;
use App\Command\UpdateCategoryCommand;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use App\Traits\CommandInstanceTrait;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;

class CategoryService
{
    use CommandInstanceTrait;

    /**
     * @param Category $category
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
     * @param CategoryDTO $categoryDTO
     * @return CreateCategoryCommand|UpdateCategoryCommand
     */
    public function getCommand(CategoryDTO $categoryDTO):  CommandInterface
    {
        $command = $this->getCommandInstance($categoryDTO->getId(), 'Category');
        return $command->newInstanceArgs([
            $categoryDTO->getId() ?? Uuid::uuid4(),
            $categoryDTO->getName(),
            $categoryDTO->getDescription(),
            null
        ]);
    }
}