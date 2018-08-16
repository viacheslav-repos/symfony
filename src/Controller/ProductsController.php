<?php

namespace App\Controller;

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

/**
 * Class ProductsController
 * @package App\Controller
 *
 * php bin/console make:controller ProductController
 *
 * @property EntityManager $productManager
 */
class ProductsController extends Controller
{
    private $productManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->productManager = $entityManager;
    }

    /**
     * @return Response
     *
     * @Route("/products", name="show_products_list")
     */
    public function list()
    {
        return $this->render('product/list.html.twig', [
            'products' => $this->productManager->getRepository(Product::class)->findAll(),
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
        if (!$product = $this->productManager->getRepository(Product::class)->find($productId)) {
            throw $this->createNotFoundException('No product found for id ' . $productId);
        }

        // todo: show separate product details

        return $this->render('product/edit.html.twig', array(
            'form' => $this->createForm(ProductType::class, $product)->createView(),
        ));
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
            $this->productManager->persist($form->getData());
            $this->productManager->flush();

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
        if (!$product = $this->productManager->getRepository(Product::class)->find($productId)) {
            throw $this->createNotFoundException('No product found for id ' . $productId);
        }

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->productManager->persist($form->getData());
            $this->productManager->flush();

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
        if (!$product = $this->productManager->getRepository(Product::class)->find($productId)) {
            throw $this->createNotFoundException('No product found for id ' . $productId);
        }

        // todo: are you sure ?

        $this->productManager->remove($product);
        $this->productManager->flush();

        return $this->list();
    }
}
