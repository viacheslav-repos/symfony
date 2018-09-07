<?php

namespace App\Event;

use App\Entity\Product;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class AddProductEvent
 *
 * @package App\Event
 */
class AddProductEvent extends Event implements ProductEventInterface
{
    /** @var Product */
    protected $product;

    /**
     * AddProductEvent constructor.
     *
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * @return string
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function getMessage(): string
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer           = new ObjectNormalizer($classMetadataFactory);
        $serializer           = new Serializer([$normalizer], [new JsonEncoder()]);

        return 'Product with id #' . $this->getProductId() . ' has been created. Data: ' . $serializer->serialize($this->product, 'json', array('groups' => array('product')));
    }

    /**
     * @return int|null
     */
    public function getProductId(): ?int
    {
        return $this->product->getId();
    }
}
