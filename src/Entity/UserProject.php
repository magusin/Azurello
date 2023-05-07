<?php

namespace App\Entity;

use App\Repository\UserProjectRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserProjectRepository::class)]
class UserProject
{
    #[ORM\Id]
    #[Groups(['userProject_project'])]
    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'userProjects')]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    #[ORM\Id]
    #[Groups(['userProject_user'])]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userProjects')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[Groups(['userProject_details'])]
    #[ORM\ManyToOne(targetEntity: UserType::class, inversedBy: 'userProjects')]
    #[ORM\JoinColumn(nullable: false)]
    private UserType $userType;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getUserType(): ?UserType
    {
        return $this->userType;
    }

    public function setUserType(?UserType $userType): self
    {
        $this->userType = $userType;

        return $this;
    }
}
