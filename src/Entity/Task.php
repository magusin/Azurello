<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 45)]
    private $name;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[ORM\Column(type: 'string', length: 45)]
    private $created_by;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updated_at;

    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private $updated_by;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $deleted_at;

    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private $deleted_by;

    #[ORM\ManyToOne(targetEntity: project::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private $project;

    #[ORM\ManyToOne(targetEntity: user::class, inversedBy: 'tasks')]
    private $user;

    #[ORM\ManyToOne(targetEntity: groupTask::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private $group_task;

    #[ORM\ManyToOne(targetEntity: taskStatus::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private $task_status;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->created_by;
    }

    public function setCreatedBy(string $created_by): self
    {
        $this->created_by = $created_by;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updated_by;
    }

    public function setUpdatedBy(?string $updated_by): self
    {
        $this->updated_by = $updated_by;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?\DateTimeInterface $deleted_at): self
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    public function getDeletedBy(): ?string
    {
        return $this->deleted_by;
    }

    public function setDeletedBy(?string $deleted_by): self
    {
        $this->deleted_by = $deleted_by;

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

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getGroupTask(): ?groupTask
    {
        return $this->group_task;
    }

    public function setGroupTask(?groupTask $group_task): self
    {
        $this->group_task = $group_task;

        return $this;
    }

    public function getTaskStatus(): ?taskStatus
    {
        return $this->task_status;
    }

    public function setTaskStatus(?taskStatus $task_status): self
    {
        $this->task_status = $task_status;

        return $this;
    }
}
