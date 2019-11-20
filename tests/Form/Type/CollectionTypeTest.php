<?php 

namespace App\Tests\Form\Type;

use App\Form\Type\TestedType;
use App\DTO\CollectionDTO;
use App\Form\Type\CollectionType;
use Symfony\Component\Form\Test\TypeTestCase;

class CollectionTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'name' => 'collection',
            'description' => 'description',
        ];

        $collectionDTO = new CollectionDTO(null, $formData['name'], $formData['description']);

        $form = $this->factory->create(CollectionType::class, $collectionDTO);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($form->getData(), $collectionDTO);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}