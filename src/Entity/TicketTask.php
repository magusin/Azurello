<?php

namespace App\Entity;

use App\Repository\TicketTaskRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TicketTaskRepository::class)]
class TicketTask
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['ticketTask'])]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['ticketTask'])]
    #[ORM\Column(length: 45)]
    private $name;

    #[Groups(['ticketTask_ticket'])]
    #[ORM\ManyToOne(targetEntity: Ticket::class, inversedBy: 'ticketTasks')]
    #[ORM\JoinColumn(nullable: false)]
    private Ticket $ticket;

    #[Groups(['ticketTask_status'])]
    #[ORM\ManyToOne(targetEntity: Status::class, inversedBy: 'ticketTasks')]
    #[ORM\JoinColumn(nullable: false)]
    private Status $status;

    #[Groups(['ticketTask_user'])]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'ticketTasks')]
    private User $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    public function setTicket(?Ticket $ticket): self
    {
        $this->ticket = $ticket;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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
}
