<?php

namespace App\Controller;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
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
        $project = $this->projectRepository->findAll();

        return $this->json($project, Response::HTTP_OK, [], ['groups' => 'project']);
    }

    /* List all Project on details */
    #[Route('/projects_details', name: 'project_list_details', methods: ["HEAD", "GET"])]
    public function projectListDetails(): JsonResponse
    {
        $project = $this->projectRepository->findAll();

        return $this->json($project, Response::HTTP_OK, [], ['groups' => ['project',  'project_task', 'task']]);
    }

    /* Specific Project details */
    #[Route('/project/{id}', name: 'project', methods: ["HEAD", "GET"])]
    public function project(): JsonResponse
    {
        $project = $this->projectRepository->findAll();

        return $this->json($project, Response::HTTP_OK, [], ['groups' => ['project',  'project_task', 'task', 'task_taskStatus', 'task_status']]);
    }

    /* Create project */
    #[Route('/project', name: 'create_project', methods: ["POST"])]
    public function createProject(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        /* Check JSON body */
        if (
            empty($data["name"]) ||
            empty($data["created_by"])
        ) {
            return $this->json("JSON incorrect", Response::HTTP_BAD_REQUEST);
        }

        $project = new Project();
        $project->setName($data["name"]);
        $project->setCreatedAt(new DateTime());
        $project->setCreatedBy($data["created_by"]);
        $this->projectRepository->add($project, true);

        return $this->json($project, Response::HTTP_CREATED);
    }

    /* Edit Project */
    #[Route('project/{id}', name: 'edit_project', methods: ["PATCH"])]
    public function editProject(Request $request, int $id): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        /* Check JSON body */
        if (
            empty($data["name"]) ||
            empty($data["updated_by"])
        ) {
            return $this->json("JSON incorrect", Response::HTTP_BAD_REQUEST);
        }

        $project = $this->projectRepository->find($id);
 
        /* Check if project exists */
        if (!$project) {
            return $this->json('No project found for id ' . $id, Response::HTTP_BAD_REQUEST);
        }

        $project->setName($data["name"]);
        $project->setUpdatedAt(new DateTime());
        $project->setUpdatedBy($data["updated_by"]);
        $this->projectRepository->add($project, true);

        return $this->json($project, Response::HTTP_OK);
    }


    /* Delete Project */
    #[Route('/project/{id}', name: 'delete_project', methods: ["DELETE"])]
    public function deleteProject(int $id): JsonResponse
    {
        $project = $this->projectRepository->find($id);
 
        /* Check if project exists */
        if (!$project) {
            return $this->json('No project found for id ' . $id, Response::HTTP_BAD_REQUEST);
        }
 
        $this->projectRepository->remove($project, true);
 
        return $this->json($project, Response::HTTP_ACCEPTED);
    }
 
}