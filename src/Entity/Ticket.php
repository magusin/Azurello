<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['ticket'])]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['ticket'])]
    #[ORM\Column(type: 'string', length: 45)]
    private $name;

    #[Groups(['ticket'])]
    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[Groups(['ticket'])]
    #[ORM\Column(type: 'string', length: 45)]
    private $createdBy;

    #[Groups(['ticket'])]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    #[Groups(['ticket'])]
    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private $updatedBy;

    #[Groups(['ticket'])]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $deletedAt;

    #[Groups(['ticket'])]
    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private $deletedBy;

    #[Groups(['ticket_levelGroup'])]
    #[ORM\ManyToOne(targetEntity: LevelGroup::class, inversedBy: 'tickets')]
    private LevelGroup $levelGroup;

    #[Groups(['ticket_status'])]
    #[ORM\ManyToOne(targetEntity: Status::class, inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private Status $status;

    #[Groups(['ticket_sprint'])]
    #[ORM\ManyToMany(targetEntity: Sprint::class, inversedBy: 'tickets')]
    private Collection $sprints;

    #[Groups(['ticket_ticketTask'])]
    #[ORM\OneToMany(targetEntity: TicketTask::class, mappedBy: 'ticket')]
    private Collection $ticketTasks;

    #[Groups(['ticket_ticketType'])]
    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private TicketType $ticketType;

    #[Groups(['ticket_user'])]
    #[ORM\ManyToOne(inversedBy: 'tickets')]
    private User $user;

    public function __construct()
    {
        $this->sprints = new ArrayCollection();
        $this->ticketTasks = new ArrayCollection();
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

    public function getLevelGroup(): ?LevelGroup
    {
        return $this->levelGroup;
    }

    public function setLevelGroup(?LevelGroup $levelGroup): self
    {
        $this->levelGroup = $levelGroup;

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
    public function getSprints(): Collection
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
     * @return Collection<int, TicketTask>
     */
    public function getTicketTasks(): Collection
    {
        return $this->ticketTasks;
    }

    public function addTicketTask(TicketTask $ticketTask): self
    {
        if (!$this->ticketTasks->contains($ticketTask)) {
            $this->ticketTasks->add($ticketTask);
            $ticketTask->setTicket($this);
        }

        return $this;
    }

    public function removeTicketTask(TicketTask $ticketTask): self
    {
        if ($this->ticketTasks->removeElement($ticketTask)) {
            // set the owning side to null (unless already changed)
            if ($ticketTask->getTicket() === $this) {
                $ticketTask->setTicket(null);
            }
        }

        return $this;
    }

    public function getTicketType(): ?TicketType
    {
        return $this->ticketType;
    }

    public function setTicketType(?TicketType $ticketType): self
    {
        $this->ticketType = $ticketType;

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
