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

    #[Groups(['status_ticket'])]
    #[ORM\OneToMany(mappedBy: 'status', targetEntity: Ticket::class, orphanRemoval: true)]
    private Collection $tickets;

    #[Groups(['status_ticketTask'])]
    #[ORM\OneToMany(mappedBy: 'status', targetEntity: TicketTask::class, orphanRemoval: true)]
    private Collection $ticketTasks;

    #[Groups(['status_project'])]
    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'status')]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->ticketTasks = new ArrayCollection();
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
            $ticket->setStatus($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getStatus() === $this) {
                $ticket->setStatus(null);
            }
        }

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
            $ticketTask->setStatus($this);
        }

        return $this;
    }

    public function removeTicketTask(TicketTask $ticketTask): self
    {
        if ($this->ticketTasks->removeElement($ticketTask)) {
            // set the owning side to null (unless already changed)
            if ($ticketTask->getStatus() === $this) {
                $ticketTask->setStatus(null);
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
