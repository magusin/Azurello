<?php

namespace App\Controller;

use App\Context\ControllerContext;
use App\Entity\UserType;
use App\Repository\UserTypeRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserTypeController extends ControllerContext
{
    private $projectRepository;
    private $userTypeRepository;

    public function __construct(
        ProjectRepository $projectRepository,
        UserTypeRepository $userTypeRepository
    ) {
        $this->projectRepository = $projectRepository;
        $this->userTypeRepository = $userTypeRepository;
    }


    /* List all UserType */
    #[Route('/userType', name: 'userType_list', methods: ["HEAD", "GET"])]
    public function userTypeList(): JsonResponse
    {
        $userType = $this->userTypeRepository->findAll();

        return $this->json($userType, Response::HTTP_OK, [], ['groups' => 'userType']);
    }


    /* List all UserType on details */
    #[Route('/userType_details', name: 'userType_list_details', methods: ["HEAD", "GET"])]
    public function userTypeListDetails(): JsonResponse
    {
        $userType = $this->userTypeRepository->findAll();

        return $this->json($userType, Response::HTTP_OK, [], ['groups' => [
            'userType',
            'userType_userProject', 'userProject',
            'userProject_user', 'user',
            'userType_project', 'project'
        ]]);
    }


    /* Specific UserType details */
    #[Route('/userType/{id}', name: 'userType', methods: ["HEAD", "GET"])]
    public function UserType(int $id): JsonResponse
    {
        $userType = $this->userTypeRepository->find($id);

        // Check if userType exists
        if (!$userType) {
            return $this->json($this->errorMessageEntityNotFound("userType"), Response::HTTP_BAD_REQUEST);
        }

        return $this->json($userType, Response::HTTP_OK, [], ['groups' => [
            'userType',
            'userType_userProject', 'userProject',
            'userProject_user', 'user',
            'userType_project', 'project'
        ]]);
    }


    /* Create UserType */
    #[Route('/userType', name: 'create_userType', methods: ["POST"])]
    public function createUserType(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check JSON body
        if (
            empty($data["project_id"]) ||
            empty($data["label"])
        ) {
            return $this->json($this->errorMessageJsonBody(), Response::HTTP_BAD_REQUEST);
        }
        $project = $this->projectRepository->find($data["project_id"]);

        // Check if project exists
        if (!$project) {
            return $this->json($this->errorMessageEntityNotFound("project"), Response::HTTP_BAD_REQUEST);
        }
        $userType = new UserType();
        $userType->setLabel($data["label"]);
        $userType->setProject($project);
        $this->userTypeRepository->add($userType, true);

        return $this->json($userType, Response::HTTP_CREATED, [], ['groups' => [
            'userType',
            'userType_project', 'project'
        ]]);
    }
}
