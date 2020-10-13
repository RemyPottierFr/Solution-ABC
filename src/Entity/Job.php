<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\JobRepository")
 */
class Job
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Member", inversedBy="jobs")
     */
    private $members;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nameJob;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Prestation", mappedBy="job", orphanRemoval=true)
     */
    private $prestations;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->prestations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Member[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMembers(Member $members): self
    {
        if (!$this->members->contains($members)) {
            $this->members[] = $members;
        }

        return $this;
    }

    public function removeMembers(Member $members): self
    {
        if ($this->members->contains($members)) {
            $this->members->removeElement($members);
        }

        return $this;
    }

    public function getNameJob(): ?string
    {
        return $this->nameJob;
    }

    public function setNameJob(string $nameJob): self
    {
        $this->nameJob = $nameJob;

        return $this;
    }

    /**
     * @return Collection|Prestation[]
     */
    public function getPrestations(): Collection
    {
        return $this->prestations;
    }

    public function addPrestation(Prestation $prestation): self
    {
        if (!$this->prestations->contains($prestation)) {
            $this->prestations[] = $prestation;
            $prestation->setJob($this);
        }

        return $this;
    }

    public function removePrestation(Prestation $prestation): self
    {
        if ($this->prestations->contains($prestation)) {
            $this->prestations->removeElement($prestation);
            // set the owning side to null (unless already changed)
            if ($prestation->getJob() === $this) {
                $prestation->setJob(null);
            }
        }

        return $this;
    }

    public function addMember(Member $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
        }

        return $this;
    }

    public function removeMember(Member $member): self
    {
        if ($this->members->contains($member)) {
            $this->members->removeElement($member);
        }

        return $this;
    }
}
