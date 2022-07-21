<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['project', 'project_details'])]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['project', 'project_details'])]
    #[ORM\Column(type: 'string', length: 45)]
    private $name;

    #[Groups(['project', 'project_details'])]
    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[Groups(['project', 'project_details'])]
    #[ORM\Column(type: 'string', length: 45)]
    private $created_by;

    #[Groups(['project', 'project_details'])]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updated_at;

    #[Groups(['project', 'project_details'])]
    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private $updated_by;

    #[Groups(['project', 'project_details'])]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $deleted_at;

    #[Groups(['project', 'project_details'])]
    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private $deleted_by;

    #[Groups(['project_details'])]
    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Task::class, orphanRemoval: true)]
    private $tasks;

    public function __construct()
    {
        $this->userTypes_id = new ArrayCollection();
        $this->tasks = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setProject($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getProject() === $this) {
                $task->setProject(null);
            }
        }

        return $this;
    }
}
