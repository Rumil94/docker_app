<?php

namespace App\Service;

use App\Repository\TaskRepository;
use App\Entity\{ Comment, Task };
use App\Repository\CommentRepository;
use Symfony\Bundle\SecurityBundle\Security;

class CommentService {

    private Security $security;
    private TaskRepository $taskRepository;

    public function __construct(
        private readonly CommentRepository $commentRepository,
        TaskRepository $taskRepository,
        Security $security
    ) {
        $this->security = $security;
        $this->taskRepository = $taskRepository;
    }

    public function listByTaskId(int $id): array
    {
        $comments = $this->commentRepository->findBy(['task' => $id], ['id' => 'desc']);
        $data = [];
        foreach ($comments as $comment) {
            if ($comment) {
                $data[] = [
                    'id' => $comment->getId(),
                    'text' => $comment->getText(),
                    'created_at' => $comment->getCreatedAt() ? date_format($comment->getCreatedAt(), "d.m.Y H:i:s") : '',
                    'updated_at' => $comment->getUpdatedAt() ? date_format($comment->getUpdatedAt(), "d.m.Y H:i:s") : '',
                    'user' => $comment->getUser()->getEmail(),
                    'is_published' => $comment->isIsPublished(),
                    'archive' => $comment->isArchive()
                ];
            }
        }
        return $data ?? [];
    }

    /**
     * @param object $data
     * @param Task $task
     * @return int|bool|null
     */
    public function save(object $data, Task $task): int|bool|null
    {
        $comment = new Comment();
        $comment->setText($data->get('text'));
        $comment->setUser($this->security->getUser());
        $comment->setCreatedAt(new \DateTime('now'));
        $task->getComments()->add($comment);
        $comment->setTask($task);
        if (!$this->commentRepository->save($comment)) {
            return false;
        }
        return $comment->getId();
    }
}