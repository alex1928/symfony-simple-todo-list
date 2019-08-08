<?php

namespace App\Form;

use App\Entity\Task;
use App\Repository\TaskCategoryRepository;
use App\Form\DataTransformer\TaskCategoryTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskFormType extends AbstractType
{
    private $taskCategoryRepositiory;

    public function __construct(TaskCategoryRepository $repository)
    {
        $this->taskCategoryRepositiory = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content')
            ->add('taskCategory', HiddenType::class)
            ->get('taskCategory')->addModelTransformer(new TaskCategoryTransformer($this->taskCategoryRepositiory));
        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
