<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['project'])]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['project'])]
    #[ORM\Column(type: 'string', length: 45)]
    private $name;

    #[Groups(['project'])]
    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[Groups(['project'])]
    #[ORM\Column(type: 'string', length: 45)]
    private $created_by;

    #[Groups(['project'])]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updated_at;

    #[Groups(['project'])]
    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private $updated_by;

    #[Groups(['project'])]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $deleted_at;

    #[Groups(['project'])]
    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private $deleted_by;

    #[Groups(['project_userType'])]
    #[ORM\OneToMany(targetEntity: UserType::class, mappedBy: "project")]
    private Collection $user_types;

    #[Groups(['project_userStoryGroup'])]
    #[ORM\OneToMany(targetEntity: UserStoryGroup::class, mappedBy: "project")]
    private Collection $user_story_groups;

    #[Groups(['project_status'])]
    #[ORM\OneToMany(targetEntity: Status::class, mappedBy: "project")]
    private Collection $status;

    #[Groups(['project_sprint'])]
    #[ORM\OneToMany(targetEntity: Sprint::class, mappedBy: "project")]
    private Collection $sprints;

    #[Groups(['project_userProject'])]
    #[ORM\OneToMany(targetEntity: UserProject::class, mappedBy: "project")]
    private Collection $user_projects;

    public function __construct()
    {
        $this->user_types = new ArrayCollection();
        $this->user_story_groups = new ArrayCollection();
        $this->status = new ArrayCollection();
        $this->sprints = new ArrayCollection();
        $this->user_projects = new ArrayCollection();
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
     * @return Collection<int, UserType>
     */
    public function getUserTypes(): Collection
    {
        return $this->user_types;
    }

    public function addUserType(UserType $user_type): self
    {
        if (!$this->user_types->contains($user_type)) {
            $this->user_types->add($user_type);
            $user_type->setProject($this);
        }

        return $this;
    }

    public function removeUserType(UserType $user_type): self
    {
        if ($this->user_types->removeElement($user_type)) {
            // set the owning side to null (unless already changed)
            if ($user_type->getProject() === $this) {
                $user_type->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getUserStoryGroups(): Collection
    {
        return $this->user_story_groups;
    }

    public function addUserStoryGroup(UserStoryGroup $userStoryGroup): self
    {
        if (!$this->user_story_groups->contains($userStoryGroup)) {
            $this->user_story_groups->add($userStoryGroup);
            $userStoryGroup->setProject($this);
        }

        return $this;
    }

    public function removeUserStoryGroup(UserStoryGroup $userStoryGroup): self
    {
        if ($this->user_story_groups->removeElement($userStoryGroup)) {
            // set the owning side to null (unless already changed)
            if ($userStoryGroup->getProject() === $this) {
                $userStoryGroup->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Status>
     */
    public function getStatus(): Collection
    {
        return $this->status;
    }

    public function addStatus(Status $status): self
    {
        if (!$this->status->contains($status)) {
            $this->status->add($status);
            $status->setProject($this);
        }

        return $this;
    }

    public function removeStatus(Status $status): self
    {
        if ($this->status->removeElement($status)) {
            // set the owning side to null (unless already changed)
            if ($status->getProject() === $this) {
                $status->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserProject>
     */
    public function getUserProjects(): Collection
    {
        return $this->user_projects;
    }

    public function addUserProject(UserProject $user_project): self
    {
        if (!$this->user_projects->contains($user_project)) {
            $this->user_projects->add($user_project);
            $user_project->setProject($this);
        }

        return $this;
    }

    public function removeUserProject(UserProject $user_project): self
    {
        if ($this->user_projects->removeElement($user_project)) {
            // set the owning side to null (unless already changed)
            if ($user_project->getProject() === $this) {
                $user_project->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sprint>
     */
    public function getSprints(): Collection
    {
        return $this->sprints;
    }

    public function addSprint(Sprint $sprint): self
    {
        if (!$this->sprints->contains($sprint)) {
            $this->sprints->add($sprint);
            $sprint->setProject($this);
        }

        return $this;
    }

    public function removeSprint(Sprint $sprint): self
    {
        if ($this->sprints->removeElement($sprint)) {
            // set the owning side to null (unless already changed)
            if ($sprint->getProject() === $this) {
                $sprint->setProject(null);
            }
        }

        return $this;
    }
}
