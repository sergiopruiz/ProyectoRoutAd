<?php

namespace App\Controller;

use App\Entity\Servicio;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/servicio")
 */
class ServicioController extends AbstractController
{

    /**
     * @Route("/{id}", name="servicio_show", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function show(Request $request): Response
    {
        $servicios = $this->getDoctrine()->getRepository(Servicio::class)->findBy(['tabletId' => $request->get('id')]);

        return $this->render('servicio/show.html.twig', [
            'servicios' => $servicios,
        ]);
    }
    /**
     * @Route("/datatables_servicio",
     *     name="servicio_datatables",
     *     methods={"POST"},
     *     options={"expose"=true}
     *     )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function dataTableServicio(Request $request): JsonResponse
    {
        $response = new JsonResponse();
        $data = $request->request->all();
        if (is_null($data))
            throw new BadRequestHttpException("No se reciben correctamente los datos de Datatables");

        $resultado = $this->getDoctrine()->getRepository(Servicio::class)->findListado($data,$data[10]);

        for ($i = 0; $i < count($resultado['data']); $i++){
            $resultado['data'][$i]['fecha'] = $resultado['data'][$i]['fecha']->format('d-m-Y H:i');
        }

        $response->setContent(json_encode($resultado));
        return $response;
    }

    /**
     * @Route("/{id}", name="servicio_delete", methods={"DELETE"})
     * @param Request $request
     * @param Servicio $servicio
     * @return Response
     */
    public function delete(Request $request, Servicio $servicio): Response
    {
        if ($this->isCsrfTokenValid('delete'.$servicio->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($servicio);
            $entityManager->flush();
        }

        return $this->redirectToRoute('servicio_index');
    }
}
