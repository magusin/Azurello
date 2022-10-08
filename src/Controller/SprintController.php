<?php

namespace App\Controller;

use App\Entity\Sprint;
use App\Context\ControllerContext;
use App\Repository\ProjectRepository;
use App\Repository\SprintRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SprintController extends ControllerContext
{
    private $sprintRepository;
    private $projectRepository;

    public function __construct(
        SprintRepository $sprintRepository,
        ProjectRepository $projectRepository
    ) {
        $this->sprintRepository = $sprintRepository;
        $this->projectRepository = $projectRepository;
    }


    /* List all Sprint */
    #[Route('/sprint-list', name: 'sprint_list', methods: ["HEAD", "GET"])]
    public function sprintList(): JsonResponse
    {
        $sprint = $this->sprintRepository->findAll();

        return $this->json($sprint, Response::HTTP_OK, [], ['groups' => ['sprint']]);
    }


    /* List all Sprint on details */
    #[Route('/sprint-list-details', name: 'sprint_list_details', methods: ["HEAD", "GET"])]
    public function sprintListDetails(): JsonResponse
    {
        $sprint =  $this->sprintRepository->findAll();

        return $this->json($sprint, Response::HTTP_OK, [], ['groups' => [
            'sprint',
            'sprint_project', 'project',
            'sprint_user', 'user',
            'sprint_ticket', 'ticket'
        ]]);
    }


    /* Specific Sprint details */
    #[Route('/sprint/{id}', name: 'sprint_details', methods: ["HEAD", "GET"])]
    public function sprint(int $id): JsonResponse
    {
        $sprint = $this->sprintRepository->find($id);

        // Check if sprint exists
        if (!$sprint) {
            return $this->json($this->errorMessageEntityNotFound("sprint"), Response::HTTP_BAD_REQUEST);
        }

        return $this->json($sprint, Response::HTTP_OK, [], ['groups' => [
            'sprint',
            'sprint_project', 'project',
            'sprint_user', 'user',
            'sprint_ticket', 'ticket'
        ]]);
    }


    /* Create Sprint */
    #[Route('/sprint', name: 'sprint_create', methods: ["POST"])]
    public function createSprint(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check JSON body
        if (
            empty($data["name"]) ||
            empty($data["start_date"]) ||
            empty($data["end_date"]) ||
            empty($data["project_id"])
        ) {
            return $this->json($this->errorMessageJsonBody(), Response::HTTP_BAD_REQUEST);
        }

        $project = $this->projectRepository->find($data["project_id"]);

        // Check if project exists
        if (!$project) {
            return $this->json($this->errorMessageEntityNotFound("project"), Response::HTTP_BAD_REQUEST);
        }

        $sprint = new Sprint();
        $sprint->setName($data["name"]);
        $sprint->setProject($project);
        $sprint->setStartDate(new \DateTime($data['start_date']));
        $sprint->setEndDate(new \DateTime($data['end_date']));
        $this->sprintRepository->add($sprint, true);

        return $this->json($sprint, Response::HTTP_CREATED, [], ['groups' => ['sprint']]);
    }


    /* Edit Sprint */
    #[Route('sprint/{id}', name: 'sprint_edit', methods: ["PATCH"])]
    public function editSprint(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $sprint = $this->sprintRepository->find($id);

        // Check if sprint exists
        if (!$sprint) {
            return $this->json($this->errorMessageEntityNotFound("sprint"), Response::HTTP_BAD_REQUEST);
        }

        if (!empty($data["name"])) {
            $sprint->setName($data["name"]);
        }

        if (!empty($data["start_date"])) {
            $sprint->setStartDate(new \DateTime($data['start_date']));
        }

        if (!empty($data["end_date"])) {
            $sprint->setEndDate(new \DateTime($data['end_date']));
        }

        $this->sprintRepository->add($sprint, true);

        return $this->json($sprint, Response::HTTP_OK, [], ['groups' => [
            'sprint',
            'sprint_project', 'project',
            'sprint_user', 'user',
            'sprint_ticket', 'ticket'
        ]]);
    }


    /* Hard Delete Sprint */
    #[Route('/sprint/{id}', name: 'sprint_delete', methods: ["DELETE"])]
    public function deleteSprint(int $id): JsonResponse
    {
        $sprint = $this->sprintRepository->find($id);

        // Check if project exists
        if (!$sprint) {
            return $this->json($this->errorMessageEntityNotFound("sprint"), Response::HTTP_BAD_REQUEST);
        }

        $this->sprintRepository->remove($sprint, true);

        return $this->json($this->successEntityDeleted("sprint"), Response::HTTP_OK);
    }
}
