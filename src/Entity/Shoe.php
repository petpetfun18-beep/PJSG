<?php
// src/Entity/Shoe.php
namespace App\Entity;

use App\Repository\ShoeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShoeRepository::class)]
class Shoe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $brand = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $colors = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $sizes = null;

    #[ORM\Column(type: 'float')]
    private ?float $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    // ✅ Change category to string instead of relation
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $category = null;

    public function __construct()
    {
        $this->colors = [];
        $this->sizes = [];
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getBrand(): ?string { return $this->brand; }
    public function setBrand(string $brand): self { $this->brand = $brand; return $this; }

    public function getColors(): array { return $this->colors ?? []; }
    public function setColors(array $colors): self { $this->colors = $colors; return $this; }

    public function getSizes(): array { return $this->sizes ?? []; }
    public function setSizes(array $sizes): self { $this->sizes = $sizes; return $this; }

    public function getPrice(): ?float { return $this->price; }
    public function setPrice(float $price): self { $this->price = $price; return $this; }

    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $image): self { $this->image = $image; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    // ✅ Category as plain string
    public function getCategory(): ?string { return $this->category; }
    public function setCategory(?string $category): self { $this->category = $category; return $this; }
}

