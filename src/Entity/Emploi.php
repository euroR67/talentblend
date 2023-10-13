<?php

namespace App\Entity;

use App\Repository\EmploiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmploiRepository::class)]
class Emploi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    private ?string $poste = null;

    #[ORM\Column(length: 40)]
    private ?string $disponibilite = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $salaire = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateOffre = null;

    #[ORM\Column(length: 40)]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'emplois')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categories = null;

    #[ORM\ManyToOne(inversedBy: 'emplois')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Contrat $contrats = null;

    #[ORM\OneToMany(mappedBy: 'emploi', targetEntity: Postule::class)]
    private Collection $postulations;

    public function __construct()
    {
        $this->postulations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPoste(): ?string
    {
        return $this->poste;
    }

    public function setPoste(string $poste): static
    {
        $this->poste = $poste;

        return $this;
    }

    public function getDisponibilite(): ?string
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(string $disponibilite): static
    {
        $this->disponibilite = $disponibilite;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getSalaire(): ?int
    {
        return $this->salaire;
    }

    public function setSalaire(int $salaire): static
    {
        $this->salaire = $salaire;

        return $this;
    }

    public function getDateOffre(): ?\DateTimeInterface
    {
        return $this->dateOffre;
    }

    public function setDateOffre(\DateTimeInterface $dateOffre): static
    {
        $this->dateOffre = $dateOffre;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCategories(): ?Categorie
    {
        return $this->categories;
    }

    public function setCategories(?Categorie $categories): static
    {
        $this->categories = $categories;

        return $this;
    }

    public function getContrats(): ?Contrat
    {
        return $this->contrats;
    }

    public function setContrats(?Contrat $contrats): static
    {
        $this->contrats = $contrats;

        return $this;
    }

    /**
     * @return Collection<int, Postule>
     */
    public function getPostulations(): Collection
    {
        return $this->postulations;
    }

    public function addPostulation(Postule $postulation): static
    {
        if (!$this->postulations->contains($postulation)) {
            $this->postulations->add($postulation);
            $postulation->setEmploi($this);
        }

        return $this;
    }

    public function removePostulation(Postule $postulation): static
    {
        if ($this->postulations->removeElement($postulation)) {
            // set the owning side to null (unless already changed)
            if ($postulation->getEmploi() === $this) {
                $postulation->setEmploi(null);
            }
        }

        return $this;
    }
}
