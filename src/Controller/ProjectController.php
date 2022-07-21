<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    #[Route('/project_list', name: 'project_list', methods: ["HEAD", "GET"])]
    public function projectList(ProjectRepository $projectRepository): JsonResponse
    {
        $project = $projectRepository->findAll();

        return $this->json($project, Response::HTTP_OK, [], ['groups' => 'project']);
    }

    #[Route('/project_list_details', name: 'project_list_details', methods: ["HEAD", "GET"])]
    public function projectListDetails(ProjectRepository $projectRepository): JsonResponse
    {
        $project = $projectRepository->findAll();

        return $this->json($project, Response::HTTP_OK, [], ['groups' => ['project', 'task']]);
    }
}
