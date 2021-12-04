<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: 'integer')]
    private int $price;

    #[ORM\Column(type: 'integer')]
    private int $tva;

    #[ORM\Column(type: 'string', length: 255)]
    private string $color;

    #[ORM\Column(type: 'string', length: 255)]
    private string $brand;

    #[ORM\Column(type: 'string', length: 255)]
    private string $os;

    #[ORM\Column(type: 'integer')]
    private int $memory;

    #[ORM\Column(type: 'integer')]
    private int $stock;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): void
    {
        $this->price = $price;
    }

    public function getTva():int
    {
        return $this->tva;
    }
 
    public function setTva(int $tva): void
    {
        $this->tva = $tva;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): void
    {
        $this->brand = $brand;
    }

    public function getOs(): string
    {
        return $this->os;
    }

    public function setOs(string $os): void
    {
        $this->os = $os;
    }

    public function getMemory(): int 
    {
        return $this->memory;
    }

    public function setMemory(int $memory): void
    {
        $this->memory = $memory;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): void
    {
        $this->stock = $stock;
    }
}