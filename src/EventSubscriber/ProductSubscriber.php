<?php

namespace App\EventSubscriber;

use App\Entity\Product;
use App\Event\AddProductEvent;
use App\Event\DeleteProductEvent;
use App\Event\ProductEventInterface;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ProductSubscriber
 *
 * @package App\EventSubscriber
 *
 * @property LoggerInterface   $logger
 * @property \Twig_Environment $twig
 * @property \Swift_Mailer     $mailer
 * @property FilesystemCache   $cache
 * @property EntityManager     $entityManager
 */
class ProductSubscriber implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    private $twig;
    private $mailer;
    private $cache;
    private $entityManager;

    /**
     * ProductSubscriber constructor.
     *
     * @param LoggerInterface   $logger
     * @param \Twig_Environment $twig
     * @param \Swift_Mailer     $mailer
     * @param EntityManager     $entityManager
     */
    public function __construct(LoggerInterface $logger, \Twig_Environment $twig, \Swift_Mailer $mailer, EntityManager $entityManager)
    {
        $this->setLogger($logger);

        $this->twig          = $twig;
        $this->mailer        = $mailer;
        $this->cache         = new FilesystemCache();
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            "product.edit" => [
                ["updateCache", 0],
                ["writeLog", 1],
            ],
            "product.add"  => [
                ["updateCache", 2],
                ["sendNewProductEmail", 0],
                ["writeLog", 1],
            ],
            "product.delete"  => [
                ["clearCache", 0],
                ["writeLog", 1],
            ]
        ];
    }

    /**
     * @param ProductEventInterface $event
     */
    public function writeLog(ProductEventInterface $event): void
    {
        $this->logger->info($event->getMessage());
    }

    /**
     * Send email about creating a new product
     *
     * @param AddProductEvent $event
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendNewProductEmail(AddProductEvent $event)
    {
        $message = (new \Swift_Message('New product created'))
            ->setFrom('symfony.local@example.com')
            ->setTo('local.mail@gmail.com')
            ->setBody($this->twig->render('emails/create_product.html.twig', ['productId' => $event->getProductId()]), 'text/html');

        #$this->mailer->send($message);
    }

    /**
     * @param ProductEventInterface $event
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function updateCache(ProductEventInterface $event)
    {
        $this->cache->set('product.' . $event->getProductId(), $this->twig->render('product/show.html.twig', array(
            'product' => $this->entityManager->getRepository(Product::class)->find($event->getProductId()),
        )));
    }

    /**
     * @param DeleteProductEvent $event
     */
    public function clearCache(DeleteProductEvent $event)
    {
        $this->cache->delete('product.' . $event->getProductId());
    }
}
