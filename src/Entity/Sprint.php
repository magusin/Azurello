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
    private $start_date;

    #[Groups(['sprint'])]
    #[ORM\Column(type: 'datetime')]
    private $end_date;

    #[Groups(['sprint_user'])]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'sprint_created')]
    #[ORM\JoinColumn(nullable: false)]
    private $user_creator;

    #[Groups(['sprint_user'])]
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'sprints')]
    private $user;

    #[Groups(['sprint_userStory'])] 
    #[ORM\ManyToMany(targetEntity: UserStory::class, mappedBy: 'sprints')]
    private $user_stories;

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->userstories = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUserCreator(): ?User
    {
        return $this->user_creator;
    }

    public function setUserCreator(?User $user_creator): self
    {
        $this->user_creator = $user_creator;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->user->removeElement($user);

        return $this;
    }

    /**
     * @return Collection<int, UserStory>
     */
    public function getUserStories(): Collection
    {
        return $this->user_stories;
    }

    public function addUserStory(Userstory $user_story): self
    {
        if (!$this->user_stories->contains($user_story)) {
            $this->user_stories->add($user_story);
            $user_story->addSprint($this);
        }

        return $this;
    }

    public function removeUserstory(Userstory $user_story): self
    {
        if ($this->user_stories->removeElement($user_story)) {
            $user_story->removeSprint($this);
        }

        return $this;
    }
}
