<?php

namespace App\Controller;

use App\Entity\UserStory;
use App\Repository\ProjectRepository;
use App\Repository\StatusRepository;
use DateTime;
use App\Repository\UserStoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserStoryController extends AbstractController
{
    private $userStoryRepository;
    private $projectRepository;
    private $statusRepository;

    public function __construct(UserStoryRepository $userStoryRepository, ProjectRepository $projectRepository, StatusRepository $statusRepository)
    {
        $this->userStoryRepository = $userStoryRepository;
        $this->projectRepository = $projectRepository;
        $this->statusRepository = $statusRepository;
    }

    /* List all userStory */
    #[Route('/userStories', name: 'userStory_list', methods: ["HEAD", "GET"])]
    public function userStoryList(): JsonResponse
    {
        $userStory = $this ->userStoryRepository->findAllNotDeleted();

        return $this->json($userStory, Response::HTTP_OK, [], ['groups' => ['userStory']]);
    }

     /* List all UserStory on details */
    #[Route('/userStory_details', name: 'userStory_list_detail', methods: ["HEAD", "GET"])]
    public function userStoryDetails(): JsonResponse
    {
        $userStory = $this->userStoryRepository->findAllNotDeleted();

        return $this->json($userStory, Response::HTTP_OK, [], ['groups' => ['userStory', 'userStory_user', 'user', 'userStory_group', 'group', 'userStory_status', 'status']]);
    }

    /* Specific UserStory details */
    #[Route('/userStory/{id}', name: 'userStory', methods: ["HEAD", "GET"])]
    public function UserStory(int $id): JsonResponse
    {
        $userStory = $this->userStoryRepository->find($id);

        // Check if userStory exists
        if (!$userStory) {
            return $this->json("No userStory found", Response::HTTP_BAD_REQUEST);
        }
        
        // Check if userStory is not deleted
        if ($userStory->getDeletedBy(!null)) {
            return $this->json("This UserStory is deleted", Response::HTTP_BAD_REQUEST);
        }

        return $this->json($userStory, Response::HTTP_OK, [], ['groups' => ['userStory', 'userStory_user', 'user', 'userStory_group', 'group', 'userStory_status', 'status']]);
    }

    /* Create UserStory */
    #[Route('/userStory', name: 'create_userStory', methods: ["POST"])]
    public function createUserStory(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check JSON body
        if (
            empty($data["status_id"]) ||
            empty($data["project_id"]) ||
            empty($data["name"]) ||
            empty($data["created_by"])
        ) {
            return $this->json("JSON incorrect", Response::HTTP_BAD_REQUEST);
        }
        $status = $this->statusRepository->find($data["status_id"]);
        $project = $this->projectRepository->find($data["project_id"]);

         // Check if project exists
        if (!$project) {
            return $this->json("No project found", Response::HTTP_BAD_REQUEST);
        }
        if (!$status) {
            return $this->json("No status found", Response::HTTP_BAD_REQUEST);
        }
        $userStory = new UserStory();
        $userStory->setName($data["name"]);
        $userStory->setProject($project);
        $userStory->setStatus($status);
        $userStory->setCreatedAt(new DateTime());
        $userStory->setCreatedBy($data["created_by"]);
        $this->userStoryRepository->add($userStory, true);

        return $this->json($userStory, Response::HTTP_CREATED, [], ['groups' => ['userStory', 'userStory_status', 'status', 'userStory_group', 'group','userStory_project', 'project']]);
    }

}
