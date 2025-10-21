<?php
namespace App\Entity;

use App\Entity\Shoe;
use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $customerName = null;

    #[ORM\Column(length: 11)]
    private ?string $customerPhone = null; // changed from email, max length 11

    #[ORM\Column(length: 255)]
    private ?string $customerAddress = null;

    #[ORM\Column]
    private ?float $totalAmount = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $orderDate = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $items = [];

    #[ORM\Column(length: 255)]
    private ?string $status = 'Pending';

    #[ORM\ManyToOne(targetEntity: Shoe::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Shoe $shoe = null;

    #[ORM\Column(length: 50)]
    private ?string $paymentMethod = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $selectedColor = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $selectedSize = null;

    // ─── Getters & Setters ──────────────────────────────────────────────

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomerName(): ?string
    {
        return $this->customerName;
    }

    public function setCustomerName(string $customerName): static
    {
        $this->customerName = $customerName;
        return $this;
    }

    public function getCustomerPhone(): ?string
    {
        return $this->customerPhone;
    }

    public function setCustomerPhone(string $customerPhone): static
    {
        // optional validation: must be 11 digits and start with "09"
        if (!preg_match('/^09\d{9}$/', $customerPhone)) {
            throw new \InvalidArgumentException('Phone number must be 11 digits and start with "09".');
        }
        $this->customerPhone = $customerPhone;
        return $this;
    }

    public function getCustomerAddress(): ?string
    {
        return $this->customerAddress;
    }

    public function setCustomerAddress(string $customerAddress): static
    {
        $this->customerAddress = $customerAddress;
        return $this;
    }

    public function getTotalAmount(): ?float
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(float $totalAmount): static
    {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    public function getOrderDate(): ?\DateTimeImmutable
    {
        return $this->orderDate;
    }

    public function setOrderDate(\DateTimeImmutable $orderDate): static
    {
        $this->orderDate = $orderDate;
        return $this;
    }

    public function getItems(): ?array
    {
        return $this->items;
    }

    public function setItems(?array $items): static
    {
        $this->items = $items;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getShoe(): ?Shoe
    {
        return $this->shoe;
    }

    public function setShoe(?Shoe $shoe): static
    {
        $this->shoe = $shoe;
        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(string $paymentMethod): static
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    public function getSelectedColor(): ?string
    {
        return $this->selectedColor;
    }

    public function setSelectedColor(?string $selectedColor): static
    {
        $this->selectedColor = $selectedColor;
        return $this;
    }

    public function getSelectedSize(): ?string
    {
        return $this->selectedSize;
    }

    public function setSelectedSize(?string $selectedSize): static
    {
        $this->selectedSize = $selectedSize;
        return $this;
    }
}
