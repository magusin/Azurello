<?php

namespace App\Entity;

use App\Repository\SprintRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SprintRepository::class)]
class Sprint
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $start_date;

    #[ORM\Column(type: 'datetime')]
    private $end_date;

    #[ORM\Column(type: 'string', length: 45)]
    private $sprint_name;

    #[ORM\ManyToOne(targetEntity: user::class, inversedBy: 'sprint_created')]
    #[ORM\JoinColumn(nullable: false)]
    private $user_creator;

    #[ORM\ManyToMany(targetEntity: user::class, inversedBy: 'sprints')]
    private $user;

    public function __construct()
    {
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTimeInterface $start_date): self
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->end_date;
    }

    public function setEndDate(\DateTimeInterface $end_date): self
    {
        $this->end_date = $end_date;

        return $this;
    }

    public function getSprintName(): ?string
    {
        return $this->sprint_name;
    }

    public function setSprintName(string $sprint_name): self
    {
        $this->sprint_name = $sprint_name;

        return $this;
    }

    public function getUserCreator(): ?user
    {
        return $this->user_creator;
    }

    public function setUserCreator(?user $user_creator): self
    {
        $this->user_creator = $user_creator;

        return $this;
    }

    /**
     * @return Collection<int, user>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(user $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
        }

        return $this;
    }

    public function removeUser(user $user): self
    {
        $this->user->removeElement($user);

        return $this;
    }
}
