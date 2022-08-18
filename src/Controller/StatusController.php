<?php

namespace App\Controller;

use App\Context\ControllerContext;
use App\Entity\Status;
use App\Repository\StatusRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class StatusController extends ControllerContext
{
    private $statusRepository;

    public function __construct(StatusRepository $statusRepository)
    {
        $this->statusRepository = $statusRepository;
    }

    /* List all Status */
    #[Route('/status', name: 'status_list', methods: ["HEAD", "GET"])]
    public function listStatus(): JsonResponse
    {
        $status = $this->statusRepository->findAll();

        return $this->json($status, Response::HTTP_OK, [], ['groups' => ['status']]);
    }


    /* List all Status */
    #[Route('/status_details', name: 'status_list_details', methods: ["HEAD", "GET"])]
    public function listStatusDetails(): JsonResponse
    {
        $status = $this->statusRepository->findAll();

        return $this->json($status, Response::HTTP_OK, [], ['groups' => [
            'status',
            'status_userStory', 'userStory',
            'status_task', 'task'
        ]]);
    }


    /* Specific status details */
    #[Route('/status/{id}', name: 'status_detail', methods: ["HEAD", "GET"])]
    public function status(int $id): JsonResponse
    {
        $status = $this->statusRepository->find($id);

        // Check if status exists
        if (!$status) {
            return $this->json($this->errorMessageEntityNotFound("status"), Response::HTTP_BAD_REQUEST);
        }
        return $this->json($status, Response::HTTP_OK, [], ['groups' => [
            'status',
            'status_task', 'task',
            'status_userStory', 'userStory'
        ]]);
    }


    /* Create status */
    #[Route('/status', name: 'create_status', methods: ["POST"])]
    public function createStatus(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check JSON body
        if (
            empty($data["label"])
        ) {
            return $this->json($this->errorMessageJsonBody(), Response::HTTP_BAD_REQUEST);
        }

        $status = new Status();
        $status->setLabel($data["label"]);
        $this->statusRepository->add($status, true);

        return $this->json($status, Response::HTTP_CREATED, [], ['groups' => [
            'status',
            'status_userStory', 'userStory',
            'status_task', 'task'
        ]]);
    }

    /* Edit Status */
    #[Route('status/{id}', name: 'edit_status', methods: ["PATCH"])]
    public function editStatus(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $status = $this->statusRepository->find($id);


        // Check if status exists
        if (!$status) {
            return $this->json($this->errorMessageEntityNotFound("status"), Response::HTTP_BAD_REQUEST);
        }

        if (!empty($data["label"])) {
            $status->setLabel($data["label"]);
        }

        $this->statusRepository->add($status, true);

        return $this->json($status, Response::HTTP_OK, [], ['groups' => [
            'status',
            'status_userStory', 'userStory',
            'status_task', 'task'
        ]]);
    }

    /* Hard Delete Status */
    #[Route('/status/{id}', name: 'delete_status', methods: ["DELETE"])]
    public function deleteStatus(int $id): JsonResponse
    {
        $status = $this->statusRepository->find($id);

        // Check if project exists
        if (!$status) {
            return $this->json($this->errorMessageEntityNotFound("status"), Response::HTTP_BAD_REQUEST);
        }

        $this->statusRepository->remove($status, true);

        return $this->json($status, Response::HTTP_OK, [], ['groups' => [
            'status',
            'status_userStory', 'userStory',
            'status_task', 'task'
        ]]);
    }
}
