<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProductsController
 * @package App\Controller
 *
 * php bin/console make:controller ProductController
 */
class ProductsController extends Controller
{
    /**
     * @Route("/products", name="products_list")
     */
    public function list()
    {
        return $this->render('product/list.html.twig', [
            'products' => $this->getDoctrine()->getRepository(Product::class)->findAll(),
        ]);
    }

    /**
     * @Route("/products/add", name="add_product")
     */
    public function add()
    {
        // ToDo: get data from post; create template for one product

        $product = new Product();
        $product->setTitle('new product');
        $product->setDescription('new amazing product');
        $product->setPrice(777);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($product);
        $entityManager->flush();

        return new Response('Saved new product with id ' . $product->getId());
    }

    /**
     * @Route("/products/edit/{id}", name="edit_product")
     */
    public function edit($id)
    {
        //ToDo: implement editing

        return $this->render('product/list.html.twig', [
            'products' => [],
        ]);
    }
}
