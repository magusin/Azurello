<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    #[Route('/task', name: 'app_task')]
    public function index(TaskRepository $taskRepository): JsonResponse
    {
        $task = $taskRepository->findAll();

        return $this->json($task, Response::HTTP_OK, [], ['groups' => ['task_details', 'project', 'user', 'group_task', 'task_status']]);
    }
}
