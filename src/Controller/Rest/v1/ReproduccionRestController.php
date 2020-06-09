<?php


namespace App\Controller\Rest\v1;


use App\BLL\ReproduccionBLL;
use App\Controller\Rest\BaseApiController;
use App\Entity\Anuncio;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/api/v1")
 */

class ReproduccionRestController extends BaseApiController
{

    /**
     * @Route(
     *     "/reproducciones/guardar.{_format}",
     *     name="apiv1_post_reproducciones_guardar",
     *     defaults={"_format": "json"},
     *     methods={"POST"}
     * )
     * @param Request $request
     * @param ReproduccionBLL $reproduccionBLL
     * @return Response
     */
    public function save(Request $request,ReproduccionBLL $reproduccionBLL): Response
    {
        $data = $this->getContent($request);

        $result = $reproduccionBLL->save($data);

        return $this->getResponse(['ok' => $result], Response::HTTP_OK);
    }

    /**
     * @Route(
     *     "/reproducciones/anuncio/{id}.{_format}",
     *     name="apiv1_get_reproducciones_anuncio",
     *     defaults={"_format": "json"},
     *     requirements={
     *          "id": "\d+",
     *          "_format": "json"
     *     },
     *     methods={"GET"}
     *     )
     * @param Anuncio $anuncio
     * @param ReproduccionBLL $reproduccionBLL
     * @return JsonResponse
     */
    public function getReproduccionesAnuncio(Anuncio $anuncio,ReproduccionBLL $reproduccionBLL): Response
    {
        $result = $reproduccionBLL->getNumeroReproducciones($anuncio->getId());

        if(!empty($result))
        {
            return $this->getResponse(['numero de reproducciones' => $result], Response::HTTP_OK);
        }
        else
        {
            return $this->getResponse(['No hay reproducciones del anuncio'], Response::HTTP_OK);
        }
    }
}