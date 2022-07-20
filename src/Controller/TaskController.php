<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    #[Route('/task', name: 'app_task')]
    public function index(TaskRepository $taskRepository): JsonResponse
    {
        $task = $taskRepository->findAll();

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'data' => $task,
            'path' => 'src/Controller/TaskController.php',
        ]);
    }
}
