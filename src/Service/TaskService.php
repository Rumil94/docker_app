<?php

namespace App\Service;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

class TaskService {

    private Security $security;

    public function __construct(
        private readonly TaskRepository $taskRepository,
        Security $security,
    ) {
        $this->security = $security;
    }


    public function list(): array
    {
        $tasks = $this->taskRepository->findBy([], ['id' => 'desc']);
        $data = [];
        foreach ($tasks as $task) {
            $data[] = $this->getTaskInfo($task);
        }
        return $data ?? [];
    }

    /**
     * @param int $id
     * @return array
     */
    public function get(int $id): array
    {
        $task = $this->taskRepository->find($id);
        return $this->getTaskInfo($task) ?? [];
    }

    /**
     * @param object $data
     * @return int|bool|null
     */
    public function save(object $data): int|bool|null
    {
        $task = new Task();
        $task->setTitle($data->get('title'));
        $task->setContent($data->get('content'));
        $task->setCreatedAt(new \DateTime('now'));
        $task->setIsFinished(false);
        $task->setUser($this->security->getUser());
        if (!$this->taskRepository->save($task)) {
            return false;
        }
        return $task->getId();
    }

    /**
     * @param int $id
     * @param object $data
     * @return bool
     */
    public function update(int $id, object $data): bool
    {
        $task = $this->taskRepository->find($id);
        if ($task) {
            $task->setTitle($data->get('title'));
            $task->setContent($data->get('content'));
            $task->setUpdatedAt(new \DateTime('now'));
            if ($this->taskRepository->save($task)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $task = $this->taskRepository->find($id);
        if ($task) {
            if ($this->taskRepository->remove($task)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Task $task
     * @return array
     */
    protected function getTaskInfo(Task $task): array
    {
        $data = [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'content' => $task->getContent(),
            'created_at' => $task->getCreatedAt() ? date_format($task->getCreatedAt(), "d.m.Y H:i:s") : '',
            'updated_at' => $task->getUpdatedAt() ? date_format($task->getUpdatedAt(), "d.m.Y H:i:s") : '',
            'is_finished' => $task->isIsFinished() ? 'Да' : 'Нет',
            'user' => $task->getUser()->getEmail()
        ];
        return $data ?? [];
    }

    /**
     * @param array $params
     * @return QueryBuilder
     */
    public function getTasksQuery(array $params = []): QueryBuilder
    {
        $qb = $this->taskRepository->createQueryBuilder('t');

        if (isset($params['term']) && !empty($params['term'])) {
            $term = $params['term'];
            $qb->andWhere('t.title like :term or t.content like :term')
                ->setParameter('term', '%'.$term.'%');
        }

        if (isset($params['userId']) && !empty($params['userId'])) {
            $userId = $params['userId'];
            $qb->andWhere('t.user_id = ' . $userId);
        }

        if (isset($params['sort']) && !empty($params['sort'])) {
            $arr = explode(' ', $params['sort']);
            $qb->orderBy('t.' . $arr[0], $arr[1]);
        } else {
            $qb->orderBy('t.id','DESC');
        }

        return $qb;
    }
}