<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"pseudo"}, message="There is already an account with this pseudo")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="Veuillez renseigner ce champ !")
     */
    private $pseudo;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Veuillez renseigner ce champ !")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="organisateur")
     */
    private $sortiesOrganiser;

    /**
     * @ORM\ManyToMany(targetEntity=Sortie::class, mappedBy="participation")
     */
    private $sortiesParticipant;

    /**
     * @ORM\Column(type="string", length=120)
     * @Assert\NotBlank(message="Veuillez renseigner ce champ !")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=120)
     * @Assert\NotBlank(message="Veuillez renseigner ce champ !")
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=13, nullable=true)
     * @Assert\Length(min=10, max=13)
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Veuillez renseigner ce champ !")
     * @Assert\Email(message="veuiller renseigner un mail correct")
     */
    private $mail;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotBlank(message="Veuillez renseigner ce champ !")
     */
    private $actif;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class)
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Veuillez renseigner ce champ !")
     */
    private $campus;

    public function __construct()
    {
        $this->sortiesOrganiser = new ArrayCollection();
        $this->sortiesParticipant = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->pseudo;
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
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSortiesOrganiser(): Collection
    {
        return $this->sortiesOrganiser;
    }

    public function addSortiesOrganiser(Sortie $sortiesOrganiser): self
    {
        if (!$this->sortiesOrganiser->contains($sortiesOrganiser)) {
            $this->sortiesOrganiser[] = $sortiesOrganiser;
            $sortiesOrganiser->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSortiesOrganiser(Sortie $sortiesOrganiser): self
    {
        if ($this->sortiesOrganiser->removeElement($sortiesOrganiser)) {
            // set the owning side to null (unless already changed)
            if ($sortiesOrganiser->getOrganisateur() === $this) {
                $sortiesOrganiser->setOrganisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSortiesParticipant(): Collection
    {
        return $this->sortiesParticipant;
    }

    public function addSortiesParticipant(Sortie $sortiesParticipant): self
    {
        if (!$this->sortiesParticipant->contains($sortiesParticipant)) {
            $this->sortiesParticipant[] = $sortiesParticipant;
            $sortiesParticipant->addParticipation($this);
        }

        return $this;
    }

    public function removeSortiesParticipant(Sortie $sortiesParticipant): self
    {
        if ($this->sortiesParticipant->removeElement($sortiesParticipant)) {
            $sortiesParticipant->removeParticipation($this);
        }

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }
}
