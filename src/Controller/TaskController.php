<?php

namespace App\Controller;

use App\Entity\Task;
use App\Utils\Paginator;
use App\Service\TaskService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    private UserService $userService;
    private TaskService $taskService;

    public function __construct(UserService $userService, TaskService $taskService, ) {
        $this->userService = $userService;
        $this->taskService = $taskService;
    }

    #[Route('/task', name: 'task')]
    public function index(Request $request, Paginator $paginator, EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Task::class);
        $query =  $repository->createQueryBuilder('t')->orderBy('t.id', 'DESC');
        $paginator->paginate($query, $request->get('page', 1), 20);
        $users = $this->userService->list();
        return $this->render('task/index.html.twig', [
            'users' => $users,
            'paginator' => $paginator
        ]);
    }

    /**
     * @return Response
     */
    #[Route('/task/show-all', name: 'task_show_all', methods: ['GET'])]
    public function showAll(): Response
    {
        $tasks = $this->taskService->list();
        return $this->json($tasks);
    }

    /**
     * @param int $id
     * @return Response
     */
    #[Route('/task/show/{id}', name: 'task_show', methods: ['POST'])]
    public function show(int $id): Response
    {
        $task = $this->taskService->get($id);
        if (!$task) {
            return $this->json('Задача с id = ' . $id . ' не найдена!', 404);
        }
        return $this->json($task);
    }

    /**
     * @param int $id
     * @return Response
     */
    #[Route('/task/delete/{id}', name: 'task_delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $answer = $this->taskService->delete($id);
        if (!$answer) {
            return $this->json('Ошибка при удалении задачи с id = ' . $id, 404);
        }
        return $this->json(['success' => true]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return Response
     */
    #[Route('/task/update/{id}', name: 'task_update', methods: ['PUT'])]
    public function update(Request $request, int $id): Response
    {
        $data = $request->request;
        $answer = $this->taskService->update($id, $data);
        if (!$answer) {
            return $this->json('Ошибка при обновлении задачи с id = ' . $id, 404);
        }
        return $this->json(['success' => true]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/task/create', name: 'task_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $data = $request->request;
        $answer = $this->taskService->save($data);
        return $this->json(['success' => (bool)$answer]);
    }
}
