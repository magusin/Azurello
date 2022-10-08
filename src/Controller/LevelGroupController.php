<?php

namespace App\Controller;

use App\Context\ControllerContext;
use App\Entity\LevelGroup;
use App\Repository\ProjectRepository;
use App\Repository\LevelGroupRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LevelGroupController extends ControllerContext
{
    private $levelGroupRepository;
    private $projectRepository;

    public function __construct(
        LevelGroupRepository $levelGroupRepository,
        ProjectRepository $projectRepository,
    ) {
        $this->levelGroupRepository = $levelGroupRepository;
        $this->projectRepository = $projectRepository;
    }

    /* List all levelGroups */
    #[Route('/level-group-list', name: 'levelGroup_list', methods: ["HEAD", "GET"])]
    public function levelGroupList(): JsonResponse
    {
        $levelGroup = $this->levelGroupRepository->findAll();

        return $this->json($levelGroup, Response::HTTP_OK, [], ['groups' => ['levelGroup']]);
    }

    /* List all levelGroups in details */
    #[Route('/level-group-list-details', name: 'levelGroup_list_details', methods: ["HEAD", "GET"])]
    public function levelGroupListDetails(): JsonResponse
    {
        $levelGroup = $this->levelGroupRepository->findAll();

        return $this->json($levelGroup, Response::HTTP_OK, [], ['groups' => [
            'levelGroup', 'levelGroup_childrens'
        ]]);
    }

    /* Specific levelGroup details */
    #[Route('/level-group/{id}', name: 'levelGroup_details', methods: ["HEAD", "GET"])]
    public function levelGroup(int $id): JsonResponse
    {
        $levelGroup = $this->levelGroupRepository->find($id);

        // Check if levelGroup exists
        if (!$levelGroup) {
            return $this->json($this->errorMessageEntityNotFound("levelGroup"), Response::HTTP_BAD_REQUEST);
        }
        return $this->json($levelGroup, Response::HTTP_OK, [], ['groups' => [
            'levelGroup', 'levelGroup_childrens'
        ]]);
    }

    /* Create levelGroup */
    #[Route('/level-group', name: 'levelGroup_create', methods: ["POST"])]
    public function createLevelGroup(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check JSON body
        if (
            empty($data["name"]) ||
            empty($data['project_id'])
        ) {
            return $this->json($this->errorMessageJsonBody(), Response::HTTP_BAD_REQUEST);
        }


        $project = $this->projectRepository->find($data["project_id"]);
        // Check if project exists
        if (!$project) {
            return $this->json($this->errorMessageEntityNotFound("project"), Response::HTTP_BAD_REQUEST);
        }

        $levelGroup = new LevelGroup();

        if (!empty($data['parent_id'])) {
            $parent = $this->levelGroupRepository->find($data["parent_id"]);
            // Check if levelGroup parent exists
            if (!$parent) {
                return $this->json($this->errorMessageEntityNotFound("levelGroup"), Response::HTTP_BAD_REQUEST);
            }
            $levelGroup->setParent($parent);
        }

        $levelGroup->setName($data["name"]);
        $levelGroup->setProject($project);
        $this->levelGroupRepository->add($levelGroup, true);

        return $this->json($levelGroup, Response::HTTP_CREATED, [], ['groups' => ['levelGroup']]);
    }


    /* Edit levelGroup */
    #[Route('level-group/{id}', name: 'levelGroup_edit', methods: ["PATCH"])]
    public function editLevelGroup(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $levelGroup = $this->levelGroupRepository->find($id);

        // Check if levelGroup exists
        if (!$levelGroup) {
            return $this->json($this->errorMessageEntityNotFound("levelGroup"), Response::HTTP_BAD_REQUEST);
        }

        if (!empty($data["name"])) {
            $levelGroup->setName($data["name"]);
        }

        if (!empty($data["parent_id"])) {
            /* Delete levelGroup parent */
            if ($data["parent_id"] == 'remove') {
                $levelGroup->removeParent();
            }

            /* Add levelGroup parent */
            if ($data["parent_id"] != 'remove') {
                $parent = $this->levelGroupRepository->find($data["parent_id"]);
                // Check if levelGroup parent exists
                if (!$parent) {
                    return $this->json($this->errorMessageEntityNotFound("levelGroup"), Response::HTTP_BAD_REQUEST);
                }
                // Check if parent isnt itself
                if ($parent->getId() == $levelGroup->getId()) {
                    return $this->json($this->errorMessageRelationItself("levelGroup"), Response::HTTP_BAD_REQUEST);
                }
                $levelGroup->setParent($parent);
            }
        }

        $this->levelGroupRepository->add($levelGroup, true);

        return $this->json($levelGroup, Response::HTTP_OK, [], ['groups' => [
            'levelGroup', 'levelGroup_parent'
        ]]);
    }

    /* Hard delete levelGroup */
    #[Route('/level-group/{id}', name: 'levelGroup_delete', methods: ["DELETE"])]
    public function deleteLevelGroup(int $id): JsonResponse
    {
        $levelGroup = $this->levelGroupRepository->find($id);

        // Check if levelGroup exists
        if (!$levelGroup) {
            return $this->json($this->errorMessageEntityNotFound("levelGroup"), Response::HTTP_BAD_REQUEST);
        }

        $this->levelGroupRepository->remove($levelGroup, true);

        return $this->json($this->successEntityDeleted("levelGroup"), Response::HTTP_OK);
    }
}
