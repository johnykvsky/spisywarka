<?php

namespace App\Tests\Requests;

use App\Request\CreateCollectionRequest;
use App\Tests\Mothers\CollectionMother;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Ramsey\Uuid\Uuid;

class CreateCollectionRequestTest extends TestCase
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
        $collecion = CollectionMother::random()->jsonSerialize();
        $request = new CreateCollectionRequest($collecion['name'],$collecion['description']);

        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $errors = $validator->validate($request);

        $this->assertCount(0, $errors);
        $this->assertSame($collecion['name'], $request->getName());
        $this->assertSame($collecion['description'], $request->getDescription());
    }
}