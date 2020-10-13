<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecommendationRepository")
 */
class Recommendation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Member", inversedBy="recommendations")
     */
    private $owner;

    /**
     * @ORM\Column(type="text")
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Member", inversedBy="assignedReco")
     */
    private $target;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Client", mappedBy="recommendation")
     */
    private $clients;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $clientName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $infoClient;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Prestation", inversedBy="recommendations")
     */
    private $prestations;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    private $displayClient;

    public function __construct()
    {
        $this->clients = new ArrayCollection();
        $this->prestations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?Member
    {
        return $this->owner;
    }

    public function setOwner(?Member $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getTarget(): ?Member
    {
        return $this->target;
    }

    public function setTarget(?Member $target): self
    {
        $this->target = $target;

        return $this;
    }

    /**
     * @return Collection|Client[]
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): self
    {
        if (!$this->clients->contains($client)) {
            $this->clients[] = $client;
            $client->setRecommendation($this);
        }

        return $this;
    }

    public function removeClient(Client $client): self
    {
        if ($this->clients->contains($client)) {
            $this->clients->removeElement($client);
            // set the owning side to null (unless already changed)
            if ($client->getRecommendation() === $this) {
                $client->setRecommendation(null);
            }
        }

        return $this;
    }

    public function getClientName(): ?string
    {
        return $this->clientName;
    }

    public function setClientName(string $clientName): self
    {
        $this->clientName = $clientName;

        return $this;
    }

    public function getInfoClient(): ?string
    {
        return $this->infoClient;
    }

    public function setInfoClient(string $infoClient): self
    {
        $this->infoClient = $infoClient;

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
        }

        return $this;
    }

    public function removePrestation(Prestation $prestation): self
    {
        if ($this->prestations->contains($prestation)) {
            $this->prestations->removeElement($prestation);
        }

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDisplayClient(): ?bool
    {
        return $this->displayClient;
    }

    public function setDisplayClient(bool $displayClient): self
    {
        $this->displayClient = $displayClient;

        return $this;
    }
}
