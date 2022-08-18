<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserTypeRepository::class)]
class UserType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['userType'])]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['userType'])]
    #[ORM\Column(type: 'string', length: 45)]
    private $label;

    #[Groups(['userType_project'])]
    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'user_types')]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    #[Groups(['userType_userProject'])]
    #[ORM\OneToMany(targetEntity: UserProject::class, mappedBy: "user_type")]
    private Collection $user_projects;

    public function __construct()
    {
        $this->user_projects = new ArrayCollection();
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

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return Collection<int, UserProject>
     */
    public function getUserProjects(): Collection
    {
        return $this->user_projects;
    }

    public function addUserProject(UserProject $user_project): self
    {
        if (!$this->user_projects->contains($user_project)) {
            $this->user_projects->add($user_project);
            $user_project->setUserType($this);
        }

        return $this;
    }

    public function removeUserProject(UserProject $user_project): self
    {
        if ($this->user_projects->removeElement($user_project)) {
            // set the owning side to null (unless already changed)
            if ($user_project->getUserType() === $this) {
                $user_project->setUserType(null);
            }
        }

        return $this;
    }
}
