<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups; 

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['task'])] 
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['task'])]
    #[ORM\Column(length: 45)]
    private $name;

    #[Groups(['task_userStory'])] 
    #[ORM\ManyToOne(targetEntity: UserStory::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private $user_story;

    #[Groups(['task_status'])] 
    #[ORM\ManyToOne(targetEntity: Status::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private $status;

    #[Groups(['task_user'])] 
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'tasks')]
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserStory(): ?UserStory
    {
        return $this->user_story;
    }

    public function setUserStory(?UserStory $user_story): self
    {
        $this->user_story = $user_story;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

        return $this;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
