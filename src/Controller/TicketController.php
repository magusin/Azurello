<?php

namespace App\Controller;

use DateTime;
use App\Context\ControllerContext;
use App\Entity\Ticket;
use App\Entity\TicketType;
use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Repository\StatusRepository;
use App\Repository\TicketRepository;
use App\Repository\TicketTypeRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TicketController extends ControllerContext
{
    private $ticketRepository;
    private $statusRepository;
    private $ticketTypeRepository;
    private $jwtManager;
    private $tokenStorageInterface;
    private $userRepository;
    private $currentUser;
    private $projectRepository;

    public function __construct(
        TicketRepository $ticketRepository,
        StatusRepository $statusRepository,
        TicketTypeRepository $ticketTypeRepository,
        TokenStorageInterface $tokenStorageInterface,
        JWTTokenManagerInterface $jwtManager,
        UserRepository $userRepository,
        ProjectRepository $projectRepository
    ) {
        $this->ticketRepository = $ticketRepository;
        $this->statusRepository = $statusRepository;
        $this->ticketTypeRepository = $ticketTypeRepository;
        $this->userRepository = $userRepository;
        $this->projectRepository = $projectRepository;
        $this->jwtManager = $jwtManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
        // Get user from the token
        $decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());
        $this->currentUser = $this->userRepository->findOneBy(array('email' => $decodedJwtToken['email']));
    }


    /* List all ticket */
    #[Route('/ticket-list/{id}', name: 'ticket_list', methods: ["HEAD", "GET"])]
    public function ticketList(String $id): JsonResponse
    {
        $project = $this->projectRepository->find($id);

        // Check if project exists
        if (!$project) {
            return $this->json($this->errorMessageEntityNotFound("project"), Response::HTTP_BAD_REQUEST);
        }

        // Check if project is soft deleted
        if ($project->getIsDeleted()) {
            return $this->json($this->errorMessageEntityIsDeleted("project"), Response::HTTP_BAD_REQUEST);
        }

        // Get all ticket for each ticket type for this project
        $ticketList = new ArrayCollection();
        $project->getTicketTypes()->map(function (TicketType $ticketType) use (&$ticketList) {
            foreach ($ticketType->getTickets() as $ticket) {
                if ($ticket->getParent() == null) {
                    $ticketList->add($ticket);
                }
            }
        });

        return $this->json($ticketList, Response::HTTP_OK, [], ['groups' => [
            'ticket',
            'ticket_childrens',
            'ticket_ticketType', 'ticketType',
            'ticket_status', 'status',
        ]]);
    }


    /* List all Ticket on details */
    #[Route('/ticket-list-details', name: 'ticket_list_details', methods: ["HEAD", "GET"])]
    public function ticketDetails(): JsonResponse
    {
        $ticket = $this->ticketRepository->findAllNotDeleted();

        return $this->json($ticket, Response::HTTP_OK, [], ['groups' => [
            'ticket',
            'ticket_childrens',
            'ticket_status', 'status',
            'ticket_ticketType', 'ticketType',
            'ticket_sprint', 'sprint',
            'ticket_user', 'user'
        ]]);
    }


    /* Specific Ticket details */
    #[Route('/ticket/{id}', name: 'ticket_details', methods: ["HEAD", "GET"])]
    public function ticket(String $id): JsonResponse
    {
        $ticket = $this->ticketRepository->find($id);

        // Check if ticket exists
        if (!$ticket) {
            return $this->json($this->errorMessageEntityNotFound("ticket"), Response::HTTP_BAD_REQUEST);
        }

        // Check if ticket is soft deleted
        if ($ticket->getIsDeleted()) {
            return $this->json($this->errorMessageEntityIsDeleted("ticket"), Response::HTTP_BAD_REQUEST);
        }

        return $this->json($ticket, Response::HTTP_OK, [], ['groups' => [
            'ticket',
            'ticket_childrens',
            'ticket_status', 'status',
            'ticket_ticketType', 'ticketType',
            'ticket_sprint', 'sprint',
            'ticket_user', 'user'
        ]]);
    }


    /* Create Ticket */
    #[Route('/ticket', name: 'ticket_create', methods: ["POST"])]
    public function createTicket(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check if the user is in this project && have access to this function


        // Check JSON body
        if (
            empty($data["name"]) ||
            empty($data["project_id"]) ||
            empty($data["ticket_type_id"])
        ) {
            return $this->json($this->errorMessageJsonBody(), Response::HTTP_BAD_REQUEST);
        }

        $project = $this->projectRepository->find($data["project_id"]);
        // Check if project exists
        if (!$project) {
            return $this->json($this->errorMessageEntityNotFound("project"), Response::HTTP_BAD_REQUEST);
        }
        // Check if project is soft deleted
        if ($project->getIsDeleted()) {
            return $this->json($this->errorMessageEntityIsDeleted("project"), Response::HTTP_BAD_REQUEST);
        }

        $ticketType = $this->ticketTypeRepository->find($data["ticket_type_id"]);
        // Check if ticket type exists && exists in this project
        if (!$ticketType || !$project->getTicketTypes()->contains($ticketType)) {
            return $this->json($this->errorMessageEntityNotFound("ticketType"), Response::HTTP_BAD_REQUEST);
        }

        $ticket = new Ticket();
        if (!empty($data["ticket_parent_id"])) {
            $ticketParent = $this->ticketRepository->find($data["ticket_parent_id"]);
            try {
                $this->checkErrorCreateTicketChild($ticketParent, $project, $ticket);
                $ticket->setParent($ticketParent);
            } catch (InvalidArgumentException $ex) {
                $errorMessage = $ex->getMessage();
                return $this->json($errorMessage, Response::HTTP_BAD_REQUEST);
            }
        }
        $ticket->setName($data["name"]);
        $ticket->setStatus($project->getStatus()[0]);
        $ticket->setTicketType($ticketType);
        if (!empty($data["story_points"])) {
            $ticket->setStoryPoints($data["story_points"]);
        }
        $ticket->setCreatedAt(new DateTime());
        $ticket->setCreatedBy($this->currentUser->getFirstname() . " " . $this->currentUser->getLastname());
        $this->ticketRepository->add($ticket, true);

        return $this->json($ticket, Response::HTTP_CREATED, [], ['groups' => [
            'ticket',
            'ticket_parent',
            'ticket_status', 'status',
            'ticket_ticketType', 'ticketType',
            'ticket_sprint', 'sprint',
            'ticket_user', 'user'
        ]]);
    }


    /* Edit Ticket */
    #[Route('ticket/{id}', name: 'ticket_edit', methods: ["PATCH"])]
    public function editTicket(Request $request, String $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $ticket = $this->ticketRepository->find($id);

        $project = $ticket->getTicketType()->getProject();

        // Check if the user have right

        // Check if ticket exists
        if (!$ticket) {
            return $this->json($this->errorMessageEntityNotFound("ticket"), Response::HTTP_BAD_REQUEST);
        }

        // Check if ticket is soft deleted
        if ($ticket->getIsDeleted()) {
            return $this->json($this->errorMessageEntityIsDeleted("ticket"), Response::HTTP_BAD_REQUEST);
        }

        if (!empty($data['name'])) {
            $ticket->setName($data["name"]);
        }

        if (!empty($data["story_points"])) {
            $ticket->setStoryPoints($data["story_points"]);
        }

        if (!empty($data['status_id'])) {
            $status = $this->statusRepository->find($data["status_id"]);
            // Check if status exists
            if (!$status) {
                return $this->json($this->errorMessageEntityNotFound("status"), Response::HTTP_BAD_REQUEST);
            }
            $ticket->setStatus($status);
        }

        if (!empty($data["ticket_parent_id"])) {
            $ticketParent = $this->ticketRepository->find($data["ticket_parent_id"]);
            try {
                $this->checkErrorCreateTicketChild($ticketParent, $project, $ticket);
                $ticket->setParent($ticketParent);
            } catch (InvalidArgumentException $ex) {
                $errorMessage = $ex->getMessage();
                return $this->json($errorMessage, Response::HTTP_BAD_REQUEST);
            }
        }

        $ticket->setUpdatedAt(new DateTime());
        $ticket->setUpdatedBy($this->currentUser->getFirstname() . " " . $this->currentUser->getLastname());
        $this->ticketRepository->add($ticket, true);

        return $this->json($ticket, Response::HTTP_OK, [], ['groups' => [
            'ticket',
            'ticket_status', 'status',
            'ticket_sprint', 'sprint',
            'ticket_ticketType', 'ticketType',
            'ticket_user', 'user',
            'ticket_task', 'task'
        ]]);
    }

    /* Remove parent from ticket */
    #[Route('/ticket-remove-parent/{id}', name: 'ticket_remove_parent', methods: ["PATCH"])]
    public function removeTicketParent(String $id): JsonResponse
    {
        $ticket = $this->ticketRepository->find($id);

        // Check if ticket exists
        if (!$ticket) {
            return $this->json($this->errorMessageEntityNotFound("ticket"), Response::HTTP_BAD_REQUEST);
        }

        // Check if ticket is soft deleted
        if ($ticket->getIsDeleted()) {
            return $this->json($this->errorMessageEntityIsDeleted("ticket"), Response::HTTP_BAD_REQUEST);
        }

        $ticket->setParent(null);
        $ticket->setUpdatedAt(new DateTime());
        $ticket->setUpdatedBy($this->currentUser->getFirstname() . " " . $this->currentUser->getLastname());
        $this->ticketRepository->add($ticket, true);

        return $this->json($ticket, Response::HTTP_OK, [], ['groups' => [
            'ticket',
            'ticket_status', 'status',
            'ticket_sprint', 'sprint',
            'ticket_ticketType', 'ticketType',
            'ticket_user', 'user',
            'ticket_task', 'task'
        ]]);
    }


    /* Soft Delete ticket */
    #[Route('/ticket/{id}', name: 'ticket_delete', methods: ["DELETE"])]
    public function deleteTicket(Request $request, String $id): JsonResponse
    {
        $ticket = $this->ticketRepository->find($id);

        // Check if ticket exists
        if (!$ticket) {
            return $this->json($this->errorMessageEntityNotFound("ticket"), Response::HTTP_BAD_REQUEST);
        }

        // Check if ticket is soft deleted
        if ($ticket->getIsDeleted()) {
            return $this->json($this->errorMessageEntityIsDeleted("ticket"), Response::HTTP_BAD_REQUEST);
        }

        $ticket->setUpdatedAt(new DateTime());
        $ticket->setUpdatedBy($this->currentUser->getFirstname() . " " . $this->currentUser->getLastname());
        $ticket->setIsDeleted(true);
        $this->ticketRepository->add($ticket, true);

        return $this->json($this->successMessageEntityDeleted("ticket"), Response::HTTP_OK);
    }

    private function checkErrorCreateTicketChild(Ticket $ticketParent, Project $project, Ticket $ticket)
    {
        // Check if parent ticket exists
        if (!$ticketParent) {
            throw new InvalidArgumentException($this->errorMessageEntityNotFound("ticket"));
        }
        // Check if parent ticket in this project
        $hasTicket = false;
        foreach ($project->getTicketTypes() as $ticketType) {
            if ($ticketType->getTickets()->contains($ticketParent)) {
                $hasTicket = true;
                break;
            }
        }
        if (!$hasTicket) {
            throw new InvalidArgumentException($this->errorMessageEntityNotFound("ticket"));
        }
        // Check if ticket is not the same as parent ticket
        if ($ticket->getId() == $ticketParent->getId()) {
            throw new InvalidArgumentException($this->errorMessageRelationItself("ticket"));
        }
        // Check if parent ticket is not a a child from the ticket
        $ticket->getChildrens()->map(function (Ticket $ticketChild) use (&$ticketParent) {
            if ($ticketChild->getId() == $ticketParent->getId()) {
                throw new InvalidArgumentException($this->errorMessageRelationCycle("ticket"));
            }
        });
    }
}
