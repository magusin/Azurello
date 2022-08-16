<?php

namespace App\Entity;

use App\Repository\StatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: StatusRepository::class)]
class Status
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['status'])] 
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['status'])] 
    #[ORM\Column(type: 'string', length: 45)]
    private $label;

    #[Groups(['status_userStory'])] 
    #[ORM\OneToMany(mappedBy: 'status', targetEntity: UserStory::class, orphanRemoval: true)]
    private $user_stories;

    public function __construct()
    {
        $this->user_stories = new ArrayCollection();
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
     * @return Collection<int, UserStory>
     */
    public function getUserStory(): Collection
    {
        return $this->user_stories;
    }

    public function addUserStory(UserStory $user_story): self
    {
        if (!$this->user_stories->contains($user_story)) {
            $this->user_stories[] = $user_story;
            $user_story->setStatus($this);
        }

        return $this;
    }

    public function removeUserStory(UserStory $user_story): self
    {
        if ($this->user_stories->removeElement($user_story)) {
            // set the owning side to null (unless already changed)
            if ($user_story->getStatus() === $this) {
                $user_story->setStatus(null);
            }
        }

        return $this;
    }
}
