<?php

namespace Tests\AppBundle\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Test\TypeTestCase;

class TaskFormTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $title = 'Title Task';
        $content = 'Content Task';

        $formData = [
            'title' => $title,
            'content' => $content,
        ];

        $model = new Task();
        $form = $this->factory->create(TaskType::class, $model);
        
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($model->getTitle(), $title);
        $this->assertEquals($model->getContent(), $content);
    }
}
