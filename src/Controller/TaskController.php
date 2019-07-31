<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TaskController extends AbstractController
{
    /**
     * @Route("/task", name="task")
     */
    public function index(Request $request)
    {

        $user = $this->getUser();
        $tasks = $user->getTasks();

        $task = new Task();
        $newTaskForm = $this->createForm(TaskFormType::class, $task);

        $newTaskForm->handleRequest($request);

        if($newTaskForm->isSubmitted() && $newTaskForm->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            $task = $newTaskForm->getData();
            $task->setAddDate(new \DateTime('now'));
            $task->SetUser($user);

            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash("info", "Task was added.");

            return $this->redirectToRoute('task');
        }

        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController',
            'tasks' => $tasks,
            'form' => $newTaskForm->createView(),
        ]);
    }

    /**
     * @Route("/task/{id}", name="show_task")
     */
    public function show(Task $task, Request $request) {

        $completeForm = $this->createFormBuilder($task)
            ->add('complete_btn', SubmitType::class, ['label' => 'Completed'])
            ->getForm();

        $completeForm->handleRequest($request);

        if($completeForm->isSubmitted() && $completeForm->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($task);
            $entityManager->flush();

            $this->addFlash("info", "Tas was completed.");

            return $this->redirectToRoute('task');
        }

        return $this->render('task/show.html.twig', [
            'task' => $task,
            'form' => $completeForm->createView(),
        ]);
    }
}
