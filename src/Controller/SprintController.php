<?php

namespace App\Controller;

use App\Repository\SprintRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class SprintController extends AbstractController
{
    #[Route('/sprints', name: 'sprint_list', methods: ["HEAD", "GET"])]
    public function sprintList(SprintRepository $sprintRepository, Request $request): JsonResponse
    {
        $sprint = $sprintRepository->findAll();

        return $this->json($sprint, Response::HTTP_OK, [], ['groups' => ['sprint']]);
    }

    #[Route('/sprints_details', name: 'sprint_list_details', methods: ["HEAD", "GET"])]
    public function sprintListDetails(SprintRepository $sprintRepository, Request $request): JsonResponse
    {
        $sprint = $sprintRepository->findAll();

        return $this->json($sprint, Response::HTTP_OK, [], ['groups' => ['sprint', 'user']]);
    }

}
