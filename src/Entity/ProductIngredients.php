<?php

namespace App\Entity;

use App\Repository\ProductIngredientsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductIngredientsRepository::class)]
class ProductIngredients
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $productId = null;

    #[ORM\Column]
    private ?int $ingredientId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): static
    {
        $this->productId = $productId;

        return $this;
    }

    public function getIngredientId(): ?int
    {
        return $this->ingredientId;
    }

    public function setIngredientId(int $ingredientId): static
    {
        $this->ingredientId = $ingredientId;

        return $this;
    }
}
