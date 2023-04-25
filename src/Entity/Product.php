<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private string $sku;

    #[ORM\Column(enumType: Category::class)]
    private Category $category;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Purchase::class, orphanRemoval: true)]
    #[ORM\OrderBy(['date' => 'DESC'])]
    private Collection $purchases;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Purchase::class, fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    #[ORM\OrderBy(['date' => 'DESC'])]
    private Collection $lazyPurchases;

    public function __construct(string $sku, Category $category)
    {
        $this->sku = $sku;
        $this->category = $category;
        $this->purchases = new ArrayCollection();
        $this->lazyPurchases = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @return Collection<int, Purchase>
     */
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    /**
     * @return Collection<int, Purchase>
     */
    public function getLazyPurchases(): Collection
    {
        return $this->lazyPurchases;
    }
}
