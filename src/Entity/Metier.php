<?php

namespace App\Entity;

use App\Repository\MetierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MetierRepository::class)]
class Metier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    private ?string $nomMetier = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'metiers')]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function __toString(): string
    {
        if ($this->nomMetier) {
            return $this->nomMetier;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomMetier(): ?string
    {
        return $this->nomMetier;
    }

    public function setNomMetier(string $nomMetier): static
    {
        $this->nomMetier = $nomMetier;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addMetier($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeMetier($this);
        }

        return $this;
    }
}
