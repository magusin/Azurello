<?php

namespace App\Controller;

use App\Entity\UserStory;
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

    public function __construct(UserStoryRepository $userStoryRepository)
    {
        $this->userStoryRepository = $userStoryRepository;
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
            empty($data["name"]) ||
            empty($data["created_by"])
        ) {
            return $this->json("JSON incorrect", Response::HTTP_BAD_REQUEST);
        }

        $userStory = new UserStory();
        $userStory->setName($data["name"]);
        $userStory->setCreatedAt(new DateTime());
        $userStory->setCreatedBy($data["created_by"]);
        $this->userStoryRepository->add($userStory, true);

        return $this->json($userStory, Response::HTTP_CREATED);
    }

}
