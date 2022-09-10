<?php

namespace App\Context;

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
