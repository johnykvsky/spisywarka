<?php 

namespace App\Tests\Form\Type;

use App\Form\Type\TestedType;
use App\DTO\CategoryDTO;
use App\Form\Type\CategoryType;
use Symfony\Component\Form\Test\TypeTestCase;

class CategoryTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'name' => 'category',
            'description' => 'description',
        ];

        $categoryDTO = new CategoryDTO(null, $formData['name'], $formData['description']);

        $form = $this->factory->create(CategoryType::class, $categoryDTO);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($form->getData(), $categoryDTO);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}