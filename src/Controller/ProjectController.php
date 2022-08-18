<?php

namespace App\Controller;

use App\Context\ControllerContext;
use DateTime;
use App\Entity\Project;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProjectController extends ControllerContext
{
    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    /* List all Project */
    #[Route('/projects', name: 'project_list', methods: ["HEAD", "GET"])]
    public function projectList(): JsonResponse
    {
        $project = $this->projectRepository->findAllNotDeleted();

        return $this->json($project, Response::HTTP_OK, [], ['groups' => 'project']);
    }


    /* List all Project on details */
    #[Route('/projects_details', name: 'project_list_details', methods: ["HEAD", "GET"])]
    public function projectListDetails(): JsonResponse
    {
        $project = $this->projectRepository->findAllNotDeleted();

        return $this->json($project, Response::HTTP_OK, [], ['groups' => [
            'project',
            'project_userType', 'userType',
            'project_userProject', 'userProject',
            'project_sprint', 'sprint',
            'project_status', 'status',
            'project_group', 'group'
        ]]);
    }


    /* Specific Project details */
    #[Route('/project/{id}', name: 'project', methods: ["HEAD", "GET"])]
    public function project(int $id): JsonResponse
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

        return $this->json($project, Response::HTTP_OK, [], ['groups' => [
            'project',
            'project_userType', 'userType',
            'project_userProject', 'userProject',
            'project_sprint', 'sprint',
            'project_status', 'status',
            'project_group', 'group'
        ]]);
    }


    /* Create project */
    #[Route('/project', name: 'create_project', methods: ["POST"])]
    public function createProject(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check JSON body
        if (
            empty($data["name"]) ||
            empty($data["created_by"])
        ) {
            return $this->json($this->errorMessageJsonBody(), Response::HTTP_BAD_REQUEST);
        }

        $project = new Project();
        $project->setName($data["name"]);
        $project->setCreatedAt(new DateTime());
        $project->setCreatedBy($data["created_by"]);
        $this->projectRepository->add($project, true);

        return $this->json($project, Response::HTTP_CREATED, [], ['groups' => [
            'project',
            'project_userType', 'userType',
            'project_userProject', 'userProject',
            'project_sprint', 'sprint',
            'project_status', 'status',
            'project_group', 'group'
        ]]);
    }


    /* Edit Project */
    #[Route('project/{id}', name: 'edit_project', methods: ["PATCH"])]
    public function editProject(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $project = $this->projectRepository->find($id);

        // Check JSON body
        if (
            empty($data["updated_by"])
        ) {
            return $this->json($this->errorMessageJsonBody(), Response::HTTP_BAD_REQUEST);
        }

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
        $project->setUpdatedBy($data["updated_by"]);
        $project->setUpdatedAt(new DateTime());

        $this->projectRepository->add($project, true);

        return $this->json($project, Response::HTTP_OK, [], ['groups' => [
            'project',
            'project_userType', 'userType',
            'project_userProject', 'userProject',
            'project_sprint', 'sprint',
            'project_status', 'status',
            'project_group', 'group'
        ]]);
    }


    /* Soft Delete Project */
    #[Route('/project/{id}', name: 'delete_project', methods: ["DELETE"])]
    public function deleteProject(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $project = $this->projectRepository->find($id);

        // Check JSON body
        if (
            empty($data["deleted_by"])
        ) {
            return $this->json($this->errorMessageJsonBody(), Response::HTTP_BAD_REQUEST);
        }

        // Check if project exists
        if (!$project) {
            return $this->json($this->errorMessageEntityNotFound("project"), Response::HTTP_BAD_REQUEST);
        }

        // Check if project is not deleted
        if ($project->getDeletedBy(!null)) {
            return $this->json($this->errorMessageEntityIsDeleted("project"), Response::HTTP_BAD_REQUEST);
        }

        $project->setDeletedAt(new DateTime());
        $project->setDeletedBy($data["deleted_by"]);
        $this->projectRepository->add($project, true);

        return $this->json($project, Response::HTTP_ACCEPTED, [], ['groups' => [
            'project',
            'project_userType', 'userType',
            'project_userProject', 'userProject',
            'project_sprint', 'sprint',
            'project_status', 'status',
            'project_group', 'group'
        ]]);
    }
}
