<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['user'])]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['user'])]
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[Groups(['user'])]
    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[Groups(['user'])]
    #[ORM\Column(type: 'string')]
    private $password;

    #[Groups(['user'])]
    #[ORM\Column(type: 'string', length: 40)]
    private $firstname;

    #[Groups(['user'])]
    #[ORM\Column(type: 'string', length: 40)]
    private $lastname;

    #[Groups(['user'])]
    #[ORM\Column(type: 'datetime')]
    private $registrationAt;

    #[Groups(['user'])]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $lastConnectionAt;

    #[Groups(['user_sprint'])]
    #[ORM\ManyToMany(targetEntity: Sprint::class, mappedBy: 'user')]
    private Collection $sprints;

    #[Groups(['user_ticketTask'])]
    #[ORM\OneToMany(targetEntity: TicketTask::class, mappedBy: 'user')]
    private Collection $ticketTasks;

    #[Groups(['user_userProject'])]
    #[ORM\OneToMany(targetEntity: UserProject::class, mappedBy: 'user')]
    private Collection $userProjects;

    #[Groups(['user_ticket'])]
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Ticket::class)]
    private Collection $tickets;

    public function __construct()
    {
        $this->sprints = new ArrayCollection();
        $this->ticketTasks = new ArrayCollection();
        $this->userProjects = new ArrayCollection();
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $userRoles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $userRoles[] = 'ROLE_USER';

        return array_unique($userRoles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getRegistrationAt(): ?\DateTimeInterface
    {
        return $this->registrationAt;
    }

    public function setRegistrationAt(\DateTimeInterface $registrationAt): self
    {
        $this->registrationAt = $registrationAt;

        return $this;
    }

    public function getLastConnectionAt(): ?\DateTimeInterface
    {
        return $this->lastConnectionAt;
    }

    public function setLastConnectionAt(?\DateTimeInterface $lastConnectionAt): self
    {
        $this->lastConnectionAt = $lastConnectionAt;

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
            $this->sprints[] = $sprint;
            $sprint->addUser($this);
        }

        return $this;
    }

    public function removeSprint(Sprint $sprint): self
    {
        if ($this->sprints->removeElement($sprint)) {
            $sprint->removeUser($this);
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
            $ticketTask->setUser($this);
        }

        return $this;
    }

    public function removeTicketTask(TicketTask $ticketTask): self
    {
        if ($this->ticketTasks->removeElement($ticketTask)) {
            // set the owning side to null (unless already changed)
            if ($ticketTask->getUser() === $this) {
                $ticketTask->setUser(null);
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
            $userProject->setUser($this);
        }

        return $this;
    }

    public function removeUserProject(UserProject $userProject): self
    {
        if ($this->userProjects->removeElement($userProject)) {
            // set the owning side to null (unless already changed)
            if ($userProject->getUser() === $this) {
                $userProject->setUser(null);
            }
        }

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
            $ticket->setUser($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getUser() === $this) {
                $ticket->setUser(null);
            }
        }

        return $this;
    }
}
