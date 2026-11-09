<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    // Liste des catégories : libellé affiché => valeur enregistrée en base
    public const CATEGORIES = [
        'Concert' => 'concert',
        'Conférence' => 'conference',
        'Atelier' => 'atelier',
        'Sport' => 'sport',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    private ?string $title = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'La date est obligatoire.')]
    private ?\DateTime $date = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Les informations sont obligatoires.')]
    private ?string $information = null;

    #[ORM\Column(length: 50)]
    private ?string $category = null;

    #[ORM\Column]
    #[Assert\Positive(message: 'Le nombre de places doit être supérieur à 0.')]
    private ?int $places = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column]
    private bool $validated = false;

    /**
     * Les utilisateurs inscrits à l'événement
     *
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'events')]
    private Collection $participants;

    #[ORM\ManyToOne]
    private ?User $author = null;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(?\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getInformation(): ?string
    {
        return $this->information;
    }

    public function setInformation(?string $information): static
    {
        $this->information = $information;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    // Renvoie le libellé de la catégorie (ex. : "conference" => "Conférence")
    public function getCategoryLabel(): string
    {
        return array_search($this->category, self::CATEGORIES);
    }

    public function getPlaces(): ?int
    {
        return $this->places;
    }

    public function setPlaces(?int $places): static
    {
        $this->places = $places;

        return $this;
    }

    // Places restantes = places totales - nombre d'inscrits
    public function getRemainingPlaces(): int
    {
        return $this->places - count($this->participants);
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function isValidated(): bool
    {
        return $this->validated;
    }

    public function setValidated(bool $validated): static
    {
        $this->validated = $validated;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
        }

        return $this;
    }

    public function removeParticipant(User $participant): static
    {
        $this->participants->removeElement($participant);

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }
}
