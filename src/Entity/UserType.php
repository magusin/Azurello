<?php

namespace App\Entity;

use App\Repository\UserTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
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
    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'userTypes')]
    #[ORM\JoinColumn(nullable: false)]
    private $project_owner;

    #[Groups(['userType_userProject'])] 
    #[ORM\OneToMany(mappedBy:"user_type" ,targetEntity: UserProject::class)]
    private $user_project;

    public function __construct()
    {
        $this->project_id = new ArrayCollection();
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

    public function getProjectOwner(): ?Project
    {
        return $this->project_owner;
    }

    public function setProjectOwner(?Project $project_owner): self
    {
        $this->project_owner = $project_owner;

        return $this;
    }

    

    /**
     * @return Collection<int, UserProject>
     */
    public function getUserProject(): Collection
    {
        return $this->user_project;
    }

    public function addUserProject(UserProject $user_project): self
    {
        if (!$this->user_project->contains($user_project)) {
            $this->user_project->add($user_project);
            $user_project->setUserType($this);
        }

        return $this;
    }

    public function removeUserProject(UserProject $user_project): self
    {
        if ($this->user_project->removeElement($user_project)) {
            // set the owning side to null (unless already changed)
            if ($user_project->getUserType() === $this) {
                $user_project->setUserType(null);
            }
        }

        return $this;
    }
}
