<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use KnpU\LoremIpsumBundle\KnpUIpsum;


class AppFixtures extends Fixture
{

    private $passwordEncoder;
    private $loremIpsumGenerator;

    public function __construct(UserPasswordEncoderInterface $encoder, KnpUIpsum $generator)
    {
        $this->passwordEncoder = $encoder;
        $this->loremIpsumGenerator = $generator;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin');

        $password = $this->passwordEncoder->encodePassword($user, 'testpass');

        $user->setPassword($password);

        for($i = 0; $i < 5; $i++) {

            $task = new Task();

            $content = $this->loremIpsumGenerator->getParagraphs($i + 1);
            $task->setContent($content);

            $task->setAddDate(new \DateTime('now'));

            $task->setUser($user);
            $user->addTask($task);

            $manager->persist($task);
        }

        $manager->persist($user);

        $manager->flush();
    }
}
