<?php

namespace App\Tests\Requests;

use App\Request\UpdateItemRequest;
use App\Tests\Mothers\ItemMother;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Ramsey\Uuid\Uuid;

class UpdateItemRequestTest extends TestCase
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
        $item = ItemMother::random();
        $request = new UpdateItemRequest(
            $item->getId()->toString(),
            $item->getName(),
            $item->getCategory()->getId()->toString(),
            $item->getYear(), 
            $item->getFormat(), 
            $item->getAuthor(), 
            $item->getPublisher(), 
            $item->getDescription(), 
            $item->getStore(), 
            $item->getUrl()
            );

        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $errors = $validator->validate($request);

        $this->assertCount(0, $errors);
        $this->assertSame($item->getId()->toString(), $request->getId()->toString());
        $this->assertSame($item->getName(), $request->getName());
        $this->assertSame($item->getCategory()->getId()->toString(), $request->getCategoryId()->toString());
        $this->assertSame($item->getYear(), $request->getYear());
        $this->assertSame($item->getFormat(), $request->getFormat());
        $this->assertSame($item->getAuthor(), $request->getAuthor());
        $this->assertSame($item->getPublisher(), $request->getPublisher());
        $this->assertSame($item->getDescription(), $request->getDescription());
        $this->assertSame($item->getStore(), $request->getStore());
        $this->assertSame($item->getUrl(), $request->getUrl());
    }
}