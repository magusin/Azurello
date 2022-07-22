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
    private $registration_at;

    #[Groups(['user'])]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $last_connection_at;

    #[Groups(['user_sprint'])]
    #[ORM\OneToMany(mappedBy: 'user_creator', targetEntity: Sprint::class)]
    private $sprint_created;

    #[Groups(['user_sprint'])]
    #[ORM\ManyToMany(targetEntity: Sprint::class, mappedBy: 'user')]
    private $sprints;

    #[Groups(['user_task'])]
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Task::class)]
    private $tasks;

    public function __construct()
    {
        $this->sprint_created = new ArrayCollection();
        $this->sprints = new ArrayCollection();
        $this->tasks = new ArrayCollection();
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
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
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
        return $this->registration_at;
    }

    public function setRegistrationAt(\DateTimeInterface $registration_at): self
    {
        $this->registration_at = $registration_at;

        return $this;
    }

    public function getLastConnectionAt(): ?\DateTimeInterface
    {
        return $this->last_connection_at;
    }

    public function setLastConnectionAt(?\DateTimeInterface $last_connection_at): self
    {
        $this->last_connection_at = $last_connection_at;

        return $this;
    }

    /**
     * @return Collection<int, Sprint>
     */
    public function getSprintCreated(): Collection
    {
        return $this->sprint_created;
    }

    public function addSprintCreated(Sprint $sprintCreated): self
    {
        if (!$this->sprint_created->contains($sprintCreated)) {
            $this->sprint_created[] = $sprintCreated;
            $sprintCreated->setUserCreator($this);
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
            $this->tasks[] = $task;
            $task->setUser($this);
        }

        return $this;
    }

    public function removeSprintCreated(Sprint $sprintCreated): self
    {
        if ($this->sprint_created->removeElement($sprintCreated)) {
            // set the owning side to null (unless already changed)
            if ($sprintCreated->getUserCreator() === $this) {
                $sprintCreated->setUserCreator(null);
            }
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getUser() === $this) {
                $task->setUser(null);
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
}
