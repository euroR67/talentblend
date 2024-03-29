<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Valid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'Un compte existe déjà avec cette adresse email.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 30)]
    private ?string $nom = null;

    #[ORM\Column(length: 30)]
    private ?string $prenom = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $pays = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cv = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Metier $metier;

    #[ORM\OneToMany(mappedBy: 'userExp', targetEntity: Experience::class, orphanRemoval: true, cascade: ['persist'])]
    #[Valid]
    private Collection $experiences;

    #[ORM\OneToMany(mappedBy: 'userFormation', targetEntity: Formation::class, orphanRemoval: true, cascade: ['persist'])]
    #[Valid]
    private Collection $formations;

    #[ORM\ManyToMany(targetEntity: Langue::class)]
    private Collection $langues;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Niveau $niveau = null;

    #[ORM\ManyToOne]
    private ?Ville $ville = null;

    #[ORM\OneToMany(mappedBy: 'userPostulant', targetEntity: Postule::class)]
    private Collection $postulations;

    #[ORM\OneToMany(mappedBy: 'userEntreprise', targetEntity: Represente::class)]
    private Collection $entrepriseRepresenter;

    #[ORM\ManyToMany(targetEntity: Emploi::class, inversedBy: 'users')]
    private Collection $emploiSauvegarder;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Emploi::class, orphanRemoval: true)]
    private Collection $emplois;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Entreprise::class)]
    private Collection $entrepriseCreator;

    #[ORM\ManyToMany(targetEntity: TypeEmploi::class, inversedBy: 'users')]
    private Collection $typesEmploi;

    #[ORM\ManyToMany(targetEntity: Contrat::class, inversedBy: 'users')]
    private Collection $contrats;

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Message::class)]
    private Collection $sentMessages;

    #[ORM\OneToMany(mappedBy: 'receiver', targetEntity: Message::class)]
    private Collection $receivedMessages;

    public function __construct()
    {
        $this->experiences = new ArrayCollection();
        $this->formations = new ArrayCollection();
        $this->langues = new ArrayCollection();
        $this->postulations = new ArrayCollection();
        $this->entrepriseRepresenter = new ArrayCollection();
        $this->emploiSauvegarder = new ArrayCollection();
        $this->emplois = new ArrayCollection();
        $this->entrepriseCreator = new ArrayCollection();
        $this->typesEmploi = new ArrayCollection();
        $this->contrats = new ArrayCollection();
        $this->sentMessages = new ArrayCollection();
        $this->receivedMessages = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getNom() . ' ' . $this->getPrenom();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function countUnreadMessagesFromUser(User $sender): int
    {
        $count = 0;
        foreach ($this->getReceivedMessages() as $message) {
            if (!$message->isIsRead() && $message->getSender() === $sender) {
                $count++;
            }
        }

        return $count;
    }

    // Fonction countAllUnreadMessages() qui compte tous les messages non lus
    public function countAllUnreadMessages(): int
    {
        $count = 0;
        foreach ($this->getReceivedMessages() as $message) {
            if (!$message->isIsRead()) {
                $count++;
            }
        }

        return $count;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
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

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(?string $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

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

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, Experience>
     */
    public function getExperiences(): Collection
    {
        return $this->experiences;
    }

    public function addExperience(Experience $experience): static
    {
        if (!$this->experiences->contains($experience)) {
            $this->experiences->add($experience);
            $experience->setUserExp($this);
        }

        return $this;
    }

    public function removeExperience(Experience $experience): static
    {
        if ($this->experiences->removeElement($experience)) {
            // set the owning side to null (unless already changed)
            if ($experience->getUserExp() === $this) {
                $experience->setUserExp(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Formation>
     */
    public function getFormations(): Collection
    {
        return $this->formations;
    }

    public function addFormation(Formation $formation): static
    {
        if (!$this->formations->contains($formation)) {
            $this->formations->add($formation);
            $formation->setUserFormation($this);
        }

        return $this;
    }

    public function removeFormation(Formation $formation): static
    {
        if ($this->formations->removeElement($formation)) {
            // set the owning side to null (unless already changed)
            if ($formation->getUserFormation() === $this) {
                $formation->setUserFormation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Langue>
     */
    public function getLangues(): Collection
    {
        return $this->langues;
    }

    public function addLangue(Langue $langue): static
    {
        if (!$this->langues->contains($langue)) {
            $this->langues->add($langue);
        }

        return $this;
    }

    public function removeLangue(Langue $langue): static
    {
        $this->langues->removeElement($langue);

        return $this;
    }

    public function getMetier(): ?Metier
    {
        return $this->metier;
    }

    public function setMetier(?Metier $metier): static
    {
        $this->metier = $metier;

        return $this;
    }

    public function getNiveau(): ?Niveau
    {
        return $this->niveau;
    }

    public function setNiveau(?Niveau $niveau): static
    {
        $this->niveau = $niveau;

        return $this;
    }

    public function getville(): ?Ville
    {
        return $this->ville;
    }

    public function setville(?Ville $ville): static
    {
        $this->ville = $ville;

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
            $postulation->setUserPostulant($this);
        }

        return $this;
    }

    public function removePostulation(Postule $postulation): static
    {
        if ($this->postulations->removeElement($postulation)) {
            // set the owning side to null (unless already changed)
            if ($postulation->getUserPostulant() === $this) {
                $postulation->setUserPostulant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Represente>
     */
    public function getEntrepriseRepresenter(): Collection
    {
        return $this->entrepriseRepresenter;
    }

    public function addEntrepriseRepresenter(Represente $representant): static
    {
        if (!$this->entrepriseRepresenter->contains($representant)) {
            $this->entrepriseRepresenter->add($representant);
            $representant->setUserEntreprise($this);
        }

        return $this;
    }

    public function removeEntrepriseRepresenter(Represente $representant): static
    {
        if ($this->entrepriseRepresenter->removeElement($representant)) {
            // set the owning side to null (unless already changed)
            if ($representant->getUserEntreprise() === $this) {
                $representant->setUserEntreprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Emploi>
     */
    public function getEmploiSauvegarder(): Collection
    {
        return $this->emploiSauvegarder;
    }

    public function addEmploiSauvegarder(Emploi $emploiSauvegarder): static
    {
        if (!$this->emploiSauvegarder->contains($emploiSauvegarder)) {
            $this->emploiSauvegarder->add($emploiSauvegarder);
        }

        return $this;
    }

    public function removeEmploiSauvegarder(Emploi $emploiSauvegarder): static
    {
        $this->emploiSauvegarder->removeElement($emploiSauvegarder);

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
            $emploi->setUser($this);
        }

        return $this;
    }

    public function removeEmploi(Emploi $emploi): static
    {
        if ($this->emplois->removeElement($emploi)) {
            // set the owning side to null (unless already changed)
            if ($emploi->getUser() === $this) {
                $emploi->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Entreprise>
     */
    public function getEntrepriseCreator(): Collection
    {
        return $this->entrepriseCreator;
    }

    public function addEntrepriseCreator(Entreprise $entrepriseCreator): static
    {
        if (!$this->entrepriseCreator->contains($entrepriseCreator)) {
            $this->entrepriseCreator->add($entrepriseCreator);
            $entrepriseCreator->setUser($this);
        }

        return $this;
    }

    public function removeEntrepriseCreator(Entreprise $entrepriseCreator): static
    {
        if ($this->entrepriseCreator->removeElement($entrepriseCreator)) {
            // set the owning side to null (unless already changed)
            if ($entrepriseCreator->getUser() === $this) {
                $entrepriseCreator->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TypeEmploi>
     */
    public function getTypesEmploi(): Collection
    {
        return $this->typesEmploi;
    }

    public function addTypesEmploi(TypeEmploi $typesEmploi): static
    {
        if (!$this->typesEmploi->contains($typesEmploi)) {
            $this->typesEmploi->add($typesEmploi);
        }

        return $this;
    }

    public function removeTypesEmploi(TypeEmploi $typesEmploi): static
    {
        $this->typesEmploi->removeElement($typesEmploi);

        return $this;
    }

    /**
     * @return Collection<int, Contrat>
     */
    public function getcontrats(): Collection
    {
        return $this->contrats;
    }

    public function addContrat(Contrat $contrat): static
    {
        if (!$this->contrats->contains($contrat)) {
            $this->contrats->add($contrat);
        }

        return $this;
    }

    public function removeContrat(Contrat $contrat): static
    {
        $this->contrats->removeElement($contrat);

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getSentMessages(): Collection
    {
        return $this->sentMessages;
    }

    public function addSentMessage(Message $sentMessage): static
    {
        if (!$this->sentMessages->contains($sentMessage)) {
            $this->sentMessages->add($sentMessage);
            $sentMessage->setSender($this);
        }

        return $this;
    }

    public function removeSentMessage(Message $sentMessage): static
    {
        if ($this->sentMessages->removeElement($sentMessage)) {
            // set the owning side to null (unless already changed)
            if ($sentMessage->getSender() === $this) {
                $sentMessage->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getReceivedMessages(): Collection
    {
        return $this->receivedMessages;
    }

    public function addReceivedMessage(Message $receivedMessage): static
    {
        if (!$this->receivedMessages->contains($receivedMessage)) {
            $this->receivedMessages->add($receivedMessage);
            $receivedMessage->setReceiver($this);
        }

        return $this;
    }

    public function removeReceivedMessage(Message $receivedMessage): static
    {
        if ($this->receivedMessages->removeElement($receivedMessage)) {
            // set the owning side to null (unless already changed)
            if ($receivedMessage->getReceiver() === $this) {
                $receivedMessage->setReceiver(null);
            }
        }

        return $this;
    }
}
