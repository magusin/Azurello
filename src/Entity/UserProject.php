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
    #[Groups(['user_Project', 'user_project_details'])] 
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['user_Project_details'])] 
    #[ORM\ManyToOne(targetEntity: user::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[Groups(['user_Project_details'])] 
    #[ORM\ManyToOne(targetEntity: project::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $project;

    #[Groups(['user_Project_details'])] 
    #[ORM\ManyToOne(targetEntity: usertype::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $user_type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getProject(): ?project
    {
        return $this->project;
    }

    public function setProject(?project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getUserType(): ?usertype
    {
        return $this->user_type;
    }

    public function setUserType(?usertype $user_type): self
    {
        $this->user_type = $user_type;

        return $this;
    }
}
