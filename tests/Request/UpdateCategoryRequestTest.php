<?php

namespace App\Tests\Requests;

use App\Request\UpdateCategoryRequest;
use App\Tests\Mothers\CategoryMother;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Ramsey\Uuid\Uuid;

class UpdateCategoryRequestTest extends TestCase
{
    protected function setUp()
    {
        AnnotationRegistry::registerLoader('class_exists');
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    public function test_construct(): void
    {
        $category = CategoryMother::random()->jsonSerialize();
        $request = new UpdateCategoryRequest($category['id'], $category['name'], $category['description']);

        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $errors = $validator->validate($request);

        $this->assertCount(0, $errors);
        $this->assertSame($category['id'], $request->getId()->toString());
        $this->assertSame($category['name'], $request->getName());
        $this->assertSame($category['description'], $request->getDescription());
    }
}