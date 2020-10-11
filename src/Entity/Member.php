<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MemberRepository")
 * @UniqueEntity(fields={"registerEmail"}, message="cette email existe deja")
 * @UniqueEntity(fields={"name"}, message="cette utilisateur existe deja")
 */
class Member implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Regex(
     *     pattern="/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{4,255}$/",
     *     match=true,
     *     message="Invalide caractere")
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Le champs ne doit pas etre vide")
     * @Assert\Length(max="255",
     *      maxMessage="La champs {{ value }} est trop long,
     *      \il ne devrait pas dépasser {{ limit }} caractères")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *     pattern="/^(?=.*[a-z])(?=.*\W).{4,255}$/",
     *     match=true,
     *     message="Invalide caractere")
     * @Assert\NotBlank(message="Le champs ne doit pas etre vide")
     * @Assert\Length(max="255",
     *      maxMessage="La champs {{ value }} est trop long,il ne devrait pas dépasser {{ limit }} caractères")
     */
    private $company;

    /**
     * @Assert\Regex(
     *     pattern="/^\+?\s*(\d{2}\s?){5,5}$/",
     *     match=true,
     *     message="Votre numero de telephone ne correspond pas")
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le champs ne doit pas etre vide")
     * @Assert\Length(max="255",
     *      maxMessage="La champs {{ value }} est trop long,il ne devrait pas dépasser {{ limit }} caractères")
     */
    private $phonenumber;

    /**
     * @Assert\Regex(
     *     pattern="/^(?=.*[a-z])(?=.*\W).{4,255}$/",
     *     match=true,
     *     message="Invalide caractere")
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le champs ne doit pas etre vide")
     * @Assert\Length(max="255",
     *      maxMessage="La champs {{ value }} est trop long,il ne devrait pas dépasser {{ limit }} caractères")
     */
    private $workingLocation;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le champs ne doit pas etre vide")
     */
    private $profileImage;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Recommendation", mappedBy="owner")
     */
    private $recommendations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Recommendation", mappedBy="target")
     */
    private $assignedReco;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Job", mappedBy="members")
     */
    private $jobs;
    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $registerEmail;
    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];
    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;
    /**
     * @Assert\Regex(
     *     pattern="/^(([0-8][0-9])|(9[0-5]))[0-9]{3}$/",
     *     match=true,
     *     message="Invalide caractere")
     * @ORM\Column(type="string", length=255)
     */
    private $postCode;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    public function __construct()
    {
        $this->assignedReco = new ArrayCollection();
        $this->jobs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRegisterEmail(): ?string
    {
        return $this->registerEmail;
    }

    public function setRegisterEmail(string $registerEmail): self
    {
        $this->registerEmail = $registerEmail;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->registerEmail;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getPhonenumber(): ?string
    {
        return $this->phonenumber;
    }

    public function setPhonenumber(string $phonenumber): self
    {
        $this->phonenumber = $phonenumber;

        return $this;
    }

    public function getWorkingLocation(): ?string
    {
        return $this->workingLocation;
    }

    public function setWorkingLocation(string $workingLocation): self
    {
        $this->workingLocation = $workingLocation;

        return $this;
    }

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImage(string $profileImage): self
    {
        $this->profileImage = $profileImage;

        return $this;
    }

    /**
     * @return Collection|Recommendation[]
     */
    public function getRecommendations(): Collection
    {
        return $this->recommendations;
    }

    public function addRecommendation(Recommendation $recommendation): self
    {
        if (!$this->recommendations->contains($recommendation)) {
            $this->recommendations[] = $recommendation;
            $recommendation->setOwner($this);
        }

        return $this;
    }

    public function removeRecommendation(Recommendation $recommendation): self
    {
        if ($this->recommendations->contains($recommendation)) {
            $this->recommendations->removeElement($recommendation);
            // set the owning side to null (unless already changed)
            if ($recommendation->getOwner() === $this) {
                $recommendation->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Recommendation[]
     */
    public function getAssignedReco(): Collection
    {
        return $this->assignedReco;
    }

    public function addAssignedReco(Recommendation $assignedReco): self
    {
        if (!$this->assignedReco->contains($assignedReco)) {
            $this->assignedReco[] = $assignedReco;
            $assignedReco->setTarget($this);
        }

        return $this;
    }

    public function removeAssignedReco(Recommendation $assignedReco): self
    {
        if ($this->assignedReco->contains($assignedReco)) {
            $this->assignedReco->removeElement($assignedReco);
            // set the owning side to null (unless already changed)
            if ($assignedReco->getTarget() === $this) {
                $assignedReco->setTarget(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Job[]
     */
    public function getJobs(): Collection
    {
        return $this->jobs;
    }

    public function addJob(Job $job): self
    {
        if (!$this->jobs->contains($job)) {
            $this->jobs[] = $job;
            $job->addMembers($this);
        }

        return $this;
    }

    public function removeJob(Job $job): self
    {
        if ($this->jobs->contains($job)) {
            $this->jobs->removeElement($job);
            $job->removeMembers($this);
        }

        return $this;
    }

    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    public function setPostCode(string $postCode): self
    {
        $this->postCode = $postCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }
}
