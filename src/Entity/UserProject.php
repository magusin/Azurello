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
    #[Groups(['user_project', 'user_project_details'])] 
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['user_project_details'])] 
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[Groups(['user_project_details'])] 
    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $project;

    #[Groups(['user_project_details'])] 
    #[ORM\ManyToOne(targetEntity: UserType::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $user_type;

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
