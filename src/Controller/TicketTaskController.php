<?php

namespace App\Controller;

use App\Context\ControllerContext;
use App\Entity\TicketTask;
use App\Repository\StatusRepository;
use App\Repository\TicketTaskRepository;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class TicketTaskController extends ControllerContext
{
    private $ticketTaskRepository;
    private $ticketRepository;
    private $statusRepository;
    private $userRepository;

    public function __construct(
        UserRepository $userRepository,
        TicketTaskRepository $ticketTaskRepository,
        TicketRepository $ticketRepository,
        StatusRepository $statusRepository
    ) {
        $this->userRepository = $userRepository;
        $this->ticketTaskRepository = $ticketTaskRepository;
        $this->ticketRepository = $ticketRepository;
        $this->statusRepository = $statusRepository;
    }


    /* List all TicketTask */
    #[Route('/ticket-task-list', name: 'ticketTask_list', methods: ["HEAD", "GET"])]
    public function ticketTaskList(): JsonResponse
    {
        $ticketTask = $this->ticketTaskRepository->findAll();

        return $this->json($ticketTask, Response::HTTP_OK, [], ['groups' => ['ticketTask']]);
    }

    /* List all TicketTasks on details */
    #[Route('/ticket-task-list-details', name: 'ticketTask_list_details', methods: ["HEAD", "GET"])]
    public function ticketTaskListDetails(): JsonResponse
    {
        $ticketTask = $this->ticketTaskRepository->findAll();

        return $this->json($ticketTask, Response::HTTP_OK, [], ['groups' => [
            'ticketTask',
            'ticketTask_status', 'status',
            'ticketTask_user', 'user',
            'ticketTask_ticket', 'ticket'
        ]]);
    }


    /* Specific ticketTask details */
    #[Route('/ticket-task/{id}', name: 'ticketTask_details', methods: ["HEAD", "GET"])]
    public function ticketTask(int $id): JsonResponse
    {
        $ticketTask = $this->ticketTaskRepository->find($id);

        // Check if ticketTask exists
        if (!$ticketTask) {
            return $this->json($this->errorMessageEntityNotFound("ticketTask"), Response::HTTP_BAD_REQUEST);
        }
        return $this->json($ticketTask, Response::HTTP_OK, [], ['groups' => [
            'ticketTask',
            'ticketTask_status', 'status',
            'ticketTask_user', 'user',
            'ticketTask_ticket', 'ticket'
        ]]);
    }


    /* Create ticketTask */
    #[Route('/ticket-task', name: 'ticketTask_create', methods: ["POST"])]
    public function createTicketTask(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check JSON body
        if (
            empty($data["name"]) ||
            empty($data["ticket_id"]) ||
            empty($data["status_id"])
        ) {
            return $this->json($this->errorMessageJsonBody(), Response::HTTP_BAD_REQUEST);
        }

        $ticket = $this->ticketRepository->find($data["ticket_id"]);
        $status = $this->statusRepository->find($data["status_id"]);

        // Check if ticket exists
        if (!$ticket) {
            return $this->json($this->errorMessageEntityNotFound("ticket"), Response::HTTP_BAD_REQUEST);
        }

        // Check if Status exists
        if (!$status) {
            return $this->json($this->errorMessageEntityNotFound("status"), Response::HTTP_BAD_REQUEST);
        }

        $ticketTask = new TicketTask();

        if (!empty($data["user_id"])) {
            $user = $this->userRepository->find($data["user_id"]);
            // Check if user exists
            if (!$status) {
                return $this->json($this->errorMessageEntityNotFound("user"), Response::HTTP_BAD_REQUEST);
            }
            $ticketTask->setUser($user);
        }
        
        $ticketTask->setName($data["name"]);
        $ticketTask->setTicket($ticket);
        $ticketTask->setStatus($status);
        $this->ticketTaskRepository->add($ticketTask, true);

        return $this->json($ticketTask, Response::HTTP_CREATED, [], ['groups' => [
            'ticketTask',
            'ticketTask_status', 'status',
            'ticketTask_user', 'user',
            'ticketTask_ticket', 'ticket'
        ]]);
    }


    /* Edit TicketTask */
    #[Route('ticket-task/{id}', name: 'ticketTask_edit', methods: ["PATCH"])]
    public function editTicketTask(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $ticketTask = $this->ticketTaskRepository->find($id);

        // Check if ticketTask exists
        if (!$ticketTask) {
            return $this->json($this->errorMessageEntityNotFound("ticketTask"), Response::HTTP_BAD_REQUEST);
        }

        if (!empty($data["name"])) {
            $ticketTask->setName($data["name"]);
        }

        if (!empty($data["ticket_id"])) {
            $ticket = $this->ticketRepository->find($data["ticket_id"]);
            // Check if ticket exists
            if (!$ticket) {
                return $this->json($this->errorMessageEntityNotFound("ticket"), Response::HTTP_BAD_REQUEST);
            }
            $ticketTask->setTicket($ticket);
        }

        if (!empty($data["status_id"])) {
            $status = $this->statusRepository->find($data["status_id"]);
            // Check if status exists
            if (!$status) {
                return $this->json($this->errorMessageEntityNotFound("status"), Response::HTTP_BAD_REQUEST);
            }
            $ticketTask->setStatus($status);
        }

        if (!empty($data["user_id"])) {
            $user = $this->userRepository->find($data["user_id"]);
            // Check if user exists
            if (!$status) {
                return $this->json($this->errorMessageEntityNotFound("user"), Response::HTTP_BAD_REQUEST);
            }
            $ticketTask->setUser($user);
        }

        $this->ticketTaskRepository->add($ticketTask, true);

        return $this->json($ticketTask, Response::HTTP_OK, [], ['groups' => [
            'ticketTask',
            'ticketTask_status', 'status',
            'ticketTask_user', 'user',
            'ticketTask_ticket', 'ticket'
        ]]);
    }


    /* Hard Delete TicketTask */
    #[Route('/ticket-task/{id}', name: 'ticketTask_delete', methods: ["DELETE"])]
    public function deleteTicketTask(int $id): JsonResponse
    {
        $ticketTask = $this->ticketTaskRepository->find($id);

        // Check if ticketTask exists
        if (!$ticketTask) {
            return $this->json($this->errorMessageEntityNotFound("ticketTask"), Response::HTTP_BAD_REQUEST);
        }

        $this->ticketTaskRepository->remove($ticketTask, true);

        return $this->json($this->successEntityDeleted("ticketTask"), Response::HTTP_OK);
    }
}
