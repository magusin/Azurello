<?php

namespace App\Controller;

use DateTime;
use App\Entity\Project;
use App\Context\ControllerContext;
use App\Entity\Status;
use App\Entity\TicketType;
use App\Entity\UserProject;
use App\Entity\UserType;
use App\Repository\ProjectRepository;
use App\Repository\StatusRepository;
use App\Repository\TicketTypeRepository;
use App\Repository\UserProjectRepository;
use App\Repository\UserRepository;
use App\Repository\UserTypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProjectController extends ControllerContext
{
    private $projectRepository;
    private $userProjectRepository;
    private $userTypeRepository;
    private $userRepository;
    private $statusRepository;
    private $ticketTypeRepository;
    private $jwtManager;
    private $tokenStorageInterface;
    private $currentUser;

    public function __construct(
        ProjectRepository $projectRepository,
        UserRepository $userRepository,
        UserTypeRepository $userTypeRepository,
        UserProjectRepository $userProjectRepository,
        TicketTypeRepository $ticketTypeRepository,
        StatusRepository $statusRepository,
        TokenStorageInterface $tokenStorageInterface,
        JWTTokenManagerInterface $jwtManager
    ) {
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
        $this->userTypeRepository = $userTypeRepository;
        $this->userProjectRepository = $userProjectRepository;
        $this->ticketTypeRepository = $ticketTypeRepository;
        $this->statusRepository = $statusRepository;

        $this->jwtManager = $jwtManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
        // Get user from the token
        $decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());
        $this->currentUser = $this->userRepository->findOneBy(array('email' => $decodedJwtToken['email']));
    }

    /* List all Project */
    #[Route('/project-list', name: 'project_list', methods: ["HEAD", "GET"])]
    public function projectList(): JsonResponse
    {
        // Get all project for this user
        $userProjects = $this->currentUser->getUserProjects();
        $projects = [];
        foreach ($userProjects as $userProject) {
            $project = $userProject->getProject();
            if ($project->getDeletedAt() == null) {
                array_push($projects, $userProject->getProject());
            }
        }

        return $this->json($projects, Response::HTTP_OK, [], ['groups' => 'project']);
    }


    /* List all Project in details */
    #[Route('/project-list-details', name: 'project_list_details', methods: ["HEAD", "GET"])]
    public function projectListDetails(): JsonResponse
    {
        $project = $this->projectRepository->findAllNotDeleted();

        return $this->json($project, Response::HTTP_OK, [], ['groups' => [
            'project',
            'project_userType', 'userType',
            'project_userProject', 'userProject',
            'project_sprint', 'sprint',
            'project_status', 'status',
            'project_ticketType', 'ticketType'
        ]]);
    }


    /* Specific Project details */
    #[Route('/project/{id}', name: 'project_details', methods: ["HEAD", "GET"])]
    public function project(int $id): JsonResponse
    {
        $project = $this->projectRepository->find($id);

        // Check if project exists
        if (!$project) {
            return $this->json($this->errorMessageEntityNotFound("project"), Response::HTTP_BAD_REQUEST);
        }

        // Check if user have access to this project
        if (!$this->isUserHaveRight($this->currentUser, $project)) {
            return $this->json($this->errorMessageEntityNotFound("project"), Response::HTTP_BAD_REQUEST);
        }

        // Check if project is not deleted
        if ($project->getDeletedBy(!null)) {
            return $this->json($this->errorMessageEntityIsDeleted("project"), Response::HTTP_BAD_REQUEST);
        }

        return $this->json($project, Response::HTTP_OK, [], ['groups' => [
            'project',
            'project_userType', 'userType',
            'project_userProject', 'userProject', 'userProject_user',
            'project_sprint', 'sprint',
            'project_status', 'status',
            'project_ticketType', 'ticketType_ticket', 'ticket', 'ticket_children'
        ]]);
    }


    /* Create project */
    #[Route('/project', name: 'project_create', methods: ["POST"])]
    public function createProject(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check JSON body
        if (
            empty($data["name"])
        ) {
            return $this->json($this->errorMessageJsonBody(), Response::HTTP_BAD_REQUEST);
        }

        // Create project
        $project = new Project();
        $project->setName($data["name"]);
        if (!empty($data["description"])) {
            $project->setDescription($data["description"]);
        }
        $project->setCreatedAt(new DateTime());
        $project->setCreatedBy($this->currentUser->getFirstname() . " " . $this->currentUser->getLastname());
        $this->projectRepository->add($project, true);

        // Create defaut user type
        $userType = new UserType();
        $userType->setProject($project);
        $userType->setLabel("Administrator");
        $this->userTypeRepository->add($userType, true);

        // Create defaut ticket type
        $ticketType = new TicketType();
        $ticketType->setProject($project);
        $ticketType->setName("UserStory");
        $ticketType2 = new TicketType();
        $ticketType2->setProject($project);
        $ticketType2->setName("Bug");
        $ticketType3 = new TicketType();
        $ticketType3->setProject($project);
        $ticketType3->setName("Test");
        $this->ticketTypeRepository->add($ticketType, true);
        $this->ticketTypeRepository->add($ticketType2, true);
        $this->ticketTypeRepository->add($ticketType3, true);

        // Create defaut status
        $status = new Status();
        $status->setProject($project);
        $status->setLabel("Todo");
        $status2 = new Status();
        $status2->setProject($project);
        $status2->setLabel("In progress");
        $status3 = new Status();
        $status3->setProject($project);
        $status3->setLabel("Closed");
        $this->statusRepository->add($status, true);
        $this->statusRepository->add($status2, true);
        $this->statusRepository->add($status3, true);

        // Bind current user with project
        $userProject = new UserProject();
        $userProject->setProject($project);
        $userProject->setUser($this->currentUser);
        $userProject->setUserType($userType);
        $this->userProjectRepository->add($userProject, true);

        return $this->json($project, Response::HTTP_CREATED, [], ['groups' => ['project']]);
    }


    /* Edit Project */
    #[Route('project/{id}', name: 'project_edit', methods: ["PATCH"])]
    public function editProject(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $project = $this->projectRepository->find($id);

        // Check if project exists
        if (!$project) {
            return $this->json($this->errorMessageEntityNotFound("project"), Response::HTTP_BAD_REQUEST);
        }

        // Check if project is not deleted
        if ($project->getDeletedBy(!null)) {
            return $this->json($this->errorMessageEntityIsDeleted("project"), Response::HTTP_BAD_REQUEST);
        }

        if (!empty($data["name"])) {
            $project->setName($data["name"]);
        }
        if (!empty($data["description"])) {
            $project->setDescription($data["description"]);
        }
        $project->setUpdatedBy($this->currentUser->getFirstname() . " " . $this->currentUser->getLastname());
        $project->setUpdatedAt(new DateTime());

        $this->projectRepository->add($project, true);

        return $this->json($project, Response::HTTP_OK, [], ['groups' => [
            'project',
            'project_userType', 'userType',
            'project_userProject', 'userProject',
            'project_sprint', 'sprint',
            'project_status', 'status',
            'project_ticketType', 'ticketType'
        ]]);
    }

    /* Soft Delete Project */
    #[Route('/project/{id}', name: 'project_delete', methods: ["DELETE"])]
    public function deleteProject(Request $request, int $id): JsonResponse
    {
        $project = $this->projectRepository->find($id);

        // Check if project exists
        if (!$project) {
            return $this->json($this->errorMessageEntityNotFound("project"), Response::HTTP_BAD_REQUEST);
        }

        // Check if project is not deleted
        if ($project->getDeletedBy(!null)) {
            return $this->json($this->errorMessageEntityIsDeleted("project"), Response::HTTP_BAD_REQUEST);
        }

        $project->setDeletedAt(new DateTime());
        $project->setDeletedBy($this->currentUser->getFirstname() . " " . $this->currentUser->getLastname());
        $this->projectRepository->add($project, true);

        return $this->json($this->successEntityDeleted("project"), Response::HTTP_OK);
    }
}
