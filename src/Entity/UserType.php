<?php

namespace App\Entity;

use App\Repository\UserTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserTypeRepository::class)]
class UserType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 45)]
    private $label;

    #[ORM\ManyToMany(targetEntity: Project::class, inversedBy: 'userTypes_id')]
    private $project_id;

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

    /**
     * @return Collection<int, Project>
     */
    public function getProjectId(): Collection
    {
        return $this->project_id;
    }

    public function addProjectId(Project $projectId): self
    {
        if (!$this->project_id->contains($projectId)) {
            $this->project_id[] = $projectId;
        }

        return $this;
    }

    public function removeProjectId(Project $projectId): self
    {
        $this->project_id->removeElement($projectId);

        return $this;
    }
}
