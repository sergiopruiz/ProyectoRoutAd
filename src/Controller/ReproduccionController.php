<?php

namespace App\Controller;

use App\Entity\Reproduccion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reproduccion")
 */
class ReproduccionController extends AbstractController
{
    /**
     * @Route("/", name="reproduccion_index", methods={"GET"})
     */
    public function index(): Response
    {
        $reproduccions = $this->getDoctrine()
            ->getRepository(Reproduccion::class)
            ->findAll();

        return $this->render('reproduccion/index.html.twig', [
            'reproduccions' => $reproduccions,
        ]);
    }

    /**
     * @Route("/{id}", name="reproduccion_show", methods={"GET"})
     * @param Reproduccion $reproduccion
     * @return Response
     */
    public function show(Reproduccion $reproduccion): Response
    {
        return $this->render('reproduccion/show.html.twig', [
            'reproduccion' => $reproduccion,
        ]);
    }
}
