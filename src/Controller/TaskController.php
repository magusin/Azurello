<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    #[Route('/tasks', name: 'task_list', methods: ["HEAD", "GET"])]
    public function taskList(TaskRepository $taskRepository): JsonResponse
    {
        $task = $taskRepository->findAll();

        return $this->json($task, Response::HTTP_OK, [], ['groups' => ['task']]);
    }

    #[Route('/task_details', name: 'task_list_detail', methods: ["HEAD", "GET"])]
    public function taskDetails(TaskRepository $taskRepository): JsonResponse
    {
        $task = $taskRepository->findAll();

        return $this->json($task, Response::HTTP_OK, [], ['groups' => ['task', 'user', 'group_task', 'task_status']]);
    }

}
