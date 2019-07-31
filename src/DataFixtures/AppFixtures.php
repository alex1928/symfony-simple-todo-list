<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->passwordEncoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin');

        $password = $this->passwordEncoder->encodePassword($user, 'testpass');

        $user->setPassword($password);

        for($i = 0; $i < 5; $i++) {

            $task = new Task();
            $task->setContent("Test task number " . $i);
            $task->setAddDate(new \DateTime('now'));

            $dateInterval = new \DateInterval('P2DT' . $i . 'H');
            $dueDate = $task->getAddDate()->add($dateInterval);

            $task->setDueDate($dueDate);
            $task->setUser($user);
            $user->addTask($task);

            $manager->persist($task);
        }

        $manager->persist($user);

        $manager->flush();
    }
}
