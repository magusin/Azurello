<?php

namespace App\Entity;

use App\Repository\UserStoryGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserStoryGroupRepository::class)]
class UserStoryGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['userStoryGroup'])]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['userStoryGroup'])]
    #[ORM\Column(type: 'string', length: 45)]
    private $name;

    #[Groups(['userStoryGroup_groupChildrens'])]
    #[ORM\OneToMany(targetEntity: UserStoryGroup::class, mappedBy: 'group_parent')]
    private Collection $group_childrens;

    #[Groups(['userStoryGroup_groupParent'])]
    #[ORM\ManyToOne(targetEntity: UserStoryGroup::class, inversedBy: 'group_childrens')]
    #[ORM\JoinColumn(nullable: true)]
    private UserStoryGroup $group_parent;

    #[Groups(['userStoryGroup_project'])]
    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'groups')]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    #[Groups(['userStoryGroup_userStory'])]
    #[ORM\OneToMany(targetEntity: UserStory::class, mappedBy: 'group')]
    private Collection $user_stories;

    public function __construct()
    {
        $this->user_stories = new ArrayCollection();
        $this->group_childrens = new ArrayCollection();
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

    public function getGroupParent(): ?UserStoryGroup
    {
        return $this->group_parent;
    }

    public function setGroupParent(?UserStoryGroup $userStoryGroup): self
    {
        $this->group_parent = $userStoryGroup;

        return $this;
    }

    /**
     * @return Collection<int, UserStoryGroup>
     */
    public function getGroupChildrens(): Collection
    {
        return $this->group_childrens;
    }

    public function addGroupChildren(UserStoryGroup $userStoryGroup): self
    {
        if (!$this->group_childrens->contains($userStoryGroup)) {
            $this->group_childrens->add($userStoryGroup);
            $userStoryGroup->setGroupParent($this);
        }

        return $this;
    }

    public function removeGroupChildren(UserStoryGroup $userStoryGroup): self
    {
        if ($this->group_childrens->removeElement($userStoryGroup)) {
            // set the owning side to null (unless already changed)
            if ($userStoryGroup->getGroupParent() === $this) {
                $userStoryGroup->setGroupParent(null);
            }
        }

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
            $user_story->setUserStoryGroup($this);
        }

        return $this;
    }

    public function removeUserStory(UserStory $user_story): self
    {
        if ($this->user_stories->removeElement($user_story)) {
            // set the owning side to null (unless already changed)
            if ($user_story->getUserStoryGroup() === $this) {
                $user_story->setUserStoryGroup(null);
            }
        }

        return $this;
    }

}
