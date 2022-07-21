<?php

namespace App\Entity;

use App\Repository\GroupTaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GroupTaskRepository::class)]
class GroupTask
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['group_task', 'group_task_details'])]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['group_task_details'])]
    #[ORM\ManyToOne(targetEntity: GroupTask::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $grouptasks;

    public function __construct()
    {
        $this->grouptasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Grouptask>
     */
    public function getGrouptasks(): Collection
    {
        return $this->grouptasks;
    }

    public function addGrouptask(GroupTask $grouptask): self
    {
        if (!$this->grouptasks->contains($grouptask)) {
            $this->grouptasks[] = $grouptask;
            // $grouptask->setGroupTask($this);
        }

        return $this;
    }

    public function removeGrouptask(GroupTask $grouptask): self
    {
        if ($this->grouptasks->removeElement($grouptask)) {
            // set the owning side to null (unless already changed)
            // if ($grouptask->getGroupTask() === $this) {
            //     $grouptask->setGroupTask(null);
            // }
        }

        return $this;
    }
}
