<?php

namespace App\Controller;

use DateTime;
use App\Context\ControllerContext;
use App\Entity\Ticket;
use App\Repository\LevelGroupRepository;
use App\Repository\StatusRepository;
use App\Repository\TicketRepository;
use App\Repository\TicketTypeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class TicketController extends ControllerContext
{
    private $ticketRepository;
    private $statusRepository;
    private $levelGroupRepository;

    public function __construct(
        TicketRepository $ticketRepository,
        StatusRepository $statusRepository,
        LevelGroupRepository $levelGroupRepository,
        TicketTypeRepository $ticketTypeRepository
    ) {
        $this->ticketRepository = $ticketRepository;
        $this->statusRepository = $statusRepository;
        $this->levelGroupRepository = $levelGroupRepository;
        $this->ticketTypeRepository = $ticketTypeRepository;
    }


    /* List all ticket */
    #[Route('/ticket-list', name: 'ticket_list', methods: ["HEAD", "GET"])]
    public function ticketList(): JsonResponse
    {
        $ticket = $this->ticketRepository->findAllNotDeleted();

        return $this->json($ticket, Response::HTTP_OK, [], ['groups' => ['ticket']]);
    }


    /* List all Ticket on details */
    #[Route('/ticket-list-details', name: 'ticket_list_details', methods: ["HEAD", "GET"])]
    public function ticketDetails(): JsonResponse
    {
        $ticket = $this->ticketRepository->findAllNotDeleted();

        return $this->json($ticket, Response::HTTP_OK, [], ['groups' => [
            'ticket',
            'ticket_levelGroup', 'levelGroup',
            'ticket_status', 'status',
            'ticket_sprint', 'sprint',
            'ticket_type', 'type',
            'ticket_user', 'user',
            'ticket_task', 'task'
        ]]);
    }


    /* Specific Ticket details */
    #[Route('/ticket/{id}', name: 'ticket_details', methods: ["HEAD", "GET"])]
    public function ticket(int $id): JsonResponse
    {
        $ticket = $this->ticketRepository->find($id);

        // Check if ticket exists
        if (!$ticket) {
            return $this->json($this->errorMessageEntityNotFound("ticket"), Response::HTTP_BAD_REQUEST);
        }

        // Check if ticket is not deleted
        if ($ticket->getDeletedBy(!null)) {
            return $this->json($this->errorMessageEntityIsDeleted("ticket"), Response::HTTP_BAD_REQUEST);
        }

        return $this->json($ticket, Response::HTTP_OK, [], ['groups' => [
            'ticket',
            'ticket_levelGroup', 'levelGroup',
            'ticket_status', 'status',
            'ticket_sprint', 'sprint',
            'ticket_type', 'type',
            'ticket_user', 'user',
            'ticket_task', 'task'
        ]]);
    }


    /* Create Ticket */
    #[Route('/ticket', name: 'ticket_create', methods: ["POST"])]
    public function createTicket(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check JSON body
        if (
            empty($data["name"]) ||
            empty($data["created_by"]) ||
            empty($data["status_id"]) ||
            empty($data["level_group_id"]) ||
            empty($data["ticket_type_id"])
        ) {
            return $this->json($this->errorMessageJsonBody(), Response::HTTP_BAD_REQUEST);
        }

        $status = $this->statusRepository->find($data["status_id"]);
        // Check if status exists
        if (!$status) {
            return $this->json($this->errorMessageEntityNotFound("status"), Response::HTTP_BAD_REQUEST);
        }

        $levelGroup = $this->levelGroupRepository->find($data["level_group_id"]);
        // Check if level group exists
        if (!$levelGroup) {
            return $this->json($this->errorMessageEntityNotFound("levelGroup"), Response::HTTP_BAD_REQUEST);
        }

        $ticketType = $this->ticketTypeRepository->find($data["ticket_type_id"]);
        // Check if ticket type exists
        if (!$ticketType) {
            return $this->json($this->errorMessageEntityNotFound("ticketType"), Response::HTTP_BAD_REQUEST);
        }

        $ticket = new Ticket();
        $ticket->setName($data["name"]);
        $ticket->setStatus($status);
        $ticket->setTicketType($ticketType);
        $ticket->setLevelGroup($levelGroup);
        $ticket->setCreatedAt(new DateTime());
        $ticket->setCreatedBy($data["created_by"]);
        $this->ticketRepository->add($ticket, true);

        return $this->json($ticket, Response::HTTP_CREATED, [], ['groups' => [
            'ticket',
            'ticket_levelGroup', 'levelGroup',
            'ticket_status', 'status',
            'ticket_sprint', 'sprint',
            'ticket_type', 'type',
            'ticket_user', 'user',
            'ticket_task', 'task'
        ]]);
    }


    /* Edit Ticket */
    #[Route('ticket/{id}', name: 'ticket_edit', methods: ["PATCH"])]
    public function editTicket(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $ticket = $this->ticketRepository->find($id);

        // Check JSON body
        if (
            empty($data["updated_by"])
        ) {
            return $this->json($this->errorMessageJsonBody(), Response::HTTP_BAD_REQUEST);
        }

        // Check if ticket exists
        if (!$ticket) {
            return $this->json($this->errorMessageEntityNotFound("ticket"), Response::HTTP_BAD_REQUEST);
        }

        // Check if ticket is not deleted
        if ($ticket->getDeletedBy(!null)) {
            return $this->json($this->errorMessageEntityIsDeleted("ticket"), Response::HTTP_BAD_REQUEST);
        }

        if (!empty($data['name'])) {
            $ticket->setName($data["name"]);
        }

        if (!empty($data['status_id'])) {
            $status = $this->statusRepository->find($data["status_id"]);
            // Check if status exists
            if (!$status) {
                return $this->json($this->errorMessageEntityNotFound("status"), Response::HTTP_BAD_REQUEST);
            }
            $ticket->setStatus($status);
        }

        if (!empty($data['level_group_id'])) {
            $levelGroup = $this->levelGroupRepository->find($data["lvel_group_id"]);
            // Check if levelGroup exists
            if (!$levelGroup) {
                return $this->json($this->errorMessageEntityNotFound("levelGroup"), Response::HTTP_BAD_REQUEST);
            }
            $ticket->setLevelGroup($levelGroup);
        }

        $ticket->setUpdatedAt(new DateTime());
        $ticket->setUpdatedBy($data["updated_by"]);
        $this->ticketRepository->add($ticket, true);

        return $this->json($ticket, Response::HTTP_OK, [], ['groups' => [
            'ticket',
            'ticket_levelGroup', 'levelGroup',
            'ticket_status', 'status',
            'ticket_sprint', 'sprint',
            'ticket_type', 'type',
            'ticket_user', 'user',
            'ticket_task', 'task'
        ]]);
    }


    /* Soft Delete ticket */
    #[Route('/ticket/{id}', name: 'ticket_delete', methods: ["DELETE"])]
    public function deleteTicket(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $ticket = $this->ticketRepository->find($id);

        // Check JSON body
        if (
            empty($data["deleted_by"])
        ) {
            return $this->json($this->errorMessageJsonBody(), Response::HTTP_BAD_REQUEST);
        }

        // Check if ticket exists
        if (!$ticket) {
            return $this->json($this->errorMessageEntityNotFound("ticket"), Response::HTTP_BAD_REQUEST);
        }

        // Check if ticket is not deleted
        if ($ticket->getDeletedBy(!null)) {
            return $this->json($this->errorMessageEntityIsDeleted("ticket"), Response::HTTP_BAD_REQUEST);
        }

        $ticket->setDeletedAt(new DateTime());
        $ticket->setDeletedBy($data["deleted_by"]);
        $this->ticketRepository->add($ticket, true);

        return $this->json($this->successEntityDeleted("ticket"), Response::HTTP_OK);
    }
}
