<?php

namespace App\Event;

/**
 * Interface
 *
 * @package App\Event
 */
interface ProductEventInterface
{
    /**
     * @return string
     */
    public function getMessage(): string;

    /**
     * @return int|null
     */
    public function getProductId(): ?int;
}
