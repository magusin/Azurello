<?php

namespace App\Entity;

use App\Repository\StatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: StatusRepository::class)]
class Status
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['status'])] 
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['status'])] 
    #[ORM\Column(type: 'string', length: 45)]
    private $label;

    #[Groups(['status_userStory'])] 
    #[ORM\OneToMany(mappedBy: 'status', targetEntity: UserStory::class, orphanRemoval: true)]
    private Collection $user_stories;

    #[Groups(['status_task'])]
    #[ORM\OneToMany(mappedBy: 'status', targetEntity: Task::class, orphanRemoval: true)]
    private Collection $tasks;

    #[Groups(['status_project'])]
    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'status')]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    public function __construct()
    {
        $this->user_stories = new ArrayCollection();
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
     * @return Collection<int, UserStory>
     */
    public function getUserStories(): Collection
    {
        return $this->user_stories;
    }

    public function addUserStory(UserStory $user_story): self
    {
        if (!$this->user_stories->contains($user_story)) {
            $this->user_stories->add($user_story);
            $user_story->setStatus($this);
        }

        return $this;
    }

    public function removeUserStory(UserStory $user_story): self
    {
        if ($this->user_stories->removeElement($user_story)) {
            // set the owning side to null (unless already changed)
            if ($user_story->getStatus() === $this) {
                $user_story->setStatus(null);
            }
        }

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
            $task->setStatus($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getStatus() === $this) {
                $task->setStatus(null);
            }
        }

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
}
