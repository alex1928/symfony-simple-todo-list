<?php

namespace App\Tests\Form\Type;


use App\Form\Type\TaskCategoryFormType;
use App\Entity\TaskCategory;
use Symfony\Component\Form\Test\TypeTestCase;


class TaskCategoryFormTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'name' => "Test category name",
        ];

        $categoryToCompare = new TaskCategory();

        $form = $this->factory->create(TaskCategoryFormType::class, $categoryToCompare);

        $category = new TaskCategory();
        $category->setName($formData['name']);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($category, $categoryToCompare);

        $view = $form->createView();
        $children = $view->children;

        foreach(array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}