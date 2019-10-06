<?php

namespace App\Tests\Requests;

use App\Request\CreateItemRequest;
use App\Tests\Mothers\ItemMother;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Ramsey\Uuid\Uuid;

class CreateItemRequestTest extends TestCase
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
        $item = ItemMother::random()->jsonSerialize();
        $request = new CreateItemRequest($item['name'], (int) $item['year'], $item['format'], $item['author'],
            $item['publisher'], $item['description'], $item['store'], $item['url']
            );

        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $errors = $validator->validate($request);

        $this->assertCount(0, $errors);
        $this->assertSame($item['name'], $request->getName());
        $this->assertSame($item['year'], $request->getYear());
        $this->assertSame($item['format'], $request->getFormat());
        $this->assertSame($item['author'], $request->getAuthor());
        $this->assertSame($item['publisher'], $request->getPublisher());
        $this->assertSame($item['description'], $request->getDescription());
        $this->assertSame($item['store'], $request->getStore());
        $this->assertSame($item['url'], $request->getUrl());
    }
}