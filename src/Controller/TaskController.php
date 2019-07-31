<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends AbstractController
{
    /**
     * @Route("/task", name="task")
     */
    public function index(Request $request)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $tasks = $user->getTasks();

        $task = new Task();
        $newTaskForm = $this->createForm(TaskFormType::class, $task);

        $newTaskForm->handleRequest($request);

        if($newTaskForm->isSubmitted() && $newTaskForm->isValid()) {

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
    public function show(Task $task) {

        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }
}
