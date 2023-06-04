<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\Project;
use App\Entity\UserType;
use App\Entity\Status;
use App\Entity\Sprint;
use App\Entity\UserProject;
use App\Entity\TicketType;

class ProjectEntityTest extends TestCase
{
    /* Attributes */
    public function testSetName()
    {
        $project = new Project();
        $name = 'Test Project';

        $project->setName($name);

        // Vérifie que la nom du projet correspond bien à celui modifié
        $this->assertSame($name, $project->getName());
    }

    public function testSetDescription()
    {
        $project = new Project();
        $description = 'Test Description';

        $project->setDescription($description);

        // Vérifie que la description du projet correspond bien à celui modifié
        $this->assertSame($description, $project->getDescription());
    }

    /* UserType */
    public function testAddUserType()
    {
        $project = new Project();
        $userType = new UserType();

        $project->addUserType($userType);

        // Vérifie que la collection de userTypes du projet contient un élément
        $this->assertCount(1, $project->getUserTypes());
        // Vérifie que le userType est dans la collection de userTypes du projet
        $this->assertTrue($project->getUserTypes()->contains($userType));
        // Vérifie que la référence au projet a été créé dans le userType
        $this->assertEquals($project, $userType->getProject());
    }

    public function testRemoveUserType()
    {
        $project = new Project();
        $userType = new UserType();

        $project->addUserType($userType);
        $project->removeUserType($userType);

        // Vérifie que la collection de userTypes du projet est vide après la suppression
        $this->assertCount(0, $project->getUserTypes());
        // Vérifie que le userType n'est plus dans la collection de userTypes du projet
        $this->assertFalse($project->getUserTypes()->contains($userType));
    }

    /* Status */
    public function testAddStatus()
    {
        $project = new Project();
        $status = new Status();

        $project->addStatus($status);

        // Vérifie que la collection de status du projet contient un élément
        $this->assertCount(1, $project->getStatus());
        // Vérifie que le status est dans la collection de status du projet
        $this->assertTrue($project->getStatus()->contains($status));
        // Vérifie que la référence au projet a été créé dans le status
        $this->assertEquals($project, $status->getProject());
    }

    public function testRemoveStatus()
    {
        $project = new Project();
        $status = new Status();

        $project->addStatus($status);
        $project->removeStatus($status);

        // Vérifie que la collection de status du projet est vide après la suppression
        $this->assertCount(0, $project->getStatus());
        // Vérifie que le status n'est plus dans la collection de status du projet
        $this->assertFalse($project->getStatus()->contains($status));
    }

    /* UserProject */
    public function testAddUserProject()
    {
        $project = new Project();
        $userProject = new UserProject();

        $project->addUserProject($userProject);

        // Vérifie que la collection de userProjects du projet contient un élément
        $this->assertCount(1, $project->getUserProjects());
        // Vérifie que le userProject est dans la collection de userProjects du projet
        $this->assertTrue($project->getUserProjects()->contains($userProject));
        // Vérifie que la référence au projet a été créé dans le userProject
        $this->assertEquals($project, $userProject->getProject());
    }

    public function testRemoveUserProject()
    {
        $project = new Project();
        $userProject = new UserProject();

        $project->addUserProject($userProject);
        $project->removeUserProject($userProject);

        // Vérifie que la collection de userProjects du projet est vide après la suppression
        $this->assertCount(0, $project->getUserProjects());
        // Vérifie que le userProject n'est plus dans la collection de userProjects du projet
        $this->assertFalse($project->getUserProjects()->contains($userProject));
    }

    /* Sprint */
    public function testAddSprint()
    {
        $project = new Project();
        $sprint = new Sprint();

        $project->addSprint($sprint);

        // Vérifie que la collection de sprints du projet contient un élément
        $this->assertCount(1, $project->getSprints());
        // Vérifie que le sprint est dans la collection de sprints du projet
        $this->assertTrue($project->getSprints()->contains($sprint));
        // Vérifie que la référence au projet a été créé dans le sprint
        $this->assertEquals($project, $sprint->getProject());
    }

    public function testRemoveSprint()
    {
        $project = new Project();
        $sprint = new Sprint();

        $project->addSprint($sprint);
        $project->removeSprint($sprint);

        // Vérifie que la collection de sprints du projet est vide après la suppression
        $this->assertCount(0, $project->getSprints());
        // Vérifie que le sprint n'est plus dans la collection de sprints du projet
        $this->assertFalse($project->getSprints()->contains($sprint));
    }

    /* TicketType */
    public function testAddTicketType()
    {
        $project = new Project();
        $ticketType = new TicketType();

        $project->addTicketType($ticketType);

        // Vérifie que la collection de ticketTypes du projet contient un élément
        $this->assertCount(1, $project->getTicketTypes());
        // Vérifie que le ticketType est dans la collection de ticketTypes du projet
        $this->assertTrue($project->getTicketTypes()->contains($ticketType));
        // Vérifie que la référence au projet a été créé dans le ticketType
        $this->assertEquals($project, $ticketType->getProject());
    }

    public function testRemoveTicketType()
    {
        $project = new Project();
        $ticketType = new TicketType();

        $project->addTicketType($ticketType);
        $project->removeTicketType($ticketType);

        // Vérifie que la collection de ticketTypes du projet est vide après la suppression
        $this->assertCount(0, $project->getTicketTypes());
        // Vérifie que le ticketType n'est plus dans la collection de ticketTypes du projet
        $this->assertFalse($project->getTicketTypes()->contains($ticketType));
    }
}
