<?php

namespace App\Form\DataTransformer;


use App\Repository\TaskCategoryRepository;
use Symfony\Component\Form\DataTransformerInterface;


/**
 * Class TaskCategoryTransformer
 * @package App\Form\DataTransformer
 */
class TaskCategoryTransformer implements DataTransformerInterface {

    /**
     * @var TaskCategoryRepository
     */
    private $taskCategoryRepository;
    /**
     * TaskCategoryTransformer constructor.
     */
    public function __construct(TaskCategoryRepository $repository)
    {
        $this->taskCategoryRepository = $repository;
    }


    /**
     * @param mixed $taskCategory
     * @return mixed|null
     */
    public function transform($taskCategory)
    {
        return ($taskCategory !== null) ? $taskCategory->getId() : null;
    }


    /**
     * @param mixed $taskCategoryId
     * @return \App\Entity\TaskCategory|mixed|string|null
     */
    public function reverseTransform($taskCategoryId)
    {
        if($taskCategoryId === null && $taskCategoryId === '') {
            return '';
        }

        $taskCategory = $this->taskCategoryRepository->find($taskCategoryId);
        return $taskCategory;
    }


}



