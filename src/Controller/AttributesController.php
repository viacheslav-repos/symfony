<?php

namespace App\Controller;

use App\Entity\Attribute;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class AttributesController extends Controller
{
    /**
     * @Route("/attributes", name="attributes_list")
     */
    public function list()
    {
        return $this->render('attribute/list.html.twig', [
            'attributes' => $this->getDoctrine()->getManager()->getRepository(Attribute::class)->findAll(),
        ]);
    }
}
