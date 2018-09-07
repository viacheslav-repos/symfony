<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AttributeValueRepository")
 */
class AttributeValue
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @Groups({"product"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"product"})
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Attribute", inversedBy="attributeValues", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"product"})
     */
    private $attribute;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Product", mappedBy="attributeValues")
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAttribute(): ?Attribute
    {
        return $this->attribute;
    }

    public function setAttribute(?Attribute $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
        }

        return $this;
    }
}
