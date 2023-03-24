<?php

namespace App\Controller;

use App\Context\ControllerContext;
use App\Entity\TicketType;
use App\Repository\ProjectRepository;
use App\Repository\TicketRepository;
use App\Repository\TicketTypeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class TicketTypeController extends ControllerContext
{
    private $ticketRepository;
    private $ticketTypeRepository;
    private $projectRepository;

    public function __construct(
        TicketRepository $ticketRepository,
        TicketTypeRepository $ticketTypeRepository,
        ProjectRepository $projectRepository
    ) {
        $this->ticketRepository = $ticketRepository;
        $this->ticketTypeRepository = $ticketTypeRepository;
        $this->projectRepository = $projectRepository;
    }


    /* List all ticketType */
    #[Route('/ticket-type-list', name: 'ticketType_list', methods: ["HEAD", "GET"])]
    public function ticketTypeList(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check JSON body
        if (
            empty($data["project_id"])
        ) {
            return $this->json($this->errorMessageJsonBody(), Response::HTTP_BAD_REQUEST);
        }
        $project = $this->projectRepository->find($data["project_id"]);

        $ticketTypeList = $project->getTicketTypes();

        return $this->json($ticketTypeList, Response::HTTP_OK, [], ['groups' => ['ticketType']]);
    }


    /* List all ticketType on details */
    #[Route('/ticket-type-list-details', name: 'ticketType_list_details', methods: ["HEAD", "GET"])]
    public function ticketTypeDetails(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check JSON body
        if (
            empty($data["project_id"])
        ) {
            return $this->json($this->errorMessageJsonBody(), Response::HTTP_BAD_REQUEST);
        }
        $project = $this->projectRepository->find($data["project_id"]);

        $ticketTypeList = $project->getTicketTypes();

        return $this->json($ticketTypeList, Response::HTTP_OK, [], ['groups' => [
            'ticketType',
            'ticketType_ticket', 'ticket',
            'ticketType_project', 'project'
        ]]);
    }


    /* Specific ticketType details */
    #[Route('/ticket-type/{id}', name: 'ticketType_details', methods: ["HEAD", "GET"])]
    public function ticketType(int $id): JsonResponse
    {
        $ticketType = $this->ticketTypeRepository->find($id);

        // Check if ticketType exists
        if (!$ticketType) {
            return $this->json($this->errorMessageEntityNotFound("ticketType"), Response::HTTP_BAD_REQUEST);
        }

        return $this->json($ticketType, Response::HTTP_OK, [], ['groups' => [
            'ticketType',
            'ticketType_ticket', 'ticket',
            'ticketType_project', 'project'
        ]]);
    }


    /* Create ticketType */
    #[Route('/ticket-type', name: 'ticketType_create', methods: ["POST"])]
    public function createTicketType(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check JSON body
        if (
            empty($data["name"]) ||
            empty($data["project_id"])
        ) {
            return $this->json($this->errorMessageJsonBody(), Response::HTTP_BAD_REQUEST);
        }

        $project = $this->projectRepository->find($data["project_id"]);

        // Check if project exists
        if (!$project) {
            return $this->json($this->errorMessageEntityNotFound("project"), Response::HTTP_BAD_REQUEST);
        }

        // Check if project is not deleted
        if ($project->getDeletedBy(!null)) {
            return $this->json($this->errorMessageEntityIsDeleted("project"), Response::HTTP_BAD_REQUEST);
        }

        $ticketType = new TicketType();
        $ticketType->setProject($project);
        $ticketType->setName($data["name"]);
        $this->ticketTypeRepository->add($ticketType, true);

        return $this->json($ticketType, Response::HTTP_CREATED, [], ['groups' => [
            'ticketType',
            'ticketType_ticket', 'ticket',
            'ticketType_project', 'project'
        ]]);
    }


    /* Edit ticketType */
    #[Route('ticket-type/{id}', name: 'ticketType_edit', methods: ["PATCH"])]
    public function editTicketType(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $ticketType = $this->ticketTypeRepository->find($id);

        // Check if ticketType exists
        if (!$ticketType) {
            return $this->json($this->errorMessageEntityNotFound("ticketType"), Response::HTTP_BAD_REQUEST);
        }

        if (!empty($data['name'])) {
            $ticketType->setName($data["name"]);
        }

        $this->ticketTypeRepository->add($ticketType, true);

        return $this->json($ticketType, Response::HTTP_OK, [], ['groups' => [
            'ticketType',
            'ticketType_ticket', 'ticket',
            'ticketType_project', 'project'
        ]]);
    }


    /* Hard Delete ticketType */
    #[Route('/ticket-type/{id}', name: 'ticketType_delete', methods: ["DELETE"])]
    public function deleteTicketType(int $id): JsonResponse
    {
        $ticketType = $this->ticketTypeRepository->find($id);

        // Check if ticketType exists
        if (!$ticketType) {
            return $this->json($this->errorMessageEntityNotFound("ticketType"), Response::HTTP_BAD_REQUEST);
        }

        $this->ticketTypeRepository->remove($ticketType, true);

        return $this->json($this->successEntityDeleted("ticketType"), Response::HTTP_OK);
    }
}
