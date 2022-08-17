<?php

namespace App\Controller;

use App\Repository\SprintRepository;
use App\Repository\UserStoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserStorySprintController extends AbstractController
{
    private $sprintRepository;
    private $userStoryRepository;

    public function __construct(SprintRepository $sprintRepository, UserStoryRepository $userStoryRepository)
    {
        $this->sprintRepository = $sprintRepository;
        $this->userRepository = $userStoryRepository;
    }

    /* Create user_story_sprint */
    #[Route('/user_story_sprint', name: 'create_user_story_sprint', methods: ["POST"])]
    public function createUserStorySprint(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check JSON body
        if (
            empty($data["user_story_id"]) ||
            empty($data["sprint_id"])
        ) {
            return $this->json("JSON incorrect", Response::HTTP_BAD_REQUEST);
        }

        $user_story = $this->userStoryRepository->find($data["user_story_id"]);
        $sprint = $this->sprintRepository->find($data["sprint_id"]);

        // Check if user_story exists
        if (!$user_story) {
            return $this->json("This user_story is not found", Response::HTTP_BAD_REQUEST);
        }

        // Check if sprint exists
        if (!$sprint) {
            return $this->json("This sprint is not found", Response::HTTP_BAD_REQUEST);
        }

        // Check if this relation already exist
        if ($sprint->getUserStories()->contains($user_story)) {
            return $this->json("This user_story_sprint already exist", Response::HTTP_BAD_REQUEST);
        }

        $sprint->addUserStory($user_story);
        $this->sprintRepository->add($sprint, true);

        return $this->json($sprint, Response::HTTP_CREATED, [], ['groups' => ['sprint', 'sprint_user', 'user']]);
    }
}
