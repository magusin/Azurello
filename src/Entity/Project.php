<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[Groups(['project'])]
    #[ORM\Column(type: "ulid", unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    private $id;

    #[Groups(['project'])]
    #[ORM\Column(type: 'string', length: 45)]
    private $name;

    #[Groups(['project'])]
    #[ORM\Column(type: 'string', length: 510,  nullable: true)]
    private $description;

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
    #[ORM\Column(type: 'boolean')]
    private $isDeleted = false;

    #[Groups(['project_userType'])]
    #[ORM\OneToMany(targetEntity: UserType::class, mappedBy: "project")]
    private Collection $userTypes;

    #[Groups(['project_status'])]
    #[ORM\OneToMany(targetEntity: Status::class, mappedBy: "project")]
    private Collection $status;

    #[Groups(['project_sprint'])]
    #[ORM\OneToMany(targetEntity: Sprint::class, mappedBy: "project")]
    private Collection $sprints;

    #[Groups(['project_userProject'])]
    #[ORM\OneToMany(targetEntity: UserProject::class, mappedBy: "project")]
    private Collection $userProjects;

    #[Groups(['project_ticketType'])]
    #[ORM\OneToMany(mappedBy: 'project', targetEntity: TicketType::class)]
    private Collection $ticketTypes;

    public function __construct()
    {
        $this->userTypes = new ArrayCollection();
        $this->status = new ArrayCollection();
        $this->sprints = new ArrayCollection();
        $this->userProjects = new ArrayCollection();
        $this->ticketTypes = new ArrayCollection();
    }

    public function getId(): ?Ulid
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function setIsDeleted(?bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted ? '1' : '0';
        return $this;
    }

    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    /**
     * @return Collection<Ulid, UserType>
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
        $this->userTypes->removeElement($userType);

        return $this;
    }

    /**
     * @return Collection<Ulid, Status>
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
        $this->status->removeElement($status);
        // Suppression de la ligne à l'origine du bug
        // $status->setProject(null);

        return $this;
    }

    /**
     * @return Collection<Ulid, UserProject>
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
        $this->userProjects->removeElement($userProject);

        return $this;
    }

    /**
     * @return Collection<Ulid, Sprint>
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
        $this->sprints->removeElement($sprint);

        return $this;
    }

    /**
     * @return Collection<Ulid, TicketType>
     */
    public function getTicketTypes(): Collection
    {
        return $this->ticketTypes;
    }

    public function addTicketType(TicketType $ticketType): self
    {
        if (!$this->ticketTypes->contains($ticketType)) {
            $this->ticketTypes->add($ticketType);
            $ticketType->setProject($this);
        }

        return $this;
    }

    public function removeTicketType(TicketType $ticketType): self
    {
        $this->ticketTypes->removeElement($ticketType);

        return $this;
    }
}
