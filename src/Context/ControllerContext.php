<?php

namespace App\Context;

use App\Repository\GroupRepository;
use App\Repository\ProjectRepository;
use App\Repository\SprintRepository;
use App\Repository\StatusRepository;
use App\Repository\TaskRepository;
use App\Repository\UserProjectRepository;
use App\Repository\UserRepository;
use App\Repository\UserStoryRepository;
use App\Repository\UserTypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ControllerContext extends AbstractController
{
    protected function errorMessageJsonBody()
    {
        return "JSON body incorrect - some missing values";
    }

    protected function errorMessageEntityIsDeleted(string $entityName)
    {
        return "This $entityName is deleted";
    }

    protected function errorMessageEntityNotFound(string $entityName)
    {
        return "This $entityName is not found";
    }

    protected function errorMessageRelationAlreadyExist(string $entityName1, string $entityName2)
    {
        return "A relation between $entityName1 & $entityName2 already exist";
    }
}
