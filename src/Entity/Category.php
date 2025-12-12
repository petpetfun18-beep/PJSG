<?php
namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Shoe::class)]
    private Collection $shoes;

    /**
     * @var Collection<int, shoe>
     */
    #[ORM\ManyToMany(targetEntity: shoe::class, inversedBy: 'categories')]
    private Collection $shoe;

    public function __construct()
    {
        $this->shoes = new ArrayCollection();
        $this->shoe = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getShoes(): Collection
    {
        return $this->shoes;
    }

    // 👇 Add this so Category can be displayed as text
    public function __toString(): string
    {
        return $this->name ?? '';
    }

    /**
     * @return Collection<int, shoe>
     */
    public function getShoe(): Collection
    {
        return $this->shoe;
    }

    public function addShoe(shoe $shoe): static
    {
        if (!$this->shoe->contains($shoe)) {
            $this->shoe->add($shoe);
        }

        return $this;
    }

    public function removeShoe(shoe $shoe): static
    {
        $this->shoe->removeElement($shoe);

        return $this;
    }
}
