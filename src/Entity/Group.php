<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['group'])]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['group'])]
    #[ORM\Column(length: 45)]
    private $name;

    // TODO 
    #[Groups(['group_group'])]
    #[ORM\ManyToOne(targetEntity: Group::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $groups;

    #[Groups(['group_project'])]
    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'groups')]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    #[Groups(['group_userStory'])]
    #[ORM\OneToMany(targetEntity: UserStory::class, mappedBy: 'group')]
    private Collection $user_stories;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
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

    /**
     * @return Collection<int, Group>
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
            // $group->setGroup($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->removeElement($group)) {
            // set the owning side to null (unless already changed)
            // if ($group->getGroup() === $this) {
            //     $group->setGroup(null);
            // }
        }

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
     * @return Collection<int, UserStory>
     */
    public function getUserStories(): Collection
    {
        return $this->user_stories;
    }

    public function addUserStory(UserStory $user_story): self
    {
        if (!$this->user_stories->contains($user_story)) {
            $this->user_stories->add($user_story);
            $user_story->setGroup($this);
        }

        return $this;
    }

    public function removeUserStory(UserStory $user_story): self
    {
        if ($this->user_stories->removeElement($user_story)) {
            // set the owning side to null (unless already changed)
            if ($user_story->getGroup() === $this) {
                $user_story->setGroup(null);
            }
        }

        return $this;
    }
}
