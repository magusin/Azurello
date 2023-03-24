<?php

namespace App\Controller;

use App\Context\ControllerContext;
use App\Entity\Status;
use App\Repository\ProjectRepository;
use App\Repository\StatusRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class StatusController extends ControllerContext
{
    private $statusRepository;
    private $projectRepository;

    public function __construct(
        StatusRepository $statusRepository,
        ProjectRepository $projectRepository,
    ) {
        $this->statusRepository = $statusRepository;
        $this->projectRepository = $projectRepository;
    }

    /* List all Status */
    #[Route('/status-list', name: 'status_list', methods: ["HEAD", "GET"])]
    public function listStatus(): JsonResponse
    {
        $status = $this->statusRepository->findAll();

        return $this->json($status, Response::HTTP_OK, [], ['groups' => ['status']]);
    }


    /* List all Status in details*/
    #[Route('/status-list-details', name: 'status_list_details', methods: ["HEAD", "GET"])]
    public function listStatusDetails(): JsonResponse
    {
        $status = $this->statusRepository->findAll();

        return $this->json($status, Response::HTTP_OK, [], ['groups' => [
            'status',
            'status_project', 'project'
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
            'status_ticket', 'ticket',
            'status_project', 'project'
        ]]);
    }


    /* Create status */
    #[Route('/status', name: 'status_create', methods: ["POST"])]
    public function createStatus(Request $request): JsonResponse
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

        // Check if project is not deleted
        if ($project->getDeletedBy(!null)) {
            return $this->json($this->errorMessageEntityIsDeleted("project"), Response::HTTP_BAD_REQUEST);
        }

        $status = new Status();
        $status->setLabel($data["label"]);
        $status->setProject($project);
        $this->statusRepository->add($status, true);

        return $this->json($status, Response::HTTP_CREATED, [], ['groups' => ['status']]);
    }

    /* Edit Status */
    #[Route('status/{id}', name: 'status_edit', methods: ["PATCH"])]
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

        if (!empty($data["project_id"])) {
            $project = $this->projectRepository->find($data["project_id"]);
            // Check if project exists
            if (!$project) {
                return $this->json($this->errorMessageEntityNotFound("project"), Response::HTTP_BAD_REQUEST);
            }
            // Check if project is not deleted
            if ($project->getDeletedBy(!null)) {
                return $this->json($this->errorMessageEntityIsDeleted("project"), Response::HTTP_BAD_REQUEST);
            }
            $status->setProject($project);
        }

        $this->statusRepository->add($status, true);

        return $this->json($status, Response::HTTP_OK, [], ['groups' => [
            'status',
            'status_project', 'project'
        ]]);
    }

    /* Hard Delete Status */
    #[Route('/status/{id}', name: 'status_delete', methods: ["DELETE"])]
    public function deleteStatus(int $id): JsonResponse
    {
        $status = $this->statusRepository->find($id);

        // Check if project exists
        if (!$status) {
            return $this->json($this->errorMessageEntityNotFound("status"), Response::HTTP_BAD_REQUEST);
        }

        $this->statusRepository->remove($status, true);

        return $this->json($this->successEntityDeleted("status"), Response::HTTP_OK);
    }
}
