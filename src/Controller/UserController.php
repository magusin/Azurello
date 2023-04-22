<?php

namespace App\Controller;

use DateTime;
use App\Context\ControllerContext;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\SprintRepository;
use App\Repository\UserProjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends ControllerContext
{
    private $userRepository;
    private $sprintRepository;
    private $userProjectRepository;
    private $hasher;
    private $jwtManager;
    private $tokenStorageInterface;
    private $currentUser;

    public function __construct(
        UserRepository $userRepository,
        SprintRepository $sprintRepository,
        UserProjectRepository $userProjectRepository,
        UserPasswordHasherInterface $hasher,
        TokenStorageInterface $tokenStorageInterface,
        JWTTokenManagerInterface $jwtManager
    ) {
        $this->userRepository = $userRepository;
        $this->sprintRepository = $sprintRepository;
        $this->userProjectRepository = $userProjectRepository;
        $this->hasher = $hasher;

        $this->jwtManager = $jwtManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
        // Get user from the token
        if ($this->tokenStorageInterface->getToken() != null) {
            $decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());
            $this->currentUser = $this->userRepository->findOneBy(array('email' => $decodedJwtToken['email']));
        }
    }

    /* List all User */
    #[Route('/user-list', name: 'user_list', methods: ["HEAD", "GET"])]
    public function userList(): JsonResponse
    {
        $user = $this->userRepository->findAll();

        return $this->json($user, Response::HTTP_OK, [], ['groups' => ['user']]);
    }


    /* List all Users on details */
    #[Route('/user-list-details', name: 'user_list_details', methods: ["HEAD", "GET"])]
    public function userListDetails(): JsonResponse
    {
        $user = $this->userRepository->findAll();

        return $this->json($user, Response::HTTP_OK, [], ['groups' => [
            'user',
            // 'user_sprint', 'sprint',
            'user_userProject', 'userProject'
        ]]);
    }

    /* Specific User details */
    #[Route('/user', name: 'user-details', methods: ["HEAD", "GET"])]
    public function user(): JsonResponse
    {
        $user = $this->currentUser;

        return $this->json($user, Response::HTTP_OK, [], ['groups' => [
            'user'
        ]]);
    }

    /* Create user */
    #[Route('/create_account', name: 'user_create', methods: ["POST"])]
    public function createUser(Request $request, UserPasswordHasherInterface $hasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check JSON body
        if (
            empty($data["email"]) ||
            empty($data["password"]) ||
            empty($data["firstname"]) ||
            empty($data["lastname"])
        ) {
            return $this->json($this->errorMessageJsonBody(), Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setEmail($data["email"]);
        if (!empty($data["roles"])) {
            $user->setRoles($data["roles"]);
        }
        $user->setFirstname($data["firstname"]);
        $user->setLastname($data["lastname"]);
        $user->setRegistrationAt(new DateTime());

        $hashPassword = $this->hasher->hashPassword(
            $user,
            $data['password']
        );
        $user->setPassword($hashPassword);

        $this->userRepository->add($user, true);

        return $this->json($user, Response::HTTP_CREATED, [], ['groups' => [
            'user',
            'user_sprint', 'sprint',
            'user_userProject', 'userProject'
        ]]);
    }


    /* Edit User */
    #[Route('user', name: 'user_edit', methods: ["PATCH"])]
    public function editUser(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->currentUser;

        if (!empty($data["email"])) {
            $user->setEmail($data["email"]);
        }

        if (!empty($data["password"])) {
            $user->setPassword($data["password"]);
        }

        if (!empty($data["lastname"])) {
            $user->setLastname($data["lastname"]);
        }

        if (!empty($data["firstname"])) {
            $user->setFirstname($data["firstname"]);
        }

        if (!empty($data["sprint_id"])) {
            $sprint = $this->sprintRepository->find($data["sprint_id"]);
            // Check if sprint exists
            if (!$sprint) {
                return $this->json($this->errorMessageEntityNotFound("sprint"), Response::HTTP_BAD_REQUEST);
            }
            $user->addSprint($sprint);
        }

        if (!empty($data["user_project_id"])) {
            $userProject = $this->userProjectRepository->find($data["user_project_id"]);
            // Check if userProject exists
            if (!$userProject) {
                return $this->json($this->errorMessageEntityNotFound("user_project"), Response::HTTP_BAD_REQUEST);
            }
            $user->addUserProject($userProject);
        }

        $this->userRepository->add($user, true);

        return $this->json($user, Response::HTTP_OK, [], ['groups' => [
            'user',
            // 'user_sprint', 'sprint',
            'user_userProject', 'userProject'
        ]]);
    }


    /* Hard Delete User */
    #[Route('/user/{id}', name: 'user_delete', methods: ["DELETE"])]
    public function deleteUser(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);

        // Check if user exists
        if (!$user) {
            return $this->json($this->errorMessageEntityNotFound("user"), Response::HTTP_BAD_REQUEST);
        }

        $this->userRepository->remove($user, true);

        return $this->json($this->successEntityDeleted("user"), Response::HTTP_OK);
    }
}
