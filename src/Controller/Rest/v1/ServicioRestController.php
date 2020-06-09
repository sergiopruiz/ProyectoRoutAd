<?php


namespace App\Controller\Rest\v1;


use App\BLL\OneSignalBLL;
use App\BLL\ServicioBLL;
use App\Controller\Rest\BaseApiController;
use App\Entity\Servicio;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/api/v1")
 */

class ServicioRestController extends BaseApiController
{
    /**
     * @Route(
     *     "/servicios.{_format}",
     *     name="apiv1_get_servicios",
     *     defaults={"_format": "json"},
     *     requirements={"_format": "json"},
     *     methods={"GET"}
     * )
     *
     * @param ServicioBLL $servicioBLL
     * @return JsonResponse
     */
    public function getAll(ServicioBLL $servicioBLL) : Response
    {
        $result = $servicioBLL->getAll();
        return $this->getResponse($result);
    }
    /**
     * @Route(
     *     "/servicios/{id}.{_format}",
     *     name="apiv1_get_servicio",
     *     requirements={
     *          "id": "\d+"
     *     },
     *     defaults={"_format": "json"},
     *     methods={"GET"}
     * )
     * @param Servicio $servicio
     * @param ServicioBLL $servicioBLL
     * @return JsonResponse
     */
    public function getOne(Servicio $servicio, ServicioBLL $servicioBLL) : Response
    {
        return $this->getResponse($servicioBLL->toArray($servicio));
    }

    /**
     * @Route(
     *     "/servicios/new.{_format}",
     *     name="apiv1_post_new_service",
     *     defaults={"_format": "json"},
     *     requirements={"_format": "json"},
     *     methods={"POST"}
     *     )
     * @param Request $request
     * @param ServicioBLL $servicioBLL
     * @param OneSignalBLL $oneSignalBLL
     * @return JsonResponse
     */
    public function createService(Request $request, ServicioBLL $servicioBLL,OneSignalBLL $oneSignalBLL) : Response
    {
        $data = $this->getContent($request);

        $nuevoServicio = $servicioBLL->nuevo($data);

        $listadoAnuncios= $servicioBLL->getListadoService($nuevoServicio['id']);

        $device = $oneSignalBLL->sendPushDevice($data,$listadoAnuncios);

        return $this->getResponse(['allresponeses'  => $device], Response::HTTP_CREATED);

    }

    /**
     * @Route(
     *     "/servicios/{id}.{_format}",
     *     name="apiv1_delete_servicios",
     *     defaults={"_format": "json"}),
     *     requirements={
     *          "id": "\d+",
     *          "_format": "json"
     *     },
     *     methods={"DELETE"}
     * )
     * @param Servicio $servicio
     * @param ServicioBLL $servicioBLL
     * @return JsonResponse
     */
    public function delete(Servicio $servicio,ServicioBLL $servicioBLL) : Response
    {
        $servicioBLL->delete($servicio);

        return $this->getResponse(null, Response::HTTP_NO_CONTENT);
    }

}