<?php

namespace App\Controller;

use App\Entity\AttributeValue;
use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Simple\FilesystemCache;

/**
 * Class ProductsController
 * @package App\Controller
 *
 * php bin/console make:controller ProductsController
 *
 * @property EntityManager   $entityManager
 * @property LoggerInterface $logger
 * @property FilesystemCache $cache
 */
class ProductsController extends Controller
{
    private $productManager;
    private $logger;
    private $cache;

    public function __construct(EntityManager $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger        = $logger;
        $this->cache         = new FilesystemCache();
    }

    /**
     * @return Response
     *
     * @Route("/products", name="show_products_list")
     */
    public function list()
    {
        return $this->render('product/list.html.twig', [
            'products' => $this->entityManager->getRepository(Product::class)->findAll(),
        ]);
    }

    /**
     * @param $productId
     *
     * @return Response
     *
     * @Route("/products/show/{productId}", name="show_product_by_id")
     */
    public function show($productId)
    {
        if ($this->cache->has('product.' . $productId)) {
            return $this->cache->get('product.' . $productId);
        }

        if (!$product = $this->entityManager->getRepository(Product::class)->find($productId)) {
            throw $this->createNotFoundException('No product found for id ' . $productId);
        }

        $renderData = $this->render('product/show.html.twig', array(
            'product' => $product,
        ));

        $this->cache->set('product.' . $productId, $renderData);

        return $renderData;
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route("/products/add", name="add_product")
     */
    public function add(Request $request)
    {
        $form = $this->createForm(ProductType::class, new Product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Product $productData */
            $productData = $form->getData();

            $this->entityManager->persist($productData);
            $this->entityManager->flush();

            $productId = $productData->getId();

            $this->logger->info("New product with id #{$productId} has been created.");

            /** send email about creating a product */
            /*$message = (new \Swift_Message('Hello Email'))
                ->setFrom('symfony.local@example.com')
                ->setTo('your.mail@gmail.com')
                ->setBody($this->renderView('emails/create_product.html.twig', ['productId' => $productData->getId()]), 'text/html');

            $this->get('mailer')->send($message);*/

            $this->cache->set('product.' . $productId, $this->render('product/show.html.twig', array(
                'product' => $this->entityManager->getRepository(Product::class)->find($productId),
            )));

            return $this->redirectToRoute('show_products_list');
        }

        return $this->render('product/edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param         $productId
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route("/products/edit/{productId}", name="edit_product")
     */
    public function edit($productId, Request $request)
    {
        if (!$product = $this->entityManager->getRepository(Product::class)->find($productId)) {
            throw $this->createNotFoundException('No product found for id ' . $productId);
        }

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($form->getData());
            $this->entityManager->flush();

            $this->logger->info("Product with id #{$productId} has been modified.");

            $this->cache->set('product.' . $productId, $this->render('product/show.html.twig', array(
                'product' => $product,
            )));

            return $this->redirectToRoute('show_products_list');
        }

        return $this->render('product/edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param $productId
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route("/products/delete/{productId}", name="delete_product")
     */
    public function delete($productId)
    {
        if (!$product = $this->entityManager->getRepository(Product::class)->find($productId)) {
            throw $this->createNotFoundException('No product found for id ' . $productId);
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        $this->logger->info("Product with id #{$productId} has been deleted.");

        $this->cache->delete('product.' . $productId);

        return $this->list();
    }

    /**
     * @Route("/products/clearcache", name="clear_cache")
     */
    public function clearCache()
    {
        $this->cache->clear();

        return $this->list();
    }
}
