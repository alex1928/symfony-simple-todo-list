<?php

namespace App\Controller;

use App\Entity\TaskCategory;
use App\Form\Type\TaskCategoryFormType;
use App\Repository\TaskCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CategoryController
 * @package App\Controller
 */
class CategoryController extends AbstractController
{

    /**
     * @Route("/categories", name="categories")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $user = $this->getUser();
        $categories = $user->getTaskCategories();

        $category = new TaskCategory();
        $categoryForm = $this->createForm(TaskCategoryFormType::class, $category, [
            'action' => $this->generateUrl('new_category'),
        ]);

        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'categories' => $categories,
            'form' => $categoryForm->createView(),
        ]);
    }


    /**
     * @Route("/category/new", name="new_category")
     *
     * @param Request $request
     * @param TaskCategoryRepository $categoryRepository
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function new(Request $request, TaskCategoryRepository $categoryRepository, TranslatorInterface $translator)
    {
        $user = $this->getUser();

        $category = new TaskCategory();
        $form = $this->createForm(TaskCategoryFormType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted()) {

            $category = $form->getData();

            if($form->isValid()) {

                $count = $categoryRepository->count([
                    'user' => $user,
                    'name' => $category->getName(),
                ]);

                if($count) {
                    $this->addFlash('danger', $translator->trans("Category with this name already exists."));
                } else {

                    $category->setUser($user);

                    $manager = $this->getDoctrine()->getManager();
                    $manager->persist($category);
                    $manager->flush();

                    $this->addFlash('success', $translator->trans("Category has been added."));
                }

            } else {

                $errors = (string) $form->getErrors(true, true);
                $this->addFlash('danger', $errors);
            }
        }

        return $this->redirectToRoute('categories');
    }


    /**
     * @Route("/category/edit/{id<\d+>}", name="edit_category")
     *
     * @param TaskCategory $category
     * @param Request $request
     * @param TaskCategoryRepository $categoryRepository
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(TaskCategory $category, Request $request, TaskCategoryRepository $categoryRepository, TranslatorInterface $translator)
    {
        $user = $this->getUser();

        if($category->getUser() != $user) {
            return $this->redirectToRoute('categories');
        }

        $form = $this->createForm(TaskCategoryFormType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $count = $categoryRepository->count([
                'user' => $user,
                'name' => $category->getName(),
            ]);

            if($count) {
                $this->addFlash('danger', $translator->trans("Category with this name already exists."));
            } else {

                $category = $form->getData();
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($category);
                $manager->flush();

                $this->addFlash('success', $translator->trans("Category has been modified."));

                return $this->redirectToRoute('edit_category', [
                    'id' => $category->getId(),
                ]);
            }
        }

        return $this->render('category/edit.html.twig', [
            'controller_name' => 'CategoryController',
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/category/remove/{id<\d+>}", name="remove_category")
     *
     * @param TaskCategory $category
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function remove(TaskCategory $category, TranslatorInterface $translator) {

        $user = $this->getUser();

        if($category->getUser() != $user) {
            return $this->redirectToRoute('categories');
        }

        $manager = $this->getDoctrine()->getManager();
        $categoryTasks = $category->getTasks();

        foreach($categoryTasks as $task) {
            $task->setTaskCategory(null);
            $manager->persist($task);
        }

        $manager->remove($category);
        $manager->flush();

        $this->addFlash("success", $translator->trans("Category has been removed."));

        return $this->redirectToRoute('categories');
    }
}
