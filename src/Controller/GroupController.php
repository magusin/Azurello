<?php

namespace App\Controller;

use App\Entity\Group;
use App\Repository\GroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController
{
    private $groupRepository;

    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    /* List all Group */
    #[Route('/groups', name: 'group_list', methods: ["HEAD", "GET"])]
    public function taskList(): JsonResponse
    {
        $group = $this->groupRepository->findAll();

        return $this->json($group, Response::HTTP_OK, [], ['groups' => ['group', 'group_group']]);
    }

    /* List all groups on details */
    #[Route('/groups_details', name: 'group_list_details', methods: ["HEAD", "GET"])]
    public function groupListDetails(): JsonResponse
    {
        $group = $this->groupRepository->findAll();

        return $this->json($group, Response::HTTP_OK, [], ['groups' => ['group', 'group_group']]);
    }

    /* Specific grouo details */
    #[Route('/group/{id}', name: 'group', methods: ["HEAD", "GET"])]
    public function group(int $id): JsonResponse
    {
        $group = $this->groupRepository->find($id);

        // Check if task exists
        if (!$group) {
            return $this->json("No task found", Response::HTTP_BAD_REQUEST);
        }
        return $this->json($group, Response::HTTP_OK, [], ['groups' => ['group', 'group_group']]);
    }

    /* Create task */
    #[Route('/group', name: 'create_group', methods: ["POST"])]
    public function createGroup(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check JSON body
        if (
            empty($data["name"])
        ) {
            return $this->json("JSON incorrect", Response::HTTP_BAD_REQUEST);
        }

        $group = new Group();
        $group->setName($data["name"]);
        $this->groupRepository->add($group, true);

        return $this->json($group, Response::HTTP_CREATED, [],  ['groups' => ['group']]);
    }

    
    /* Edit Task */
    #[Route('group/{id}', name: 'edit_group', methods: ["PATCH"])]
    public function editGroup(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $group = $this->groupRepository->find($id);

        // Check if group exists
        if (!$group) {
            return $this->json("This id is not found", Response::HTTP_BAD_REQUEST);
        }

        if (!empty($data["name"])) {
            $group->setName($data["name"]);
        }

        if (!empty($data["group_group"])) {
            $group_group = $this->groupRepository->find($data["group_group"]);
            // Check if group group exists
            if (!$group_group) {
                return $this->json("No group group found", Response::HTTP_BAD_REQUEST);
            }
            $group->addGroup($group_group);
        }

        $this->groupRepository->add($group, true);

        return $this->json($group, Response::HTTP_OK, [], ['groups' => ['group', 'group_group']]);
    }

     /* Hard Delete Group */
     #[Route('/group/{id}', name: 'delete_group', methods: ["DELETE"])]
     public function deleteGroup(int $id): JsonResponse
     {
         $group = $this->groupRepository->find($id);
 
         // Check if task exists
         if (!$group) {
             return $this->json("This id is not found", Response::HTTP_BAD_REQUEST);
         }
 
         $this->groupRepository->remove($group, true);
 
         return $this->json($group, Response::HTTP_OK, [], ['groups' => ['group', 'group_group']]);
     }
}
