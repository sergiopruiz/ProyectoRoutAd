<?php


namespace App\Controller\Rest\v1;


use App\BLL\AnuncioBLL;
use App\Controller\Rest\BaseApiController;
use App\Entity\Anuncio;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1")
 */

class AnuncioRestController extends BaseApiController
{
    /**
     * @Route(
     *     "/anuncios.{_format}",
     *     name="apiv1_get_anuncios",
     *     defaults={"_format": "json"},
     *     requirements={"_format": "json"},
     *     methods={"GET"}
     * )
     * @param AnuncioBLL $anuncioBLL
     * @return JsonResponse
     */
    public function getAll(AnuncioBLL $anuncioBLL) : Response
    {
        $result = $anuncioBLL->getAll();
        return $this->getResponse($result);
    }


    /**
     * @Route(
     *     "/anuncios/{id}.{_format}",
     *     name="apiv1_get_anuncio",
     *     defaults={"_format": "json"},
     *     requirements={
     *          "id": "\d+",
     *          "_format": "json"
     *     },
     *     methods={"GET"}
     * )
     * @param Anuncio $anuncio
     * @param AnuncioBLL $anuncioBLL
     * @return JsonResponse
     */
    public function getOne(Anuncio $anuncio, AnuncioBLL $anuncioBLL) : Response
    {
        return $this->getResponse($anuncioBLL->toArray($anuncio));
    }

    /**
     * @Route(
     *     "/anuncios/{id}.{_format}",
     *     name="apiv1_delete_anuncios",
     *     defaults={"_format": "json"}),
     *     requirements={
     *          "id": "\d+",
     *          "_format": "json"
     *     },
     *     methods={"DELETE"}
     * )
     * @param Anuncio $anuncio
     * @param AnuncioBLL $anuncioBLL
     * @return JsonResponse
     */
    public function delete(Anuncio $anuncio, AnuncioBLL $anuncioBLL) : Response
    {
        $anuncioBLL->delete($anuncio);

        return $this->getResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(
     *     "/anuncios/video/{id}.{_format}",
     *     name="apiv1_getVideoURL",
     *     requirements={
     *     "id": "\d+"
     *     },
     *     defaults={"_format": "string"},
     *     methods={"GET"}
     * )
     * @param Anuncio $anuncio
     * @param AnuncioBLL $anuncioBLL
     * @return JsonResponse
     */
    public function getVideo(Anuncio $anuncio, AnuncioBLL $anuncioBLL) : Response
    {
       $video = [ 'video'  => $anuncioBLL->getVideoURL($anuncio->getId())];
       return $this->getResponse($video,Response::HTTP_OK);
    }

    /**
     * @Route(
     *     "/anuncios_check/{id}.{_format}",
     *     name="apiv1_get_anuncios_check",
     *     defaults={"_format": "json"},
     *     requirements={"_format": "json"},
     *     methods={"GET"}
     * )
     * @param AnuncioBLL $anuncioBLL
     * @param string $id
     * @return JsonResponse
     */
    public function checkAnuncios(AnuncioBLL $anuncioBLL,string $id) : Response
    {
        $actualizacion = $anuncioBLL->checkAnucios($id);

        return $this->getResponse($actualizacion, Response::HTTP_OK);
    }
}