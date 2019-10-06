<?php

namespace App\Tests\Requests;

use App\Request\UpdateCollectionRequest;
use App\Tests\Mothers\CollectionMother;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Ramsey\Uuid\Uuid;

class UpdateCollectionRequestTest extends TestCase
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
        $collection = CollectionMother::random()->jsonSerialize();
        $request = new UpdateCollectionRequest($collection['id'], $collection['name'], $collection['description']);

        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $errors = $validator->validate($request);

        $this->assertCount(0, $errors);
        $this->assertSame($collection['id'], $request->getId()->toString());
        $this->assertSame($collection['name'], $request->getName());
        $this->assertSame($collection['description'], $request->getDescription());
    }
}