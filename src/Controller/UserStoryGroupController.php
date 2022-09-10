<?php

namespace App\Controller;

use App\Context\ControllerContext;
use App\Entity\Group;
use App\Entity\UserStoryGroup;
use App\Repository\GroupRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserStoryGroupRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserStoryGroupController extends ControllerContext
{
    private $userStoryGroupRepository;
    private $projectRepository;

    public function __construct(
        UserStoryGroupRepository $userStoryGroupRepository,
        ProjectRepository $projectRepository,
    ) {
        $this->userStoryGroupRepository = $userStoryGroupRepository;
        $this->projectRepository = $projectRepository;
    }

    /* List all groups */
    #[Route('/groups', name: 'group_list', methods: ["HEAD", "GET"])]
    public function groupList(): JsonResponse
    {
        $userStoryGroup = $this->userStoryGroupRepository->findAll();

        return $this->json($userStoryGroup, Response::HTTP_OK, [], ['groups' => ['userStoryGroup']]);
    }

    /* List all groups in details */
    #[Route('/groups_details', name: 'group_list_details', methods: ["HEAD", "GET"])]
    public function groupListDetails(): JsonResponse
    {
        $group = $this->userStoryGroupRepository->findAll();

        return $this->json($group, Response::HTTP_OK, [], ['groups' => [
            'userStoryGroup', 'userStoryGroup_groupChildrens'
        ]]);
    }

    /* Specific group details */
    #[Route('/group/{id}', name: 'group', methods: ["HEAD", "GET"])]
    public function group(int $id): JsonResponse
    {
        $group = $this->userStoryGroupRepository->find($id);

        // Check if group exists
        if (!$group) {
            return $this->json($this->errorMessageEntityNotFound("group"), Response::HTTP_BAD_REQUEST);
        }
        return $this->json($group, Response::HTTP_OK, [], ['groups' => [
            'userStoryGroup', 'userStoryGroup_groupChildrens'
        ]]);
    }

    /* Create group */
    #[Route('/group', name: 'create_group', methods: ["POST"])]
    public function createGroup(Request $request): JsonResponse
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

        $group = new UserStoryGroup();

        if (!empty($data['group_parent_id'])) {
            $groupParent = $this->userStoryGroupRepository->find($data["group_parent_id"]);
            // Check if group parent exists
            if (!$groupParent) {
                return $this->json($this->errorMessageEntityNotFound("group"), Response::HTTP_BAD_REQUEST);
            }
            $group->setGroupParent($groupParent);
        }

        $group->setName($data["name"]);
        $group->setProject($project);
        $this->userStoryGroupRepository->add($group, true);

        return $this->json($group, Response::HTTP_CREATED, [],  ['groups' => ['userStoryGroup']]);
    }


    /* Edit group */
    #[Route('group/{id}', name: 'edit_group', methods: ["PATCH"])]
    public function editGroup(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $group = $this->userStoryGroupRepository->find($id);

        // Check if group exists
        if (!$group) {
            return $this->json($this->errorMessageEntityNotFound("group"), Response::HTTP_BAD_REQUEST);
        }

        if (!empty($data["name"])) {
            $group->setName($data["name"]);
        }

        if (!empty($data["group_parent_id"])) {
            $groupParent = $this->userStoryGroupRepository->find($data["group_parent_id"]);
            // Check if group parent exists
            if (!$groupParent) {
                return $this->json($this->errorMessageEntityNotFound("group"), Response::HTTP_BAD_REQUEST);
            }
            $group->setGroupParent($groupParent);
        }

        $this->userStoryGroupRepository->add($group, true);

        return $this->json($group, Response::HTTP_OK, [], ['groups' => [
            'userStoryGroup', 'userStoryGroup_groupChildrens'
        ]]);
    }

    /* Hard delete group */
    #[Route('/group/{id}', name: 'delete_group', methods: ["DELETE"])]
    public function deleteGroup(int $id): JsonResponse
    {
        $group = $this->groupRepository->find($id);

        // Check if group exists
        if (!$group) {
            return $this->json($this->errorMessageEntityNotFound("group"), Response::HTTP_BAD_REQUEST);
        }

        $this->groupRepository->remove($group, true);

        return $this->json($group, Response::HTTP_OK, [], ['groups' => ['group', 'group_group']]);
    }
}
