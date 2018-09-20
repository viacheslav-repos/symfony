<?php

namespace App\Controller;

use App\Entity\Product;
use App\Event\AddProductEvent;
use App\Event\DeleteProductEvent;
use App\Event\EditProductEvent;
use App\Form\ProductType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Cache\Simple\FilesystemCache;

/**
 * Class ProductsController
 * @package App\Controller
 *
 * php bin/console make:controller ProductsController
 *
 * @property EntityManager $entityManager
 */
class ProductsController extends Controller
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @Route("/products/show/{productId}", name="show_product_by_id")
     */
    public function show($productId)
    {
        if (!$product = $this->entityManager->getRepository(Product::class)->find($productId)) {
            throw $this->createNotFoundException('No product found for id ' . $productId);
        }

        $cache = new FilesystemCache();

        if (!$cache->has('product.' . $productId)) {
            $cache->set('product.' . $productId, $this->container->get('twig')->render('product/show.html.twig', array(
                'product' => $product,
            )));
        }

        return new Response($cache->get('product.' . $productId));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route("/products/add", name="add_product")
     */
    public function add(Request $request)
    {
        $form = $this->createForm(ProductType::class, new Product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productData = $form->getData();

            $this->save($form->getData());
            $this->get('event_dispatcher')->dispatch('product.add', new AddProductEvent($productData));

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
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route("/products/edit/{productId}", name="edit_product")
     */
    public function edit($productId, Request $request)
    {
        if (!$product = $this->entityManager->getRepository(Product::class)->find($productId)) {
            throw $this->createNotFoundException('No product found for id ' . $productId);
        }

        $product->setBrochure(null);

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->save($form->getData());
            $this->get('event_dispatcher')->dispatch('product.edit', new EditProductEvent($product));

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
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route("/products/delete/{productId}", name="delete_product")
     */
    public function delete($productId)
    {
        if (!$product = $this->entityManager->getRepository(Product::class)->find($productId)) {
            throw $this->createNotFoundException('No product found for id ' . $productId);
        }

        (new FileUploader($this->getParameter('brochures_directory')))->delete($product->getBrochure());

        $this->entityManager->remove($product);
        $this->entityManager->flush();
        $this->get('event_dispatcher')->dispatch('product.delete', new DeleteProductEvent($productId));
        $this->addFlash('info', "Product with id #{$productId} has been deleted");

        return $this->redirectToRoute('show_products_list');
    }

    /**
     * @Route("/products/clearcache", name="clear_cache")
     */
    public function clearCache()
    {
        (new FilesystemCache)->clear();

        return $this->redirectToRoute('show_products_list');
    }

    /**
     * @param Product $productData
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function save(Product $productData)
    {
        $fileName = (new FileUploader($this->getParameter('brochures_directory')))->upload($productData->getBrochure());

        $productData->setBrochure($fileName);

        $this->entityManager->persist($productData);
        $this->entityManager->flush();
    }
}
