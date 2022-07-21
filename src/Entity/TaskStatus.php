<?php

namespace App\Entity;

use App\Repository\TaskStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TaskStatusRepository::class)]
class TaskStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['task_status', 'task_status_details'])] 
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['task_status', 'task_status_details'])] 
    #[ORM\Column(type: 'string', length: 45)]
    private $label;

    #[Groups(['task_status_details'])] 
    #[ORM\OneToMany(mappedBy: 'task_status', targetEntity: Task::class, orphanRemoval: true)]
    private $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    // public function getTasks(): Collection
    // {
    //     return $this->tasks;
    // }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setTaskStatus($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getTaskStatus() === $this) {
                $task->setTaskStatus(null);
            }
        }

        return $this;
    }
}
