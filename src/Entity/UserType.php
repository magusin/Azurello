<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: UserTypeRepository::class)]
class UserType
{
    #[ORM\Id]
    #[Groups(['userType'])]
    #[ORM\Column(type: "ulid", unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    private $id;

    #[Groups(['userType'])]
    #[ORM\Column(type: 'string', length: 45)]
    private $label;

    #[Groups(['project'])]
    #[ORM\Column(type: 'boolean')]
    private $hasCreateTicketRule = false;

    #[Groups(['project'])]
    #[ORM\Column(type: 'boolean')]
    private $hasUpdateTicketRule = false;

    #[Groups(['project'])]
    #[ORM\Column(type: 'boolean')]
    private $hasSoftDeleteTicketRule = false;

    #[Groups(['project'])]
    #[ORM\Column(type: 'boolean')]
    private $hasCreateSprintRule = false;

    #[Groups(['project'])]
    #[ORM\Column(type: 'boolean')]
    private $hasUpdateSprintRule = false;

    #[Groups(['project'])]
    #[ORM\Column(type: 'boolean')]
    private $hasDeleteSprintRule = false;

    #[Groups(['project'])]
    #[ORM\Column(type: 'boolean')]
    private $hasInviteUserRule = false;

    #[Groups(['project'])]
    #[ORM\Column(type: 'boolean')]
    private $hasAssignMemberRule = false;

    #[Groups(['project'])]
    #[ORM\Column(type: 'boolean')]
    private $isOwner = false;

    #[Groups(['userType_project'])]
    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'userTypes')]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    #[Groups(['userType_userProject'])]
    #[ORM\OneToMany(targetEntity: UserProject::class, mappedBy: "userType")]
    private Collection $userProjects;

    public function __construct()
    {
        $this->userProjects = new ArrayCollection();
    }

    public function getId(): ?Ulid
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

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function setHasCreateTicketRule(?bool $hasCreateTicketRule): self
    {
        $this->hasCreateTicketRule = $hasCreateTicketRule ? '1' : '0';
        return $this;
    }

    public function getHasCreateTicketRule(): ?bool
    {
        return $this->hasCreateTicketRule;
    }

    public function setHasUpdateTicketRule(?bool $hasUpdateTicketRule): self
    {
        $this->hasUpdateTicketRule = $hasUpdateTicketRule ? '1' : '0';
        return $this;
    }

    public function getHasUpdateTicketRule(): ?bool
    {
        return $this->hasUpdateTicketRule;
    }

    public function setHasSoftDeleteTicketRule(?bool $hasSoftDeleteTicketRule): self
    {
        $this->hasSoftDeleteTicketRule = $hasSoftDeleteTicketRule ? '1' : '0';
        return $this;
    }

    public function getHasSoftDeleteTicketRule(): ?bool
    {
        return $this->hasSoftDeleteTicketRule;
    }

    public function setHasCreateSprintRule(?bool $hasCreateSprintRule): self
    {
        $this->hasCreateSprintRule = $hasCreateSprintRule ? '1' : '0';
        return $this;
    }

    public function getHasCreateSprintRule(): ?bool
    {
        return $this->hasCreateSprintRule;
    }

    public function setHasUpdateSprintRule(?bool $hasUpdateSprintRule): self
    {
        $this->hasUpdateSprintRule = $hasUpdateSprintRule ? '1' : '0';
        return $this;
    }

    public function getHasUpdateSprintRule(): ?bool
    {
        return $this->hasUpdateSprintRule;
    }

    public function setHasDeleteSprintRule(?bool $hasDeleteSprintRule): self
    {
        $this->hasDeleteSprintRule = $hasDeleteSprintRule ? '1' : '0';
        return $this;
    }

    public function getHasDeleteSprintRule(): ?bool
    {
        return $this->hasDeleteSprintRule;
    }

    public function setHasInviteUserRule(?bool $hasInviteUserRule): self
    {
        $this->hasInviteUserRule = $hasInviteUserRule ? '1' : '0';
        return $this;
    }

    public function getHasInviteUserRule(): ?bool
    {
        return $this->hasInviteUserRule;
    }

    public function setHasAssignMemberRule(?bool $hasAssignMemberRule): self
    {
        $this->hasAssignMemberRule = $hasAssignMemberRule ? '1' : '0';
        return $this;
    }

    public function getHasAssignMemberRule(): ?bool
    {
        return $this->hasAssignMemberRule;
    }

    public function setIsOwner(?bool $isOwner): self
    {
        $this->isOwner = $isOwner ? '1' : '0';
        return $this;
    }

    public function getIsOwner(): ?bool
    {
        return $this->isOwner;
    }

    /**
     * @return Collection<Ulid, UserProject>
     */
    public function getUserProjects(): Collection
    {
        return $this->userProjects;
    }

    public function addUserProject(UserProject $userProject): self
    {
        if (!$this->userProjects->contains($userProject)) {
            $this->userProjects->add($userProject);
            $userProject->setUserType($this);
        }

        return $this;
    }

    public function removeUserProject(UserProject $userProject): self
    {
        if ($this->userProjects->removeElement($userProject)) {
            // set the owning side to null (unless already changed)
            if ($userProject->getUserType() === $this) {
                $userProject->setUserType(null);
            }
        }

        return $this;
    }
}
