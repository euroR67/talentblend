<?php

namespace App\Entity;

use App\Repository\NiveauRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NiveauRepository::class)]
class Niveau
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    private ?string $anneeExperience = null;

    #[ORM\OneToMany(mappedBy: 'niveau', targetEntity: User::class)]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'niveau', targetEntity: Emploi::class, orphanRemoval: true)]
    private Collection $emplois;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->emplois = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnneeExperience(): ?string
    {
        return $this->anneeExperience;
    }

    public function setAnneeExperience(string $anneeExperience): static
    {
        $this->anneeExperience = $anneeExperience;

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
            $user->setNiveau($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getNiveau() === $this) {
                $user->setNiveau(null);
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
            $emploi->setNiveau($this);
        }

        return $this;
    }

    public function removeEmploi(Emploi $emploi): static
    {
        if ($this->emplois->removeElement($emploi)) {
            // set the owning side to null (unless already changed)
            if ($emploi->getNiveau() === $this) {
                $emploi->setNiveau(null);
            }
        }

        return $this;
    }
}
