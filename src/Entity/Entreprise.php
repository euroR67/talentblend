<?php

namespace App\Entity;

use App\Repository\EntrepriseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
class Entreprise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(length: 40)]
    private ?string $raisonSocial = null;

    #[ORM\Column(length: 30)]
    private ?string $secteur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $website = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $banniere = null;

    #[ORM\Column(length: 255)]
    private ?string $kbis = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isVerified = null;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: Represente::class, cascade: ['persist', 'remove'])]
    private Collection $representants;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: Emploi::class, orphanRemoval: true)]
    private Collection $emplois;

    #[ORM\ManyToOne(inversedBy: 'entreprises')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ville $ville = null;

    #[ORM\ManyToOne(inversedBy: 'entreprises')]
    private ?Taille $tailles = null;

    #[ORM\ManyToOne(inversedBy: 'entrepriseCreator')]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motifRefus = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Description = null;

    public function __construct()
    {
        $this->representants = new ArrayCollection();
        $this->emplois = new ArrayCollection();
    }

    public function __toString(): string
    {
        if($this->raisonSocial) {
            return $this->raisonSocial;
        }

        return '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function getRaisonSocial(): ?string
    {
        return $this->raisonSocial;
    }

    public function setRaisonSocial(string $raisonSocial): static
    {
        $this->raisonSocial = $raisonSocial;

        return $this;
    }

    public function getSecteur(): ?string
    {
        return $this->secteur;
    }

    public function setSecteur(string $secteur): static
    {
        $this->secteur = $secteur;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): static
    {
        $this->website = $website;

        return $this;
    }

    public function getBanniere(): ?string
    {
        return $this->banniere;
    }

    public function setBanniere(?string $banniere): static
    {
        $this->banniere = $banniere;

        return $this;
    }

    public function getKbis(): ?string
    {
        return $this->kbis;
    }

    public function setKbis(string $kbis): static
    {
        $this->kbis = $kbis;

        return $this;
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(?bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, Represente>
     */
    public function getRepresentants(): Collection
    {
        return $this->representants;
    }

    public function addRepresentant(Represente $representant): static
    {
        if (!$this->representants->contains($representant)) {
            $this->representants->add($representant);
            $representant->setEntreprise($this);
        }

        return $this;
    }

    public function removeRepresentant(Represente $representant): static
    {
        if ($this->representants->removeElement($representant)) {
            // set the owning side to null (unless already changed)
            if ($representant->getEntreprise() === $this) {
                $representant->setEntreprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Emploi>
     */
    public function getEmplois(): Collection
    {
        return $this->emplois;
    }

    public function addEmploi(Emploi $emploi): static
    {
        if (!$this->emplois->contains($emploi)) {
            $this->emplois->add($emploi);
            $emploi->setEntreprise($this);
        }

        return $this;
    }

    public function removeEmploi(Emploi $emploi): static
    {
        if ($this->emplois->removeElement($emploi)) {
            // set the owning side to null (unless already changed)
            if ($emploi->getEntreprise() === $this) {
                $emploi->setEntreprise(null);
            }
        }

        return $this;
    }

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getTailles(): ?Taille
    {
        return $this->tailles;
    }

    public function setTailles(?Taille $tailles): static
    {
        $this->tailles = $tailles;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getMotifRefus(): ?string
    {
        return $this->motifRefus;
    }

    public function setMotifRefus(?string $motifRefus): static
    {
        $this->motifRefus = $motifRefus;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

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
}
