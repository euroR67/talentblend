<?php

namespace App\Entity;

use App\Repository\RepresenteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RepresenteRepository::class)]
class Represente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $status = null;

    #[ORM\ManyToOne(inversedBy: 'representants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Entreprise $entreprise = null;

    #[ORM\ManyToOne(inversedBy: 'entrepriseRepresenter')]
    #[ORM\JoinColumn(name: 'user_entreprise_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?User $userEntreprise = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motifRefus = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $kbis = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        if($this->entreprise) {
            return $this->entreprise;
        }

        return '';
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getEntreprise(): ?Entreprise
    {
        return $this->entreprise;
    }

    public function setEntreprise(?Entreprise $entreprise): static
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getUserEntreprise(): ?User
    {
        return $this->userEntreprise;
    }

    public function setUserEntreprise(?User $userEntreprise): static
    {
        $this->userEntreprise = $userEntreprise;

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

    public function getKbis(): ?string
    {
        return $this->kbis;
    }

    public function setKbis(?string $kbis): static
    {
        $this->kbis = $kbis;

        return $this;
    }
}
