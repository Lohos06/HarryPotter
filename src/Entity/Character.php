<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CharacterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CharacterRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['character:lire']],
    denormalizationContext: ['groups' => ['character:ecrire']],
)]
class Character
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['character:lire'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['character:lire'])]
    private ?string $sourceId = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['character:lire', 'character:ecrire'])]
    #[Assert\Length(max: 50)]
    private ?string $bloodStatus = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['character:lire', 'character:ecrire'])]
    #[Assert\Length(max: 255)]
    private ?string $born = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['character:lire', 'character:ecrire'])]
    #[Assert\Length(max: 255)]
    private ?string $died = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['character:lire', 'character:ecrire'])]
    #[Assert\Length(max: 20)]
    private ?string $gender = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['character:lire', 'character:ecrire'])]
    #[Assert\Length(max: 50)]
    private ?string $height = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['character:lire', 'character:ecrire'])]
    #[Assert\Url]
    #[Assert\Length(max: 255)]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    #[Groups(['character:lire', 'character:ecrire', 'house:lire', 'species:lire', 'title:lire'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['character:lire', 'character:ecrire'])]
    #[Assert\Length(max: 100)]
    private ?string $nationality = null;

    #[ORM\ManyToOne(inversedBy: 'characters')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['character:lire', 'character:ecrire'])]
    #[Assert\NotNull]
    private ?Species $species = null;

    /**
     * @var Collection<int, Title>
     */
    #[ORM\ManyToMany(targetEntity: Title::class, inversedBy: 'characters')]
    #[Groups(['character:lire', 'character:ecrire'])]
    private Collection $titles;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['character:lire', 'character:ecrire'])]
    #[Assert\Length(max: 50)]
    private ?string $weight = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['character:lire', 'character:ecrire'])]
    #[Assert\Url]
    #[Assert\Length(max: 255)]
    private ?string $wikiLink = null;

    #[ORM\ManyToOne(inversedBy: 'characters')]
    #[Groups(['character:lire', 'character:ecrire'])]
    private ?House $house = null;

    public function __construct()
    {
        $this->titles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSourceId(): ?string
    {
        return $this->sourceId;
    }

    public function setSourceId(?string $sourceId): static
    {
        $this->sourceId = $sourceId;

        return $this;
    }

    public function getBloodStatus(): ?string
    {
        return $this->bloodStatus;
    }

    public function setBloodStatus(?string $bloodStatus): static
    {
        $this->bloodStatus = $bloodStatus;

        return $this;
    }

    public function getBorn(): ?string
    {
        return $this->born;
    }

    public function setBorn(?string $born): static
    {
        $this->born = $born;

        return $this;
    }

    public function getDied(): ?string
    {
        return $this->died;
    }

    public function setDied(?string $died): static
    {
        $this->died = $died;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getHeight(): ?string
    {
        return $this->height;
    }

    public function setHeight(?string $height): static
    {
        $this->height = $height;

        return $this;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    public function setNationality(?string $nationality): static
    {
        $this->nationality = $nationality;

        return $this;
    }

    public function getSpecies(): ?Species
    {
        return $this->species;
    }

    public function setSpecies(?Species $species): static
    {
        $this->species = $species;

        return $this;
    }

    /**
     * @return Collection<int, Title>
     */
    public function getTitles(): Collection
    {
        return $this->titles;
    }

    public function addTitle(Title $title): static
    {
        if (!$this->titles->contains($title)) {
            $this->titles->add($title);
        }

        return $this;
    }

    public function removeTitle(Title $title): static
    {
        $this->titles->removeElement($title);

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(?string $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getWikiLink(): ?string
    {
        return $this->wikiLink;
    }

    public function setWikiLink(?string $wikiLink): static
    {
        $this->wikiLink = $wikiLink;

        return $this;
    }

    public function getHouse(): ?House
    {
        return $this->house;
    }

    public function setHouse(?House $house): static
    {
        $this->house = $house;

        return $this;
    }
}