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
            if (!$project->getIsDeleted()) {
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

    /* List all Project soft deleted */
    #[Route('/project-soft-deleted-list', name: 'project_soft_deleted_list', methods: ["HEAD", "GET"])]
    public function projectSoftDeletedList(): JsonResponse
    {
        // Get all project for this user
        $userProjects = $this->currentUser->getUserProjects();
        $projects = [];
        foreach ($userProjects as $userProject) {
            $project = $userProject->getProject();
            if ($project->getIsDeleted()) {
                array_push($projects, $userProject->getProject());
            }
        }

        return $this->json($projects, Response::HTTP_OK, [], ['groups' => 'project']);
    }


    /* Specific Project details */
    #[Route('/project/{id}', name: 'project_details', methods: ["HEAD", "GET"])]
    public function project(String $id): JsonResponse
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

        // Check if project is soft deleted
        if ($project->getIsDeleted()) {
            return $this->json($this->errorMessageEntityIsDeleted("project"), Response::HTTP_BAD_REQUEST);
        }

        return $this->json($project, Response::HTTP_OK, [], ['groups' => [
            'project',
            'project_userType', 'userType',
            'project_userProject', 'userProject', 'userProject_user', 'user',
            'project_sprint', 'sprint',
            'project_status', 'status',
            'project_ticketType', 'ticketType', 'ticketType_ticket', 'ticket', 'ticket_children'
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
        $userTypeOwner = new UserType();
        $userTypeOwner->setProject($project);
        $userTypeOwner->setLabel("Owner");
        $userTypeOwner->setIsOwner(true);
        $userTypeVisitor = new UserType();
        $userTypeVisitor->setProject($project);
        $userTypeVisitor->setLabel("Visitor");
        $userTypeAdministrator = new UserType();
        $userTypeAdministrator->setProject($project);
        $userTypeAdministrator->setLabel("Administrator");
        $userTypeAdministrator->setHasCreateSprintRule(true);
        $userTypeAdministrator->setHasCreateTicketRule(true);
        $userTypeAdministrator->setHasSoftDeleteTicketRule(true);
        $userTypeAdministrator->setHasDeleteSprintRule(true);
        $userTypeAdministrator->setHasUpdateSprintRule(true);
        $userTypeAdministrator->setHasUpdateTicketRule(true);
        $userTypeAdministrator->setHasAssignMemberRule(true);
        $userTypeAdministrator->setHasInviteUserRule(true);
        $this->userTypeRepository->add($userTypeOwner, true);
        $this->userTypeRepository->add($userTypeVisitor, true);
        $this->userTypeRepository->add($userTypeAdministrator, true);

        // Create defaut ticket type
        $ticketType = new TicketType();
        $ticketType->setProject($project);
        $ticketType->setLabel("UserStory");
        $ticketType2 = new TicketType();
        $ticketType2->setProject($project);
        $ticketType2->setLabel("Bug");
        $ticketType3 = new TicketType();
        $ticketType3->setProject($project);
        $ticketType3->setLabel("Test");
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
        $userProject->setUserType($userTypeOwner);
        $this->userProjectRepository->add($userProject, true);

        return $this->json($project, Response::HTTP_CREATED, [], ['groups' => ['project']]);
    }


    /* Edit Project */
    #[Route('project/{id}', name: 'project_edit', methods: ["PATCH"])]
    public function editProject(Request $request, String $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $project = $this->projectRepository->find($id);

        // Check if project exists
        if (!$project) {
            return $this->json($this->errorMessageEntityNotFound("project"), Response::HTTP_BAD_REQUEST);
        }

        // Check if project is soft deleted
        if ($project->getIsDeleted()) {
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
    #[Route('/project-soft-delete/{id}', name: 'project_soft_delete', methods: ["PATCH"])]
    public function softDeleteProject(String $id): JsonResponse
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

        $project->setUpdatedAt(new DateTime());
        $project->setUpdatedBy($this->currentUser->getFirstname() . " " . $this->currentUser->getLastname());
        $project->setIsDeleted(true);
        $this->projectRepository->add($project, true);

        return $this->json($this->successMessageEntityDeleted("project"), Response::HTTP_OK);
    }

    /* Restore soft deleted Project */
    #[Route('/project-restore/{id}', name: 'project_restore', methods: ["PATCH"])]
    public function restoreProject(String $id): JsonResponse
    {
        $project = $this->projectRepository->find($id);

        // Check if project exists
        if (!$project) {
            return $this->json($this->errorMessageEntityNotFound("project"), Response::HTTP_BAD_REQUEST);
        }

        // Check if project is not soft deleted
        if (!$project->getIsDeleted()) {
            return $this->json($this->errorMessageEntityIsNotDeleted("project"), Response::HTTP_BAD_REQUEST);
        }

        $project->setUpdatedAt(new DateTime());
        $project->setUpdatedBy($this->currentUser->getFirstname() . " " . $this->currentUser->getLastname());
        $project->setIsDeleted(false);
        $this->projectRepository->add($project, true);

        return $this->json($this->successMessageEntityRestored("project"), Response::HTTP_OK);
    }

    /* Hard Delete Project */
    #[Route('/project/{id}', name: 'project_delete', methods: ["DELETE"])]
    public function deleteProject(String $id): JsonResponse
    {
        $project = $this->projectRepository->find($id);

        // Check if project exists
        if (!$project) {
            return $this->json($this->errorMessageEntityNotFound("project"), Response::HTTP_BAD_REQUEST);
        }

        // Check if user have access to this project & this function
        if (!$this->isUserHaveRight($this->currentUser, $project, "is_owner")) {
            return $this->json($this->errorMessageNotAppropriateRight(), Response::HTTP_BAD_REQUEST);
        }

        $this->projectRepository->remove($project, true);

        return $this->json($this->successMessageEntityDeleted("project"), Response::HTTP_OK);
    }
}
