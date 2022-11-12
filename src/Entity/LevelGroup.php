<?php

namespace App\Entity;

use App\Repository\LevelGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: LevelGroupRepository::class)]
class LevelGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['levelGroup'])]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['levelGroup'])]
    #[ORM\Column(type: 'string', length: 45)]
    private $name;

    #[Groups(['levelGroup_childrens'])]
    #[ORM\OneToMany(targetEntity: LevelGroup::class, mappedBy: 'parent')]
    #[ORM\JoinColumn(nullable: true)]
    private Collection $childrens;

    #[Groups(['levelGroup_parent'])]
    #[ORM\ManyToOne(targetEntity: LevelGroup::class, inversedBy: 'childrens')]
    #[ORM\JoinColumn(nullable: true)]
    private LevelGroup $parent;

    #[Groups(['levelGroup_project'])]
    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'levelGroups')]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    #[Groups(['levelGroup_ticket'])]
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'levelGroup')]
    private Collection $tickets;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->childrens = new ArrayCollection();
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

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getParent(): ?LevelGroup
    {
        return $this->parent;
    }

    public function setParent(?LevelGroup $levelGroup): self
    {
        $this->parent = $levelGroup;

        return $this;
    }

    public function removeParent(): self
    {
        unset($this->parent);

        return $this;
    }

    /**
     * @return Collection<int, LevelGroup>
     */
    public function getChildrens(): Collection
    {
        return $this->childrens;
    }

    public function addChildren(LevelGroup $levelGroup): self
    {
        if (!$this->childrens->contains($levelGroup)) {
            $this->childrens->add($levelGroup);
            $levelGroup->setParent($this);
        }

        return $this;
    }

    public function removeChildren(LevelGroup $levelGroup): self
    {
        if ($this->groupChildrens->removeElement($levelGroup)) {
            // set the owning side to null (unless already changed)
            if ($levelGroup->getParent() === $this) {
                $levelGroup->setParent(null);
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
            $ticket->setLevelGroup($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getLevelGroup() === $this) {
                $ticket->setLevelGroup(null);
            }
        }

        return $this;
    }
}
