<?php

namespace App\Event;

use App\Entity\Product;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class DeleteProductEvent
 *
 * @package App\Event
 */
class DeleteProductEvent extends Event implements ProductEventInterface
{
    /** @var Product */
    protected $productId;

    /**
     * DeleteProductEvent constructor.
     *
     * @param int|null $productId
     */
    public function __construct(?int $productId)
    {
        $this->productId = $productId;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return 'Product with id #' . $this->getProductId() . ' has been deleted';
    }

    /**
     * @return int|null
     */
    public function getProductId(): ?int
    {
        return $this->productId;
    }
}
