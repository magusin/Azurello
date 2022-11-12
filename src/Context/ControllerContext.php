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

    protected function errorMessageRelationNotExist(string $entityName1, string $entityName2)
    {
        return "There is no relation between $entityName1 & $entityName2";
    }
    
    protected function errorMessageRelationItself(string $entityName)
    {
        return "Cannont add a relation between $entityName and itself";
    }

    protected function successEntityDeleted(string $entityName) {
        return "$entityName is successfully deleted";
    }
}
