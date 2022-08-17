<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['project'])]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['project'])]
    #[ORM\Column(type: 'string', length: 45)]
    private $name;

    #[Groups(['project'])]
    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[Groups(['project'])]
    #[ORM\Column(type: 'string', length: 45)]
    private $created_by;

    #[Groups(['project'])]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updated_at;

    #[Groups(['project'])]
    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private $updated_by;

    #[Groups(['project'])]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $deleted_at;

    #[Groups(['project'])]
    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private $deleted_by;

    #[Groups(['project_userStory'])]
    #[ORM\OneToMany(mappedBy: 'project', targetEntity: UserStory::class, orphanRemoval: true)]
    private $user_stories;

    #[ORM\OneToMany(mappedBy: 'project_owner', targetEntity: UserType::class)]
    private $userTypes;

    public function __construct()
    {
        $this->userTypes_id = new ArrayCollection();
        $this->user_stories = new ArrayCollection();
        $this->userTypes = new ArrayCollection();
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->created_by;
    }

    public function setCreatedBy(string $created_by): self
    {
        $this->created_by = $created_by;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updated_by;
    }

    public function setUpdatedBy(?string $updated_by): self
    {
        $this->updated_by = $updated_by;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?\DateTimeInterface $deleted_at): self
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    public function getDeletedBy(): ?string
    {
        return $this->deleted_by;
    }

    public function setDeletedBy(?string $deleted_by): self
    {
        $this->deleted_by = $deleted_by;

        return $this;
    }

    /**
     * @return Collection<int, UserStory>
     */
    public function getUserStories(): Collection
    {
        return $this->user_stories;
    }

    public function addUserStory(UserStory $user_story): self
    {
        if (!$this->user_stories->contains($user_story)) {
            $this->user_stories[] = $user_story;
            $user_story->setProject($this);
        }

        return $this;
    }

    public function removeUserStory(UserStory $user_story): self
    {
        if ($this->user_stories->removeElement($user_story)) {
            // set the owning side to null (unless already changed)
            if ($user_story->getProject() === $this) {
                $user_story->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserType>
     */
    public function getUserTypes(): Collection
    {
        return $this->userTypes;
    }

    public function addUserType(UserType $userType): self
    {
        if (!$this->userTypes->contains($userType)) {
            $this->userTypes->add($userType);
            $userType->setProjectOwner($this);
        }

        return $this;
    }

    public function removeUserType(UserType $userType): self
    {
        if ($this->userTypes->removeElement($userType)) {
            // set the owning side to null (unless already changed)
            if ($userType->getProjectOwner() === $this) {
                $userType->setProjectOwner(null);
            }
        }

        return $this;
    }
}
