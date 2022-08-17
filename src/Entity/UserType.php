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

    #[ORM\ManyToOne(inversedBy: 'userTypes')]
    #[ORM\JoinColumn(nullable: false)]
    private $project_owner;

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
}
