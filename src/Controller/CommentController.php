<?php

namespace App\Controller;

use App\Service\CommentService;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    private CommentService $commentService;
    private TaskService $taskService;

    public function __construct(CommentService $commentService, TaskService $taskService) {
        $this->commentService = $commentService;
        $this->taskService = $taskService;
    }

    #[Route('/comment', name: 'comment')]
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }

    #[Route('/comment/create', name: 'comment_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $data = $request->request;
        $id = $data->get('id');
        $task = $this->taskService->getTask($id);
        if (!$task) {
            return $this->json(['success' => false, 'message' => "Задача #{$id} не найдена!"]);
        }
        $answer = $this->commentService->save($data, $task);
        if (!$answer) {
            return $this->json(['success' => false, 'message' => "Не удалось сохранить комментарий к задаче #{$id}!"]);
        }
        $comments = $this->commentService->listByTaskId($data->get('id'));
        return $this->json(['success' => true, 'comments' => $comments]);
    }
}
