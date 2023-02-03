<?php

namespace App\Entity;

use App\Repository\SprintRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SprintRepository::class)]
class Sprint
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['sprint'])]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['sprint'])]
    #[ORM\Column(type: 'string', length: 45)]
    private $name;

    #[Groups(['sprint'])]
    #[ORM\Column(type: 'datetime')]
    private $startDate;

    #[Groups(['sprint'])]
    #[ORM\Column(type: 'datetime')]
    private $endDate;

    #[Groups(['sprint_user'])]
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'sprints')]
    private Collection $users;

    #[Groups(['sprint_ticket'])]
    #[ORM\ManyToMany(targetEntity: Ticket::class, mappedBy: 'sprints')]
    private Collection $tickets;

    #[Groups(['sprint_project'])]
    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'sprints')]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
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

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->addSprint($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->removeElement($ticket)) {
            $ticket->removeSprint($this);
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
