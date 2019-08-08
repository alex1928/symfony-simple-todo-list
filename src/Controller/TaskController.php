<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\TaskCategory;
use App\Form\TaskFormType;
use App\Repository\TaskRepository;
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
     * @Route("/{_locale}/tasks", name="tasks", requirements={
     *     "_locale"="%app.locales%"
     * })
     * @param TaskRepository $taskRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listUnassignedTasks(TaskRepository $taskRepository)
    {
        $user = $this->getUser();
        $categories = $user->getTaskCategories();
        $tasks = $taskRepository->findTasksWithoutCategory($user);
        $category = null;

        $task = new Task();
        $task->setTaskCategory($category);
        $taskForm = $this->createForm(TaskFormType::class, $task, [
            'action' => $this->generateUrl('new_task'),
        ]);


        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController',
            'categories' => $categories,
            'currentCategory' => $category,
            'tasks' => $tasks,
            'form' => $taskForm->createView(),
        ]);
    }


    /**
     * @Route("/{_locale}/tasks/{id<\d+>}", name="tasks_category", requirements={
     *     "_locale"="%app.locales%"
     * })
     *
     * @param TaskCategory $category
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listTasksFromCategory(TaskCategory $category)
    {
        $user = $this->getUser();
        $categories = $user->getTaskCategories();
        $tasks = $category->getTasks();

        $task = new Task();
        $task->setTaskCategory($category);
        $taskForm = $this->createForm(TaskFormType::class, $task, [
            'action' => $this->generateUrl('new_task'),
        ]);

        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController',
            'categories' => $categories,
            'currentCategory' => $category,
            'tasks' => $tasks,
            'form' => $taskForm->createView(),
        ]);
    }


    /**
     * @Route("/{_locale}/task/new", name="new_task", requirements={
     *     "_locale"="%app.locales%"
     * })
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function new(Request $request, TranslatorInterface $translator)
    {
        $user = $this->getUser();
        $task = new Task();
        $newTaskForm = $this->createForm(TaskFormType::class, $task);

        $newTaskForm->handleRequest($request);

        if($newTaskForm->isSubmitted()) {

            $task = $newTaskForm->getData();

            if($newTaskForm->isValid()) {

                $entityManager = $this->getDoctrine()->getManager();

                $task->setAddDate(new \DateTime('now'));
                $task->SetUser($user);

                $entityManager->persist($task);
                $entityManager->flush();

                $this->addFlash("success", $translator->trans("Task has been added."));
            } else {

                $errors = (string) $newTaskForm->getErrors(true, true);
                $this->addFlash('danger', $errors);
            }

            $taskCategory = $task->getTaskCategory();

            if($taskCategory !== null) {
                return $this->redirectToRoute('tasks_category',[ 'id' => $taskCategory->getId() ]);
            }

        }

        return $this->redirectToRoute('tasks');
    }


    /**
     *
     * @Route("/{_locale}/task/{id<\d+>}", name="show_task", requirements={
     *     "_locale"="%app.locales%"
     * })
     *
     * @param Task $task
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function show(Task $task, Request $request, TranslatorInterface $translator)
    {
        $completeForm = $this->createFormBuilder($task)
            ->add('complete_btn', SubmitType::class, ['label' => $translator->trans("Completed")])
            ->getForm();

        $completeForm->handleRequest($request);

        if($completeForm->isSubmitted() && $completeForm->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($task);
            $entityManager->flush();

            $this->addFlash("success", $translator->trans("Task has been completed."));

            $taskCategory = $task->getTaskCategory();

            if($taskCategory !== null) {
                return $this->redirectToRoute('tasks_category', [ 'id' => $taskCategory->getId() ]);
            }

            return $this->redirectToRoute('tasks');
        }

        return $this->render('task/show.html.twig', [
            'task' => $task,
            'form' => $completeForm->createView(),
        ]);
    }


    /**
     * @Route("/{_locale}/task/edit/{id<\d+>}", name="edit_task", requirements={
     *     "_locale"="%app.locales%"
     * })
     *
     * @param Task $task
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function edit(Task $task, Request $request, TranslatorInterface $translator)
    {
        $editForm = $this->createForm(TaskFormType::class, $task);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

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
}
