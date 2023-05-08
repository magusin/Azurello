<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    #[ORM\Id]
    #[Groups(['ticket'])]
    #[ORM\Column(type: "ulid", unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    private $id;

    #[Groups(['ticket'])]
    #[ORM\Column(type: 'string', length: 45)]
    private $name;

    #[Groups(['ticket'])]
    #[ORM\Column(type: 'integer', nullable: true)]
    private $storyPoints;

    #[Groups(['ticket_detail'])]
    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[Groups(['ticket_detail'])]
    #[ORM\Column(type: 'string', length: 45)]
    private $createdBy;

    #[Groups(['ticket_detail'])]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    #[Groups(['ticket_detail'])]
    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private $updatedBy;

    #[Groups(['ticket_detail'])]
    #[ORM\Column(type: 'boolean', nullable: true)]
    private $isDeleted = false;

    #[Groups(['ticket_childrens'])]
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'parent')]
    #[ORM\JoinColumn(nullable: true)]
    private Collection $childrens;

    #[Groups(['ticket_parent'])]
    #[ORM\ManyToOne(targetEntity: Ticket::class, inversedBy: 'childrens')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Ticket $parent = null;

    #[Groups(['ticket_status'])]
    #[ORM\ManyToOne(targetEntity: Status::class, inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private Status $status;

    #[Groups(['ticket_sprint'])]
    #[ORM\ManyToMany(targetEntity: Sprint::class, inversedBy: 'tickets')]
    private Collection $sprints;

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
        $this->childrens = new ArrayCollection();
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

    public function getStoryPoints(): ?int
    {
        return $this->storyPoints;
    }

    public function setStoryPoints(int $storyPoints): self
    {
        $this->storyPoints = $storyPoints;

        return $this;
    }

    public function getParent(): ?Ticket
    {
        return $this->parent;
    }

    public function setParent(?Ticket $ticket): self
    {
        $this->parent = $ticket;

        return $this;
    }

    public function removeParent(): self
    {
        unset($this->parent);

        return $this;
    }

    /**
     * @return Collection<Ulid, Ticket>
     */
    public function getChildrens(): Collection
    {
        return $this->childrens;
    }

    public function addChildren(Ticket $ticket): self
    {
        if (!$this->childrens->contains($ticket)) {
            $this->childrens->add($ticket);
            $ticket->setParent($this);
        }

        return $this;
    }

    public function removeChildren(Ticket $ticket): self
    {
        if ($this->childrens->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getParent() === $this) {
                $ticket->setParent(null);
            }
        }

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
     * @return Collection<Ulid, Sprint>
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
