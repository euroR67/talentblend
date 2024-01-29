<?php

namespace App\Entity;

use App\Repository\PostuleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostuleRepository::class)]
class Postule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datePostulation = null;

    #[ORM\Column(nullable: true)]
    private ?bool $status = null;

    #[ORM\ManyToOne(inversedBy: 'postulations')]
    private ?User $userPostulant = null;

    #[ORM\ManyToOne(inversedBy: 'postulations')]
    private ?Emploi $emploi = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cv = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $message = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return (string)$this->getUserPostulant();
    }

    public function getDatePostulation(): ?\DateTimeInterface
    {
        return $this->datePostulation;
    }

    public function setDatePostulation(\DateTimeInterface $datePostulation): static
    {
        $this->datePostulation = $datePostulation;

        return $this;
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

    public function getUserPostulant(): ?User
    {
        return $this->userPostulant;
    }

    public function setUserPostulant(?User $userPostulant): static
    {
        $this->userPostulant = $userPostulant;

        return $this;
    }

    public function getEmploi(): ?Emploi
    {
        return $this->emploi;
    }

    public function setEmploi(?Emploi $emploi): static
    {
        $this->emploi = $emploi;

        return $this;
    }

    public function getCv(): ?string
    {
        return $this->cv;
    }

    public function setCv(?string $cv): static
    {
        $this->cv = $cv;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }
}
