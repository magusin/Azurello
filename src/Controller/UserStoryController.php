<?php

namespace App\Controller;

use App\Context\ControllerContext;
use App\Entity\UserStory;
use App\Repository\ProjectRepository;
use App\Repository\StatusRepository;
use DateTime;
use App\Repository\UserStoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserStoryController extends ControllerContext
{
    private $userStoryRepository;
    private $projectRepository;
    private $statusRepository;

    public function __construct(
        UserStoryRepository $userStoryRepository,
        ProjectRepository $projectRepository,
        StatusRepository $statusRepository
    ) {
        $this->userStoryRepository = $userStoryRepository;
        $this->projectRepository = $projectRepository;
        $this->statusRepository = $statusRepository;
    }


    /* List all userStory */
    #[Route('/userStories', name: 'userStory_list', methods: ["HEAD", "GET"])]
    public function userStoryList(): JsonResponse
    {
        $userStory = $this->userStoryRepository->findAllNotDeleted();

        return $this->json($userStory, Response::HTTP_OK, [], ['groups' => ['userStory']]);
    }


    /* List all UserStory on details */
    #[Route('/userStory_details', name: 'userStory_list_detail', methods: ["HEAD", "GET"])]
    public function userStoryDetails(): JsonResponse
    {
        $userStory = $this->userStoryRepository->findAllNotDeleted();

        return $this->json($userStory, Response::HTTP_OK, [], ['groups' => [
            'userStory',
            'userStory_group', 'group',
            'userStory_status', 'status',
            'userStory_sprint', 'sprint'
        ]]);
    }


    /* Specific UserStory details */
    #[Route('/userStory/{id}', name: 'userStory', methods: ["HEAD", "GET"])]
    public function UserStory(int $id): JsonResponse
    {
        $userStory = $this->userStoryRepository->find($id);

        // Check if userStory exists
        if (!$userStory) {
            return $this->json($this->errorMessageEntityNotFound("user_story"), Response::HTTP_BAD_REQUEST);
        }

        // Check if userStory is not deleted
        if ($userStory->getDeletedBy(!null)) {
            return $this->json($this->errorMessageEntityIsDeleted("user_story"), Response::HTTP_BAD_REQUEST);
        }

        return $this->json($userStory, Response::HTTP_OK, [], ['groups' => [
            'userStory',
            'userStory_group', 'group',
            'userStory_status', 'status',
            'userStory_sprint', 'sprint'
        ]]);
    }


    /* Create UserStory */
    #[Route('/userStory', name: 'create_userStory', methods: ["POST"])]
    public function createUserStory(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check JSON body
        if (
            empty($data["status_id"]) ||
            empty($data["name"]) ||
            empty($data["created_by"])
        ) {
            return $this->json($this->errorMessageJsonBody(), Response::HTTP_BAD_REQUEST);
        }

        $status = $this->statusRepository->find($data["status_id"]);

        // Check if status exists
        if (!$status) {
            return $this->json($this->errorMessageEntityNotFound("status"), Response::HTTP_BAD_REQUEST);
        }
        $userStory = new UserStory();
        $userStory->setName($data["name"]);
        $userStory->setStatus($status);
        $userStory->setCreatedAt(new DateTime());
        $userStory->setCreatedBy($data["created_by"]);
        $this->userStoryRepository->add($userStory, true);

        return $this->json($userStory, Response::HTTP_CREATED, [], ['groups' => [
            'userStory',
            'userStory_status', 'status',
            'userStory_group', 'group',
            'userStory_project', 'project'
        ]]);
    }


    /* Edit UserStory */
    #[Route('userStory/{id}', name: 'edit_userStory', methods: ["PATCH"])]
    public function editUserStory(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userStory = $this->userStoryRepository->find($id);

        // Check JSON body
        if (
            empty($data["updated_by"])
        ) {
            return $this->json($this->errorMessageJsonBody(), Response::HTTP_BAD_REQUEST);
        }

        // Check if userStory exists
        if (!$userStory) {
            return $this->json($this->errorMessageEntityNotFound("user_story"), Response::HTTP_BAD_REQUEST);
        }

        // Check if userStory is not deleted
        if ($userStory->getDeletedBy(!null)) {
            return $this->json($this->errorMessageEntityIsDeleted("user_story"), Response::HTTP_BAD_REQUEST);
        }

        if (!empty($data['name'])) {
            $userStory->setName($data["name"]);
        }
        $userStory->setUpdatedAt(new DateTime());
        $userStory->setUpdatedBy($data["updated_by"]);
        $this->userStoryRepository->add($userStory, true);

        return $this->json($userStory, Response::HTTP_OK, [], ['groups' => [
            'userStory',
            'userStory_status', 'status',
            'userStory_group', 'group',
            'userStory_project', 'project'
        ]]);
    }


    /* Soft Delete userStory */
    #[Route('/userStory/{id}', name: 'delete_userStory', methods: ["DELETE"])]
    public function deleteUserStory(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userStory = $this->userStoryRepository->find($id);

        // Check JSON body
        if (
            empty($data["deleted_by"])
        ) {
            return $this->json($this->errorMessageJsonBody(), Response::HTTP_BAD_REQUEST);
        }

        // Check if userStory exists
        if (!$userStory) {
            return $this->json($this->errorMessageEntityNotFound("user_story"), Response::HTTP_BAD_REQUEST);
        }

        // Check if userStory is not deleted
        if ($userStory->getDeletedBy(!null)) {
            return $this->json($this->errorMessageEntityIsDeleted("user_story"), Response::HTTP_BAD_REQUEST);
        }

        $userStory->setDeletedAt(new DateTime());
        $userStory->setDeletedBy($data["deleted_by"]);
        $this->userStoryRepository->add($userStory, true);

        return $this->json($userStory, Response::HTTP_ACCEPTED, [], ['groups' => [
            'userStory',
            'userStory_status', 'status',
            'userStory_group', 'group',
            'userStory_project', 'project'
        ]]);
    }
}
