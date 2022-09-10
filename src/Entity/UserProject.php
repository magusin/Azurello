<?php

namespace App\Entity;

use App\Repository\UserProjectRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserProjectRepository::class)]
class UserProject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['userProject'])] 
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['userProject_project'])] 
    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'user_projects')]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    #[Groups(['userProject_details'])] 
    #[ORM\ManyToOne(targetEntity: UserType::class, inversedBy: 'user_projects')]
    #[ORM\JoinColumn(nullable: false)]
    private UserType $user_type;

    #[Groups(['userProject_user'])] 
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'user_projects')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    public function getId(): ?int
    {
        return $this->id;
    }

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
        return $this->user_type;
    }

    public function setUserType(?UserType $user_type): self
    {
        $this->user_type = $user_type;

        return $this;
    }
}
