<?php

namespace App\Controller;

use DateTime;
use App\Entity\Task;
use App\Repository\StatusRepository;
use App\Repository\TaskRepository;
use App\Repository\UserStoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{
    private $taskRepository;
    private $userStoryRepository;

    public function __construct(TaskRepository $taskRepository, UserStoryRepository $userStoryRepository, StatusRepository $statusRepository)
    {
        $this->taskRepository = $taskRepository;
        $this->userStoryRepository = $userStoryRepository;
        $this->statusRepository = $statusRepository;
    }

    /* List all Task */
    #[Route('/tasks', name: 'task_list', methods: ["HEAD", "GET"])]
    public function taskList(): JsonResponse
    {
        $task = $this->taskRepository->findAll();

        return $this->json($task, Response::HTTP_OK, [], ['groups' => ['task', 'task_status', 'status', 'task_user', 'user', 'task_userStory', 'userStory']]);
    }


    /* List all Tasks on details */
    #[Route('/tasks_details', name: 'task_list_details', methods: ["HEAD", "GET"])]
    public function taskListDetails(): JsonResponse
    {
        $task = $this->taskRepository->findAll();

        return $this->json($task, Response::HTTP_OK, [], ['groups' => ['task', 'task_status', 'status', 'task_user', 'user', 'task_userStory', 'userStory']]);
    }


    /* Specific task details */
    #[Route('/task/{id}', name: 'task', methods: ["HEAD", "GET"])]
    public function task(int $id): JsonResponse
    {
        $task = $this->taskRepository->find($id);

        // Check if task exists
        if (!$task) {
            return $this->json("No task found", Response::HTTP_BAD_REQUEST);
        }
        return $this->json($task, Response::HTTP_OK, [], ['groups' => ['task', 'task_status', 'status', 'task_user', 'user', 'task_userStory', 'userStory']]);
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
            return $this->json("JSON incorrect", Response::HTTP_BAD_REQUEST);
        }

        $user_story = $this->userStoryRepository->find($data["user_story_id"]);
        $status = $this->statusRepository->find($data["status_id"]);

        // Check if user_story exists
        if (!$user_story) {
            return $this->json("No user story found", Response::HTTP_BAD_REQUEST);
        }

        // Check if Status exists
        if (!$status) {
            return $this->json("No Status found", Response::HTTP_BAD_REQUEST);
        }

        $task = new Task();
        $task->setName($data["name"]);
        $task->setUserStory($user_story);
        $task->setStatus($status);
        $this->taskRepository->add($task, true);

        return $this->json($task, Response::HTTP_CREATED, [],  ['groups' => ['task', 'task_status', 'status', 'task_user', 'user', 'task_userStory', 'userStory']]);
    }


    /* Edit Task */
    #[Route('task/{id}', name: 'edit_task', methods: ["PATCH"])]
    public function editTask(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $task = $this->taskRepository->find($id);

        // Check if task exists
        if (!$task) {
            return $this->json("This id is not found", Response::HTTP_BAD_REQUEST);
        }

        if (!empty($data["name"])) {
            $task->setName($data["name"]);
        }

        if (!empty($data["user_story"])) {
            $user_story = $this->userStoryRepository->find($data["user_story_id"]);
            // Check if user_story exists
            if (!$user_story) {
                return $this->json("No user story found", Response::HTTP_BAD_REQUEST);
            }
            $task->setUserStory($user_story);
        }

        if (!empty($data["status"])) {
            $status = $this->statusRepository->find($data["status_id"]);
            // Check if status exists
            if (!$status) {
                return $this->json("No status found", Response::HTTP_BAD_REQUEST);
            }
            $task->setUserStory($status);
        }

        $this->taskRepository->add($task, true);

        return $this->json($task, Response::HTTP_OK, [], ['groups' => ['task', 'task_status', 'status', 'task_user', 'user', 'task_userStory', 'userStory']]);
    }

    /* Hard Delete Task */
    #[Route('/task/{id}', name: 'delete_task', methods: ["DELETE"])]
    public function deleteTask(int $id): JsonResponse
    {
        $task = $this->taskRepository->find($id);

        // Check if task exists
        if (!$task) {
            return $this->json("This id is not found", Response::HTTP_BAD_REQUEST);
        }

        $this->statusRepository->remove($task, true);

        return $this->json($task, Response::HTTP_OK, [], ['groups' => ['task', 'task_status', 'status', 'task_user', 'user', 'task_userStory', 'userStory']]);
    }
}
