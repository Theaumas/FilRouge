<?php

namespace App\Entity;

use App\Enum\ProjetStatus;
use App\Repository\ProjetsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjetsRepository::class)]
class Projets
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $DateLimite = null;

    #[ORM\Column(type: 'string', enumType: ProjetStatus::class)]
    private ProjetStatus $Statut;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $DateCreation = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'projets')]
    #[ORM\JoinTable(name: 'projets_users')]
    private Collection $Membres;

    #[ORM\OneToMany(mappedBy: "projet", targetEntity: Tache::class, cascade: ["persist", "remove"])]
    private Collection $taches;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable:false)]
    private $creator;

    public function __construct()
    {
        $this->Membres = new ArrayCollection();
        $this->DateCreation = new \DateTime();
        $this->taches = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->Nom;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): static
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): static
    {
        $this->Description = $Description;

        return $this;
    }

    public function getDateLimite(): ?\DateTimeInterface
    {
        return $this->DateLimite;
    }

    public function setDateLimite(\DateTimeInterface $DateLimite): static
    {
        $this->DateLimite = $DateLimite;

        return $this;
    }

    public function getStatut(): ProjetStatus
    {
        return $this->Statut;
    }

    public function setStatut(ProjetStatus $Statut): self
    {
        $this->Statut = $Statut;
        return $this;
    }

    public function addMembre(User $user): self
    {
        if (!$this->Membres->contains($user)) {
            $this->Membres[] = $user;
    
            $user->addProjet($this); 
        }
    
        return $this;
    }

    public function getMembres(): Collection
    {
        return $this->Membres;
    }

    public function removeMembre(User $user): self
    {
        if ($this->Membres->removeElement($user)) {
            $user->removeProjet($this); 
        }
    
        return $this;
    }

    public function getDateCreation(): \DateTimeInterface
    {
        return $this->DateCreation;
    }

    public function setDateCreation(\DateTimeInterface $DateCreation): self
    {
        $this->DateCreation = $DateCreation;
        return $this;
    }

    public function getTaches(): Collection
    {
        return $this->taches;
    }

    public function addTache(Tache $tache): self
    {
        if (!$this->taches->contains($tache)) {
            $this->taches->add($tache);
            $tache->setProjet($this);
        }

        return $this;
    }

    public function removeTache(Tache $tache): self
    {
        if ($this->taches->removeElement($tache)) {
            if ($tache->getProjet() === $this) {
                $tache->setProjet(null);
            }
        }

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }
}
