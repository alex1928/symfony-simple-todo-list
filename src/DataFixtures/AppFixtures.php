<?php

namespace App\DataFixtures;

use App\Entity\TaskCategory;
use App\Entity\User;
use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker\Factory;



class AppFixtures extends Fixture
{
    const TASKS_AMOUNT = 10;
    const CATEGORIES_AMOUNT = 3;
    private $passwordEncoder;
    private $faker;


    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->passwordEncoder = $encoder;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin');

        $password = $this->passwordEncoder->encodePassword($user, 'devpass');

        $user->setPassword($password);

        $categories = [];
        for($i = 0; $i < self::CATEGORIES_AMOUNT; $i++) {

            $category_name = "Category ".($i + 1);
            $category = new TaskCategory();
            $category->setName($category_name);
            $category->setUser($user);

            $categories[] = $category;
        }


        for($i = 0; $i < self::TASKS_AMOUNT; $i++) {

            $task = $this->createRandomTask();
            $task->setUser($user);

            if($i <= self::TASKS_AMOUNT / 2) {

                $taskCategory = $categories[array_rand($categories)];
                $task->setTaskCategory($taskCategory);
            }

            $manager->persist($task);
        }

        foreach($categories as $category) {

            $manager->persist($category);
        }

        $manager->persist($user);
        $manager->flush();
    }



    private function createRandomTask() {

        $task = new Task();
        $content = $this->faker->paragraphs(rand(1, 5), true);

        $task->setContent($content);
        $task->setAddDate(new \DateTime('now'));

        return $task;
    }

}