<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{

    private $registry;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry->getManager();
        parent::__construct($registry, Task::class);
    }

    /**
     * @param Task $task
     * @return bool
     */
    public function save(Task $task): bool
    {
        try {
            $this->getEntityManager()->persist($task);
            $this->getEntityManager()->flush();
        } catch (\Exception $exception) {
            return false;
        }
        return true;
    }

    /**
     * @param Task $task
     * @return bool
     */
    public function remove(Task $task): bool
    {
        try {
            $this->getEntityManager()->remove($task);
            $this->getEntityManager()->flush();
        } catch (\Exception $exception) {
            return false;
        }
        return true;
    }
}
