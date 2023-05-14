<?php

namespace App\Controller;

use App\Context\ControllerContext;
use App\Entity\TicketType;
use App\Repository\ProjectRepository;
use App\Repository\TicketRepository;
use App\Repository\TicketTypeRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TicketTypeController extends ControllerContext
{
    private $ticketRepository;
    private $ticketTypeRepository;
    private $projectRepository;
    private $userRepository;
    private $jwtManager;
    private $tokenStorageInterface;
    private $currentUser;

    public function __construct(
        TicketRepository $ticketRepository,
        TicketTypeRepository $ticketTypeRepository,
        UserRepository $userRepository,
        ProjectRepository $projectRepository,
        TokenStorageInterface $tokenStorageInterface,
        JWTTokenManagerInterface $jwtManager
    ) {
        $this->ticketRepository = $ticketRepository;
        $this->ticketTypeRepository = $ticketTypeRepository;
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;

        $this->jwtManager = $jwtManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
        // Get user from the token
        $decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());
        $this->currentUser = $this->userRepository->findOneBy(array('email' => $decodedJwtToken['email']));
    }


    /* List all ticketType */
    #[Route('/ticket-type-list/{id}', name: 'ticketType_list', methods: ["HEAD", "GET"])]
    public function ticketTypeList(String $id): JsonResponse
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

        // Check if user have access to this project
        if (!$this->isUserHaveRight($this->currentUser, $project)) {
            return $this->json($this->errorMessageEntityNotFound("project"), Response::HTTP_BAD_REQUEST);
        }

        $ticketTypeList = $project->getTicketTypes();

        return $this->json($ticketTypeList, Response::HTTP_OK, [], ['groups' => ['ticketType']]);
    }


    /* List all ticketType on details */
    #[Route('/ticket-type-list-details', name: 'ticketType_list_details', methods: ["HEAD", "GET"])]
    public function ticketTypeDetails(String $id): JsonResponse
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

        // Check if user have access to this project
        if (!$this->isUserHaveRight($this->currentUser, $project)) {
            return $this->json($this->errorMessageEntityNotFound("project"), Response::HTTP_BAD_REQUEST);
        }

        $ticketTypeList = $project->getTicketTypes();

        return $this->json($ticketTypeList, Response::HTTP_OK, [], ['groups' => [
            'ticketType',
            'ticketType_ticket', 'ticket',
            'ticketType_project', 'project'
        ]]);
    }


    /* Specific ticketType details */
    #[Route('/ticket-type/{id}', name: 'ticketType_details', methods: ["HEAD", "GET"])]
    public function ticketType(String $id): JsonResponse
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
            empty($data["label"]) ||
            empty($data["project_id"])
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

        // Check if user have access to this project
        if (!$this->isUserHaveRight($this->currentUser, $project)) {
            return $this->json($this->errorMessageEntityNotFound("project"), Response::HTTP_BAD_REQUEST);
        }

        $ticketType = new TicketType();
        $ticketType->setProject($project);
        $ticketType->setLabel($data["label"]);
        $this->ticketTypeRepository->add($ticketType, true);

        return $this->json($ticketType, Response::HTTP_CREATED, [], ['groups' => [
            'ticketType',
            'ticketType_ticket', 'ticket',
            'ticketType_project', 'project'
        ]]);
    }


    /* Edit ticketType */
    #[Route('ticket-type/{id}', name: 'ticketType_edit', methods: ["PATCH"])]
    public function editTicketType(Request $request, String $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $ticketType = $this->ticketTypeRepository->find($id);

        // Check if ticketType exists
        if (!$ticketType) {
            return $this->json($this->errorMessageEntityNotFound("ticketType"), Response::HTTP_BAD_REQUEST);
        }

        if (!empty($data['label'])) {
            $ticketType->setLabel($data["label"]);
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
    public function deleteTicketType(String $id): JsonResponse
    {
        $ticketType = $this->ticketTypeRepository->find($id);

        // Check if ticketType exists
        if (!$ticketType) {
            return $this->json($this->errorMessageEntityNotFound("ticketType"), Response::HTTP_BAD_REQUEST);
        }

        $this->ticketTypeRepository->remove($ticketType, true);

        return $this->json($this->successMessageEntityDeleted("ticketType"), Response::HTTP_OK);
    }
}
