<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskFormType;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Contracts\Translation\TranslatorInterface;

class TaskController extends AbstractController
{
    /**
     * @Route("/{_locale}/task", name="task", requirements={
     *     "_locale"="%app.locales%"
     * })
     */
    public function index(Request $request, TranslatorInterface $translator, LoggerInterface $logger)
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

            $this->addFlash("info", $translator->trans("Task has been added."));

            return $this->redirectToRoute('task');
        }

        $locale = $request->getLocale();
        $logger->info($locale);

        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController',
            'tasks' => $tasks,
            'form' => $newTaskForm->createView(),
        ]);
    }

    /**
     * @Route("/{_locale}/task/{id}", name="show_task", requirements={
     *     "_locale"="%app.locales%"
     * })
     */
    public function show(Task $task, Request $request, TranslatorInterface $translator) {

        $completeForm = $this->createFormBuilder($task)
            ->add('complete_btn', SubmitType::class, ['label' => $translator->trans("Completed")])
            ->getForm();

        $completeForm->handleRequest($request);

        if($completeForm->isSubmitted() && $completeForm->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($task);
            $entityManager->flush();

            $this->addFlash("info", $translator->trans("Task has been completed."));

            return $this->redirectToRoute('task');
        }

        return $this->render('task/show.html.twig', [
            'task' => $task,
            'form' => $completeForm->createView(),
        ]);
    }
}
