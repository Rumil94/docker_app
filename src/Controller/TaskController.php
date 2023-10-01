<?php

namespace App\Controller;

use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{

    #[Route('/task', name: 'task')]
    public function index(): Response
    {
        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController',
        ]);
    }

    /**
     * @param TaskService $taskService
     * @return Response
     */
    #[Route('/task/show-all', name: 'task_show_all', methods: ['GET'])]
    public function showAll(TaskService $taskService): Response
    {
        $tasks = $taskService->list();
        return $this->json($tasks);
    }

    /**
     * @param int $id
     * @param TaskService $taskService
     * @return Response
     */
    #[Route('/task/show/{id}', name: 'task_show', methods: ['POST'])]
    public function show(int $id, TaskService $taskService): Response
    {
        $task = $taskService->get($id);
        if (!$task) {
            return $this->json('Задача с id = ' . $id . ' не найдена!', 404);
        }
        return $this->json($task);
    }

    /**
     * @param int $id
     * @param TaskService $taskService
     * @return Response
     */
    #[Route('/task/delete/{id}', name: 'task_delete', methods: ['DELETE'])]
    public function delete(int $id, TaskService $taskService): Response
    {
        $answer = $taskService->delete($id);
        if (!$answer) {
            return $this->json('Ошибка при удалении задачи с id = ' . $id, 404);
        }
        return $this->json(['success' => true]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @param TaskService $taskService
     * @return Response
     */
    #[Route('/task/update/{id}', name: 'task_update', methods: ['PUT'])]
    public function update(Request $request, int $id, TaskService $taskService): Response
    {
        $data = $request->request;
        $answer = $taskService->update($id, $data);
        if (!$answer) {
            return $this->json('Ошибка при обновлении задачи с id = ' . $id, 404);
        }
        return $this->json(['success' => true]);
    }

    /**
     * @param Request $request
     * @param TaskService $taskService
     * @return Response
     */
    #[Route('/task/create', name: 'task_create', methods: ['POST'])]
    public function create(Request $request, TaskService $taskService): Response
    {
        $data = $request->request;
        $answer = $taskService->save($data);
        return $this->json(['success' => (bool)$answer]);
    }
}
