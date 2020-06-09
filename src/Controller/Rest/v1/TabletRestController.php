<?php


namespace App\Controller\Rest\v1;


use App\BLL\TabletBLL;
use App\Controller\Rest\BaseApiController;
use App\Entity\Tablet;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1")
 */

class TabletRestController extends BaseApiController
{

    /**
     * @Route(
     *     "/tablets.{_format}",
     *     name="apiv1_get_tablets",
     *     defaults={"_format": "json"},
     *     requirements={"_format": "json"},
     *     methods={"GET"}
     * )
     * @param TabletBLL $tabletBLL
     * @return JsonResponse
     */
    public function getAll(TabletBLL $tabletBLL) : Response
    {
        $result = $tabletBLL->getAll();
        return $this->getResponse($result);
    }

    /**
     * @Route(
     *     "/tablets/{id}.{_format}",
     *     name="apiv1_get_tablet",
     *     requirements={
     *          "id": "\d+",
     *          "_format": "json"
     *     },
     *     defaults={"_format": "json"},
     *     methods={"GET"}
     * )
     * @param Tablet $tablet
     * @param TabletBLL $tabletBLL
     * @return JsonResponse
     */
    public function getOne(Tablet $tablet, TabletBLL $tabletBLL) : Response
    {
        return $this->getResponse($tabletBLL->toArray($tablet));
    }

    /**
     * @Route(
     *     "/tablets/registro_videos.{_format}",
     *     name="apiv1_post_registro_videos",
     *     defaults={"_format": "json"},
     *     requirements={"_format": "json"},
     *     methods={"POST"}
     * )
     * @param Request $request
     * @param TabletBLL $tabletBLL
     * @return JsonResponse
     */
    public function registroTabletAnuncio(Request $request,TabletBLL $tabletBLL) : Response
    {
        $data = $this->getContent($request);
        $registro = $tabletBLL->registroTabletAnuncio($data);

        return $this->getResponse($registro, Response::HTTP_OK);
    }
    /**
     * @Route(
     *     "/tablets/delete_videos.{_format}",
     *     name="apiv1_post_delete_videos",
     *     defaults={"_format": "json"},
     *     requirements={"_format": "json"},
     *     methods={"POST"}
     * )
     * @param Request $request
     * @param TabletBLL $tabletBLL
     * @return JsonResponse
     */
    public function deleteTabletAnuncio(Request $request,TabletBLL $tabletBLL) : Response
    {
        $data = $this->getContent($request);
        $tabletBLL->deleteTabletAnuncio($data);

        return $this->getResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(
     *     "/tablets/{id}.{_format}",
     *     name="apiv1_delete_tablet",
     *     defaults={"_format": "json"},
     *     requirements={
     *          "id": "\d+",
     *          "_format": "json"
     *     },
     *     methods={"DELETE"}
     * )
     * @param Tablet $tablet
     * @param TabletBLL $tabletBLL
     * @return JsonResponse
     */
    public function delete(Tablet $tablet, TabletBLL $tabletBLL) : Response
    {
        $tabletBLL->delete($tablet);

        return $this->getResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(
     *     "/tablets/{id}.{_format}",
     *     name="apiv1_patch_tablet_plate",
     *     requirements={"id": "\d+","_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"PATCH"}
     * )
     * @param Request $request
     * @param Tablet $tablet
     * @param TabletBLL $tabletBLL
     * @return JsonResponse
     */
    public function cambiaMatricula(Request $request,Tablet $tablet,TabletBLL $tabletBLL) : Response
    {
        $data = $this->getContent($request);

        if(!$tabletBLL->checkMatricula($data['matricula']))
            throw new BadRequestHttpException('Formato de matricula incorrecto');

        $actualizacion = $tabletBLL->updateMatricula($tablet,$data['matricula']);
        if(!$actualizacion){
            return $this->getResponse(['La matricula ya esta en uso'],Response::HTTP_CONFLICT);
        }
        return $this->getResponse($actualizacion,Response::HTTP_OK);
    }
}