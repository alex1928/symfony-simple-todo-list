<?php

namespace App\Form\Type;

use App\Entity\Task;
use App\Entity\TaskCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class TaskEditFormType extends AbstractType
{
    private $security;
    private $translator;

    public function __construct(Security $security, TranslatorInterface $translator)
    {
        $this->security = $security;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->security->getUser();

        $noAssignedCategory = new TaskCategory();
        $noAssignedCategory->setName($this->translator->trans('Unassigned'));
        $noAssignedCategory->setUser($user);

        $builder
            ->add('content')
            ->add('taskCategory', ChoiceType::class, [
                'required' => false,
                'placeholder' => $this->translator->trans('Unassigned'),
                'empty_data' => null,
                'choices' => $user->getTaskCategories(),
                'choice_label' => function(TaskCategory $category, $key, $value) {
                    return $category->getName();
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
