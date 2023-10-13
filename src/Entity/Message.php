<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $textMessage = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateMessage = null;

    #[ORM\OneToMany(mappedBy: 'messagesEnvoyes', targetEntity: User::class)]
    private Collection $Expediteur;

    #[ORM\OneToMany(mappedBy: 'messagesRecus', targetEntity: User::class)]
    private Collection $destinataire;

    public function __construct()
    {
        $this->Expediteur = new ArrayCollection();
        $this->destinataire = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTextMessage(): ?string
    {
        return $this->textMessage;
    }

    public function setTextMessage(string $textMessage): static
    {
        $this->textMessage = $textMessage;

        return $this;
    }

    public function getDateMessage(): ?\DateTimeInterface
    {
        return $this->dateMessage;
    }

    public function setDateMessage(\DateTimeInterface $dateMessage): static
    {
        $this->dateMessage = $dateMessage;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getExpediteur(): Collection
    {
        return $this->Expediteur;
    }

    public function addExpediteur(User $expediteur): static
    {
        if (!$this->Expediteur->contains($expediteur)) {
            $this->Expediteur->add($expediteur);
            $expediteur->setMessagesEnvoyes($this);
        }

        return $this;
    }

    public function removeExpediteur(User $expediteur): static
    {
        if ($this->Expediteur->removeElement($expediteur)) {
            // set the owning side to null (unless already changed)
            if ($expediteur->getMessagesEnvoyes() === $this) {
                $expediteur->setMessagesEnvoyes(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getDestinataire(): Collection
    {
        return $this->destinataire;
    }

    public function addDestinataire(User $destinataire): static
    {
        if (!$this->destinataire->contains($destinataire)) {
            $this->destinataire->add($destinataire);
            $destinataire->setMessagesRecus($this);
        }

        return $this;
    }

    public function removeDestinataire(User $destinataire): static
    {
        if ($this->destinataire->removeElement($destinataire)) {
            // set the owning side to null (unless already changed)
            if ($destinataire->getMessagesRecus() === $this) {
                $destinataire->setMessagesRecus(null);
            }
        }

        return $this;
    }
}
