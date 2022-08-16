<?php

namespace App\Controller;

use DateTime;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{
    private $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /* List all task */
    #[Route('/tasks', name: 'task_list', methods: ["HEAD", "GET"])]
    public function taskList(): JsonResponse
    {
        $task = $this ->taskRepository->findAllNotDeleted();

        return $this->json($task, Response::HTTP_OK, [], ['groups' => ['task']]);
    }

     /* List all Task on details */
    #[Route('/task_details', name: 'task_list_detail', methods: ["HEAD", "GET"])]
    public function taskDetails(): JsonResponse
    {
        $task = $this->taskRepository->findAllNotDeleted();

        return $this->json($task, Response::HTTP_OK, [], ['groups' => ['task', 'task_user', 'user', 'task_groupTask', 'group_task', 'task_taskStatus', 'task_status']]);
    }

    /* Specific Task details */
    #[Route('/task/{id}', name: 'task', methods: ["HEAD", "GET"])]
    public function task(int $id): JsonResponse
    {
        $task = $this->taskRepository->find($id);

        // Check if task exists
        if (!$task) {
            return $this->json("No task found", Response::HTTP_BAD_REQUEST);
        }
        
        // Check if task is not deleted
        if ($task->getDeletedBy(!null)) {
            return $this->json("This task is deleted", Response::HTTP_BAD_REQUEST);
        }

        return $this->json($task, Response::HTTP_OK, [], ['groups' => ['task', 'task_user', 'user', 'task_groupTask', 'group_task', 'task_taskStatus', 'task_status']]);
    }

    /* Create task */
    #[Route('/task', name: 'create_task', methods: ["POST"])]
    public function createTask(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check JSON body
        if (
            empty($data["name"]) ||
            empty($data["created_by"])
        ) {
            return $this->json("JSON incorrect", Response::HTTP_BAD_REQUEST);
        }

        $task = new Task();
        $task->setName($data["name"]);
        $task->setCreatedAt(new DateTime());
        $task->setCreatedBy($data["created_by"]);
        $this->taskRepository->add($task, true);

        return $this->json($task, Response::HTTP_CREATED);
    }

}
