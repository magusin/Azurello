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
    private $createdAt;

    #[Groups(['project'])]
    #[ORM\Column(type: 'string', length: 45)]
    private $createdBy;

    #[Groups(['project'])]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    #[Groups(['project'])]
    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private $updatedBy;

    #[Groups(['project'])]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $deletedAt;

    #[Groups(['project'])]
    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private $deletedBy;

    #[Groups(['project_userType'])]
    #[ORM\OneToMany(targetEntity: UserType::class, mappedBy: "project")]
    private Collection $userTypes;

    #[Groups(['project_levelGroup'])]
    #[ORM\OneToMany(targetEntity: LevelGroup::class, mappedBy: "project")]
    private Collection $levelGroups;

    #[Groups(['project_status'])]
    #[ORM\OneToMany(targetEntity: Status::class, mappedBy: "project")]
    private Collection $status;

    #[Groups(['project_sprint'])]
    #[ORM\OneToMany(targetEntity: Sprint::class, mappedBy: "project")]
    private Collection $sprints;

    #[Groups(['project_userProject'])]
    #[ORM\OneToMany(targetEntity: UserProject::class, mappedBy: "project")]
    private Collection $userProjects;

    public function __construct()
    {
        $this->userTypes = new ArrayCollection();
        $this->levelGroups = new ArrayCollection();
        $this->status = new ArrayCollection();
        $this->sprints = new ArrayCollection();
        $this->userProjects = new ArrayCollection();
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
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getDeletedBy(): ?string
    {
        return $this->deletedBy;
    }

    public function setDeletedBy(?string $deletedBy): self
    {
        $this->deletedBy = $deletedBy;

        return $this;
    }

    /**
     * @return Collection<int, UserType>
     */
    public function getUserTypes(): Collection
    {
        return $this->userTypes;
    }

    public function addUserType(UserType $userType): self
    {
        if (!$this->userTypes->contains($userType)) {
            $this->userTypes->add($userType);
            $userType->setProject($this);
        }

        return $this;
    }

    public function removeUserType(UserType $userType): self
    {
        if ($this->userTypes->removeElement($userType)) {
            // set the owning side to null (unless already changed)
            if ($userType->getProject() === $this) {
                $userType->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getLevelGroups(): Collection
    {
        return $this->levelGroups;
    }

    public function addLevelGroups(LevelGroup $levelGroup): self
    {
        if (!$this->levelGroups->contains($levelGroup)) {
            $this->levelGroups->add($levelGroup);
            $levelGroup->setProject($this);
        }

        return $this;
    }

    public function removeLevelGroups(LevelGroup $levelGroup): self
    {
        if ($this->levelGroups->removeElement($levelGroup)) {
            // set the owning side to null (unless already changed)
            if ($levelGroup->getProject() === $this) {
                $levelGroup->setProject(null);
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
        return $this->userProjects;
    }

    public function addUserProject(UserProject $userProject): self
    {
        if (!$this->userProjects->contains($userProject)) {
            $this->userProjects->add($userProject);
            $userProject->setProject($this);
        }

        return $this;
    }

    public function removeUserProject(UserProject $userProject): self
    {
        if ($this->userProjects->removeElement($userProject)) {
            // set the owning side to null (unless already changed)
            if ($userProject->getProject() === $this) {
                $userProject->setProject(null);
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
