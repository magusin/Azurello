<?php

namespace App\Entity;

use App\Repository\UserStoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserStoryRepository::class)]
class UserStory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['userStory'])] 
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['userStory'])] 
    #[ORM\Column(type: 'string', length: 45)]
    private $name;

    #[Groups(['userStory'])] 
    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[Groups(['userStory'])] 
    #[ORM\Column(type: 'string', length: 45)]
    private $created_by;

    #[Groups(['userStory'])] 
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updated_at;

    #[Groups(['userStory'])] 
    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private $updated_by;

    #[Groups(['userStory'])] 
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $deleted_at;

    #[Groups(['userStory'])] 
    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private $deleted_by;

    #[Groups(['userStory_project'])] 
    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'user_stories')]
    #[ORM\JoinColumn(nullable: false)]
    private $project;

    #[Groups(['userStory_group'])] 
    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'user_stories')]
    private $group;

    #[Groups(['userStory_status'])] 
    #[ORM\ManyToOne(targetEntity: Status::class, inversedBy: 'user_stories')]
    #[ORM\JoinColumn(nullable: false)]
    private $status;

    #[Groups(['userStory_sprint'])] 
    #[ORM\ManyToMany(targetEntity: Sprint::class, inversedBy: 'user_stories')]
    private $sprints;

    #[Groups(['userStory_task'])] 
    #[ORM\OneToMany(mappedBy: 'user_story', targetEntity: Task::class, orphanRemoval: true)]
    private $tasks;

    #[Groups(['userStory_user'])] 
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'user_stories')]
    private $user;

    public function __construct()
    {
        $this->sprints = new ArrayCollection();
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

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): self
    {
        $this->group = $group;

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

    /**
     * @return Collection<int, Sprint>
     */
    public function getSprint(): Collection
    {
        return $this->sprints;
    }

    public function addSprint(sprint $sprint): self
    {
        if (!$this->sprints->contains($sprint)) {
            $this->sprints->add($sprint);
        }

        return $this;
    }

    public function removeSprint(sprint $sprint): self
    {
        $this->sprints->removeElement($sprint);

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
            $this->tasks->add($task);
            $task->setUserStory($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getUserStory() === $this) {
                $task->setUserStory(null);
            }
        }

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
}
