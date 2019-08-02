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


/**
 * Class TaskController
 * @package App\Controller
 */
class TaskController extends AbstractController
{

    /**
     * @Route("/{_locale}/task", name="task", requirements={
     *     "_locale"="%app.locales%"
     * })
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function index(Request $request, TranslatorInterface $translator)
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

            $this->addFlash("success", $translator->trans("Task has been added."));

            return $this->redirectToRoute('task');
        }


        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController',
            'tasks' => $tasks,
            'form' => $newTaskForm->createView(),
        ]);
    }


    /**
     * @Route("/{_locale}/task/edit/{id}", name="edit_task", requirements={
     *     "_locale"="%app.locales%"
     * })
     *
     * @param Task $task
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Task $task, Request $request, TranslatorInterface $translator) {

        $editForm = $this->createForm(TaskFormType::class, $task);

        $editForm->handleRequest($request);

        if($editForm->isSubmitted() && $editForm->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            $task = $editForm->getData();

            $editDate = new \DateTime('now');
            $task->setEditDate($editDate);

            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans("Task has been modified."));

            return $this->redirectToRoute('show_task', [
               'id' => $task->GetId(),
            ]);
        }


        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $editForm->createView(),
        ]);
    }


    /**
     *
     * @Route("/{_locale}/task/{id}", name="show_task", requirements={
     *     "_locale"="%app.locales%"
     * })
     *
     * @param Task $task
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
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

            $this->addFlash("success", $translator->trans("Task has been completed."));

            return $this->redirectToRoute('task');
        }

        return $this->render('task/show.html.twig', [
            'task' => $task,
            'form' => $completeForm->createView(),
        ]);
    }



}
