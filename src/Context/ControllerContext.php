<?php

namespace App\Context;

use App\Entity\Project;
use App\Entity\User;
use App\Entity\UserProject;
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

    protected function errorMessageEntityIsNotDeleted(string $entityName)
    {
        return "This $entityName is not deleted";
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
        return "Can not add a relation between $entityName and itself";
    }

    protected function errorMessageNotAppropriateRight()
    {
        return "Can not use this function because of your rights on this project";
    }

    protected function successMessageEntityDeleted(string $entityName)
    {
        return "$entityName is successfully deleted";
    }

    protected function successMessageEntityRestored(string $entityName)
    {
        return "$entityName is successfully restored";
    }

    protected function isUserHaveRight(User $currentUser, Project $project, String $rule = null): bool
    {
        $userProjects = $currentUser->getUserProjects();
        if (!$userProjects->isEmpty()) {
            foreach ($userProjects as $userProject) {
                // Check if user is member of the project
                if ($userProject->getProject() === $project) {
                    // Check if user has specific right on the project if specified
                    if ($rule != null) {
                        return $this->isUserHaveSpecificRight($userProject, $rule);
                    }
                    return true;
                }
            }
        }
        return false;
    }

    private function isUserHaveSpecificRight(UserProject $userProject, String $rule): bool
    {
        $userType = $userProject->getUserType();
        // check if user is the owner of the project
        if ($userType->getIsOwner()) {
            return true;
        }

        // check if user have the specific right on the project
        switch ($rule) {
            case "has_create_sprint_rule":
                $result = $userType->getHasCreateTicketRule();
                break;
            case "has_create_ticket_rule":
                $result = $userType->getHasCreateSprintRule();
                break;
            case "has_soft_delete_ticket_rule":
                $result = $userType->getHasSoftDeleteTicketRule();
                break;
            case "has_delete_sprint_rule":
                $result = $userType->getHasDeleteSprintRule();
                break;
            case "has_update_ticket_rule":
                $result = $userType->getHasUpdateTicketRule();
                break;
            case "has_update_sprint_rule":
                $result = $userType->getHasUpdateSprintRule();
                break;
            case "has_assign_member_rule":
                $result = $userType->getHasAssignMemberRule();
                break;
            case "has_invite_member_rule":
                $result = $userType->getHasInviteUserRule();
                break;
            default:
                $result = false;
        }

        return $result;
    }
}
