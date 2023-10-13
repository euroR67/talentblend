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

    #[ORM\Column]
    private ?bool $status = null;

    #[ORM\ManyToOne(inversedBy: 'representants')]
    private ?Entreprise $entreprise = null;

    #[ORM\ManyToOne(inversedBy: 'representants')]
    private ?User $userEntreprise = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
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
}
