<?php

namespace App\Controller;

use DateTime;
use App\Entity\Sprint;
use App\Repository\SprintRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class SprintController extends AbstractController
{
    private $sprintRepository;
    private $userRepository;

    public function __construct(SprintRepository $sprintRepository, UserRepository $userRepository)
    {
        $this->sprintRepository = $sprintRepository;
        $this->userRepository = $userRepository;
    }


    /* List all Sprint */
    #[Route('/sprints', name: 'sprint_list', methods: ["HEAD", "GET"])]
    public function sprintList(): JsonResponse
    {
        $sprint = $this->sprintRepository->findAll();

        return $this->json($sprint, Response::HTTP_OK, [], ['groups' => ['sprint']]);
    }


    /* List all Sprint on details */
    #[Route('/sprints_details', name: 'sprint_list_details', methods: ["HEAD", "GET"])]
    public function sprintListDetails(): JsonResponse
    {
        $sprint =  $this->sprintRepository->findAll();

        return $this->json($sprint, Response::HTTP_OK, [], ['groups' => ['sprint', 'sprint_user', 'user']]);
    }


    /* Specific Sprint details */
    #[Route('/sprint/{id}', name: 'sprint', methods: ["HEAD", "GET"])]
    public function sprint(int $id): JsonResponse
    {
        $sprint = $this->sprintRepository->find($id);

        // Check if sprint exists
        if (!$sprint) {
            return $this->json("No sprint found", Response::HTTP_BAD_REQUEST);
        }

        return $this->json($sprint, Response::HTTP_OK, [], ['groups' => ['sprint', 'sprint_user', 'user']]);
    }


    /* Create Sprint */
    #[Route('/sprint', name: 'create_sprint', methods: ["POST"])]
    public function createSprint(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check JSON body
        if (
            empty($data["name"]) ||
            empty($data["user_creator_id"]) ||
            empty($data["start_date"]) ||
            empty($data["end_date"])
        ) {
            return $this->json("JSON incorrect", Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->find($data["user_creator_id"]);

        // Check if user exists
        if (!$user) {
            return $this->json("No user found", Response::HTTP_BAD_REQUEST);
        }

        $sprint = new Sprint();
        $sprint->setSprintName($data["name"]);
        $sprint->setUserCreator($user);
        $sprint->setStartDate(new \DateTime($data["start_date"]));
        $sprint->setEndDate(new \DateTime($data["end_date"]));
        $this->sprintRepository->add($sprint, true);

        return $this->json($sprint, Response::HTTP_CREATED, [], ['groups' => ['sprint', 'sprint_user', 'user']]);
    }

    /* Hard Delete Sprint */
    #[Route('/sprint/{id}', name: 'delete_sprint', methods: ["DELETE"])]
    public function deleteSprint(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $sprint = $this->sprintRepository->find($id);

        // Check if project exists
        if (!$sprint) {
            return $this->json("This id is not found", Response::HTTP_BAD_REQUEST);
        }
 
        // $project->setDeletedAt(new DateTime());
        // $project->setDeletedBy($data["deleted_by"]);
        // $this->projectRepository->add($project, true);
 
        // return $this->json($project, Response::HTTP_ACCEPTED);
    }
}
