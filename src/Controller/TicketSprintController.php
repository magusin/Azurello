<?php

namespace App\Controller;

use App\Context\ControllerContext;
use App\Repository\SprintRepository;
use App\Repository\TicketRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TicketSprintController extends ControllerContext
{
    private $sprintRepository;
    private $ticketRepository;

    public function __construct(
        SprintRepository $sprintRepository,
        TicketRepository $ticketRepository
    ) {
        $this->sprintRepository = $sprintRepository;
        $this->ticketRepository = $ticketRepository;
    }

    /* Create sprint_sprint */
    #[Route('/sprint-ticket', name: 'sprint_sprint_create', methods: ["POST"])]
    public function createTicketSprint(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check JSON body
        if (
            empty($data["ticket_id"]) ||
            empty($data["sprint_id"])
        ) {
            return $this->json($this->errorMessageJsonBody(), Response::HTTP_BAD_REQUEST);
        }

        $ticket = $this->ticketRepository->find(1);
        $sprint = $this->sprintRepository->find($data["sprint_id"]);

        // Check if ticket exists
        if (!$ticket) {
            return $this->json($this->errorMessageEntityNotFound("ticket"), Response::HTTP_BAD_REQUEST);
        }

        // Check if sprint exists
        if (!$sprint) {
            return $this->json($this->errorMessageEntityNotFound("sprint"), Response::HTTP_BAD_REQUEST);
        }

        // Check if this relation already exist
        if ($sprint->getTickets()->contains($ticket)) {
            return $this->json($this->errorMessageRelationAlreadyExist("sprint", "ticket"), Response::HTTP_BAD_REQUEST);
        }

        // /* Remove relation */
        // if ($data["parent_id"] == 'remove') {
        //     $levelGroup->removeParent();
        // }

        // /* Add levelGroup parent */
        // if ($data["parent_id"] != 'remove') {
        //     $parent = $this->levelGroupRepository->find($data["parent_id"]);
        //     // Check if levelGroup parent exists
        //     if (!$parent) {
        //         return $this->json($this->errorMessageEntityNotFound("levelGroup"), Response::HTTP_BAD_REQUEST);
        //     }
        //     // Check if parent isnt itself
        //     if ($parent->getId() == $levelGroup->getId()) {
        //         return $this->json($this->errorMessageRelationItself("levelGroup"), Response::HTTP_BAD_REQUEST);
        //     }
        //     $levelGroup->setParent($parent);
        // }

        $sprint->addTicket($ticket);
        $this->sprintRepository->add($sprint, true);

        return $this->json($sprint, Response::HTTP_CREATED, [], ['groups' => [
            'sprint',
            'sprint_user', 'user',
            'sprint_ticket', 'ticket'
        ]]);
    }

    /* delete sprint_sprint */
    #[Route('/sprint-ticket', name: 'sprint_sprint_delete', methods: ["DELETE"])]
    public function delete(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check JSON body
        if (
            empty($data["ticket_id"]) ||
            empty($data["sprint_id"])
        ) {
            return $this->json($this->errorMessageJsonBody(), Response::HTTP_BAD_REQUEST);
        }

        $ticket = $this->ticketRepository->find(1);
        $sprint = $this->sprintRepository->find($data["sprint_id"]);

        // Check if ticket exists
        if (!$ticket) {
            return $this->json($this->errorMessageEntityNotFound("ticket"), Response::HTTP_BAD_REQUEST);
        }

        // Check if sprint exists
        if (!$sprint) {
            return $this->json($this->errorMessageEntityNotFound("sprint"), Response::HTTP_BAD_REQUEST);
        }

        // Check if this relation exist
        if (!$sprint->getTickets()->contains($ticket)) {
            return $this->json($this->errorMessageRelationNotExist("sprint", "ticket"), Response::HTTP_BAD_REQUEST);
        }

        $sprint->removeTicket($ticket);

        $this->sprintRepository->add($sprint, true);

        return $this->json($this->successEntityDeleted("sprint_sprint"), Response::HTTP_OK);
    }
}
