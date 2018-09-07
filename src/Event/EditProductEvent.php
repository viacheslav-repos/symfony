<?php

namespace App\Event;

use App\Entity\Product;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class EditProductEvent
 *
 * @package App\Event
 */
class EditProductEvent extends Event implements ProductEventInterface
{
    /** @var Product */
    protected $product;

    /**
     * EditProductEvent constructor.
     *
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return 'Product with id #' . $this->getProductId() . ' has been updated';
    }

    /**
     * @return int|null
     */
    public function getProductId(): ?int
    {
        return $this->product->getId();
    }
}
