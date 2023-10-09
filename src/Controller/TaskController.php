<?php

namespace App\Controller;

use App\Utils\Paginator;
use App\Service\TaskService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
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
    public function index(): Response
    {
        $users = $this->userService->list();
        return $this->render('task/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @param Request $request
     * @param Paginator $paginator
     * @return Response
     */
    #[Route('/task/show-all', name: 'task_show_all', methods: ['GET'])]
    public function showAll(Request $request, Paginator $paginator): Response
    {
        $limit = 15;
        $page = $request->get('page', 1);
        $term = $request->get('query', '');
        $startNum = $page != 1 ? $page * $limit - $limit : 0;
        $params = ['term' => $term];
        if ($this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY')
            && (!$this->isGranted('ROLE_ADMIN') || !$this->isGranted('ROLE_MODERATOR'))
        ) {
            $params += ['userId' => $this->getUser()->getId()];
        }
        $query =  $this->taskService->getTasksQuery($params);
        $paginator->paginate($query, $page, $limit);
        return new Response(
            $this->renderView('task/list.html.twig', [
                'startNum' => $startNum,
                'paginator' => $paginator
            ])
        );
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
     * @param MailerInterface $mailer
     * @return Response
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    #[Route('/task/create', name: 'task_create', methods: ['POST'])]
    public function create(Request $request, MailerInterface $mailer): Response
    {
        $data = $request->request;
        $answer = $this->taskService->save($data);
        if ($answer) {
            $email = (new Email())
                ->from('hello@example.com')
                ->to('admin@example.com')
                ->subject('Новая задача')
                ->text('Создана новая задача!')
                ->html('<p>Пользователь: ' . $this->getUser()->getUserIdentifier() . '</p><p>Номер задачи: <b>' . $answer . '</b></p>');

            $mailer->send($email);
        }
        return $this->json(['success' => (bool)$answer]);
    }
}
