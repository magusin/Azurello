<?php

namespace App\Controller;

use DateTime;
use App\Context\ControllerContext;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\TaskRepository;
use App\Repository\SprintRepository;
use App\Repository\UserProjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends ControllerContext
{
    private $userRepository;

    public function __construct(
        UserRepository $userRepository,
        TaskRepository $taskRepository,
        SprintRepository $sprintRepository,
        UserProjectRepository $userProjectRepository
    ) {
        $this->userRepository = $userRepository;
        $this->taskRepository = $taskRepository;
        $this->sprintRepository = $sprintRepository;
        $this->userProjectRepository = $userProjectRepository;
    }

    
    /* List all User */
    #[Route('/users', name: 'user_list', methods: ["HEAD", "GET"])]
    public function userList(): JsonResponse
    {
        $user = $this->userRepository->findAll();

        return $this->json($user, Response::HTTP_OK, [], ['groups' => ['user']]);
    }


    /* List all Users on details */
    #[Route('/users_details', name: 'user_list_details', methods: ["HEAD", "GET"])]
    public function userListDetails(): JsonResponse
    {
        $user = $this->userRepository->findAll();

        return $this->json($user, Response::HTTP_OK, [], ['groups' => [
            'user',
            // 'user_sprint', 'sprint',
            // 'user_task', 'task',
            'user_userProject', 'userProject'
        ]]);
    }

    /* Specific User details */
    #[Route('/user/{id}', name: 'user', methods: ["HEAD", "GET"])]
    public function user(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);

        // Check if user exists
        if (!$user) {
            return $this->json($this->errorMessageEntityNotFound("user"), Response::HTTP_BAD_REQUEST);
        }
        return $this->json($user, Response::HTTP_OK, [], ['groups' => [
            'user',
            // 'user_sprint', 'sprint',
            // 'user_task', 'task',
            'user_userProject', 'userProject'
        ]]);
    }


    /* Create user */
    #[Route('/user', name: 'create_user', methods: ["POST"])]
    public function createUser(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check JSON body
        if (
            empty($data["email"]) ||
            empty($data["password"]) ||
            empty($data["firstname"]) ||
            empty($data["lastname"])
        ) {
            return $this->json($this->errorMessageJsonBody(), Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setEmail($data["email"]);
        if (!empty($data["roles"])) {
            $user->setRoles($data["roles"]);
        }
        $user->setPassword($data["password"]);
        $user->setFirstname($data["firstname"]);
        $user->setLastname($data["lastname"]);
        $user->setRegistrationAt(new DateTime());
        $this->userRepository->add($user, true);

        return $this->json($user, Response::HTTP_CREATED, [],  ['groups' => [
            'user',
            'user_sprint', 'sprint',
            'user_task', 'task',
            'user_userProject', 'userProject'
        ]]);
    }


    /* Edit User */
    #[Route('user/{id}', name: 'edit_user', methods: ["PATCH"])]
    public function editUser(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->userRepository->find($id);

        // Check if user exists
        if (!$user) {
            return $this->json($this->errorMessageEntityNotFound("user"), Response::HTTP_BAD_REQUEST);
        }

        if (!empty($data["email"])) {
            $user->setEmail($data["email"]);
        }

        if (!empty($data["password"])) {
            $user->setPassword($data["password"]);
        }

        if (!empty($data["lastname"])) {
            $user->setLastname($data["lastname"]);
        }

        if (!empty($data["firstname"])) {
            $user->setFirstname($data["firstname"]);
        }

        if (!empty($data["task_id"])) {
            $task = $this->taskRepository->find($data["task_id"]);
            // Check if task exists
            if (!$task) {
                return $this->json($this->errorMessageEntityNotFound("task"), Response::HTTP_BAD_REQUEST);
            }
            $user->addTask($task);
        }

        if (!empty($data["sprint_id"])) {
            $sprint = $this->sprintRepository->find($data["sprint_id"]);
            // Check if sprint exists
            if (!$sprint) {
                return $this->json($this->errorMessageEntityNotFound("sprint"), Response::HTTP_BAD_REQUEST);
            }
            $user->addSprint($sprint);
        }

        if (!empty($data["user_project_id"])) {
            $userProject = $this->userProjectRepository->find($data["user_project_id"]);
            // Check if userProject exists
            if (!$userProject) {
                return $this->json($this->errorMessageEntityNotFound("user_project"), Response::HTTP_BAD_REQUEST);
            }
            $user->addUserProject($userProject);
        }

        $this->userRepository->add($user, true);

        return $this->json($user, Response::HTTP_OK, [], ['groups' => [
            'user',
            // 'user_sprint', 'sprint',
            // 'user_task', 'task',
            'user_userProject', 'userProject'
        ]]);
    }


    /* Hard Delete User */
    #[Route('/user/{id}', name: 'delete_user', methods: ["DELETE"])]
    public function deleteUser(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);

        // Check if user exists
        if (!$user) {
            return $this->json($this->errorMessageEntityNotFound("user"), Response::HTTP_BAD_REQUEST);
        }

        $this->userRepository->remove($user, true);

        return $this->json($user, Response::HTTP_OK, [], ['groups' => [
            'user',
            // 'user_sprint', 'sprint',
            // 'user_task', 'task',
            'user_userProject', 'userProject'
        ]]);
    }
}
