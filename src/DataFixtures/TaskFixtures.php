<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Service\UserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use joshtronic\LoremIpsum;

class TaskFixtures extends Fixture
{
    private UserService $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    public function load(ObjectManager $manager): void
    {
        $lipsum = new LoremIpsum();
        for ($i = 1; $i <= 11; $i++) {
            $task = new Task();
            $task->setTitle($lipsum->words(rand(5, 10)));
            $task->setContent($lipsum->words(rand(11, 25)));
            $task->setCreatedAt(new \DateTime('now'));
            $task->setIsFinished(false);
            $userId = rand(1, 2);
            $user = $this->userService->get($userId);
            $task->setUser($user);
            $manager->persist($task);
            $manager->flush();
        }
    }
}
