<?php

namespace App\Controller;

use App\Context\ControllerContext;
use DateTime;
use App\Entity\Task;
use App\Repository\StatusRepository;
use App\Repository\TaskRepository;
use App\Repository\UserStoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskController extends ControllerContext
{
    private $taskRepository;
    private $userStoryRepository;
    private $statusRepository;

    public function __construct(
        TaskRepository $taskRepository,
        UserStoryRepository $userStoryRepository,
        StatusRepository $statusRepository
    ) {
        $this->taskRepository = $taskRepository;
        $this->userStoryRepository = $userStoryRepository;
        $this->statusRepository = $statusRepository;
    }

    
    /* List all Task */
    #[Route('/tasks', name: 'task_list', methods: ["HEAD", "GET"])]
    public function taskList(): JsonResponse
    {
        $task = $this->taskRepository->findAll();

        return $this->json($task, Response::HTTP_OK, [], ['groups' => ['task']]);
    }


    /* List all Tasks on details */
    #[Route('/tasks_details', name: 'task_list_details', methods: ["HEAD", "GET"])]
    public function taskListDetails(): JsonResponse
    {
        $task = $this->taskRepository->findAll();

        return $this->json($task, Response::HTTP_OK, [], ['groups' => [
            'task',
            'task_status', 'status',
            'task_user', 'user',
            'task_userStory', 'userStory'
        ]]);
    }


    /* Specific task details */
    #[Route('/task/{id}', name: 'task', methods: ["HEAD", "GET"])]
    public function task(int $id): JsonResponse
    {
        $task = $this->taskRepository->find($id);

        // Check if task exists
        if (!$task) {
            return $this->json($this->errorMessageEntityNotFound("task"), Response::HTTP_BAD_REQUEST);
        }
        return $this->json($task, Response::HTTP_OK, [], ['groups' => [
            'task',
            'task_status', 'status',
            'task_user', 'user',
            'task_userStory', 'userStory'
        ]]);
    }


    /* Create task */
    #[Route('/task', name: 'create_task', methods: ["POST"])]
    public function createTask(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check JSON body
        if (
            empty($data["name"]) ||
            empty($data["user_story_id"]) ||
            empty($data["status_id"])
        ) {
            return $this->json($this->errorMessageJsonBody(), Response::HTTP_BAD_REQUEST);
        }

        $user_story = $this->userStoryRepository->find($data["user_story_id"]);
        $status = $this->statusRepository->find($data["status_id"]);

        // Check if user_story exists
        if (!$user_story) {
            return $this->json($this->errorMessageEntityNotFound("user_story"), Response::HTTP_BAD_REQUEST);
        }

        // Check if Status exists
        if (!$status) {
            return $this->json($this->errorMessageEntityNotFound("status"), Response::HTTP_BAD_REQUEST);
        }

        $task = new Task();
        $task->setName($data["name"]);
        $task->setUserStory($user_story);
        $task->setStatus($status);
        $this->taskRepository->add($task, true);

        return $this->json($task, Response::HTTP_CREATED, [],  ['groups' => [
            'task',
            'task_status', 'status',
            'task_user', 'user',
            'task_userStory', 'userStory'
        ]]);
    }


    /* Edit Task */
    #[Route('task/{id}', name: 'edit_task', methods: ["PATCH"])]
    public function editTask(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $task = $this->taskRepository->find($id);

        // Check if task exists
        if (!$task) {
            return $this->json($this->errorMessageEntityNotFound("task"), Response::HTTP_BAD_REQUEST);
        }

        if (!empty($data["name"])) {
            $task->setName($data["name"]);
        }

        if (!empty($data["user_story"])) {
            $user_story = $this->userStoryRepository->find($data["user_story_id"]);
            // Check if user_story exists
            if (!$user_story) {
                return $this->json($this->errorMessageEntityNotFound("user_story"), Response::HTTP_BAD_REQUEST);
            }
            $task->setUserStory($user_story);
        }

        if (!empty($data["status"])) {
            $status = $this->statusRepository->find($data["status_id"]);
            // Check if status exists
            if (!$status) {
                return $this->json($this->errorMessageEntityNotFound("status"), Response::HTTP_BAD_REQUEST);
            }
            $task->setStatus($status);
        }

        $this->taskRepository->add($task, true);

        return $this->json($task, Response::HTTP_OK, [], ['groups' => [
            'task',
            'task_status', 'status',
            'task_user', 'user',
            'task_userStory', 'userStory'
        ]]);
    }


    /* Hard Delete Task */
    #[Route('/task/{id}', name: 'delete_task', methods: ["DELETE"])]
    public function deleteTask(int $id): JsonResponse
    {
        $task = $this->taskRepository->find($id);

        // Check if task exists
        if (!$task) {
            return $this->json($this->errorMessageEntityNotFound("task"), Response::HTTP_BAD_REQUEST);
        }

        $this->taskRepository->remove($task, true);

        return $this->json($task, Response::HTTP_OK, [], ['groups' => [
            'task',
            'task_status', 'status',
            'task_user', 'user',
            'task_userStory', 'userStory'
        ]]);
    }
}
