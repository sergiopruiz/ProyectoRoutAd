<?php


namespace App\Controller\Rest\v1;


use App\BLL\OneSignalBLL;
use App\Controller\Rest\BaseApiController;
use App\Entity\Tablet;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/v1")
 */

class OneSignalRestController extends BaseApiController
{
    /**
     * @Route(
     *     "/oneSignal/devices.{_format}",
     *     name="apiv1_get_devices",
     *     defaults={"_format": "json"},
     *     requirements={"_format": "json"},
     *     methods={"GET"}
     * )
     * @param OneSignalBLL $oneSignalBLL
     * @return JsonResponse
     */
    public function getDevices(OneSignalBLL $oneSignalBLL) : Response
    {
        $devices = $oneSignalBLL->getAllDevices();

        return $this->getResponse(['allresponeses' => $devices]);
    }

    /**
     * @Route(
     *     "/oneSignal/new_device.{_format}",
     *     name="apiv1_post_new_device",
     *     defaults={"_format": "json"},
     *     methods={"POST"}
     * )
     * @param Request $request
     * @param OneSignalBLL $oneSignalBLL
     * @return JsonResponse
     */
    public function newDevice(Request $request, OneSignalBLL $oneSignalBLL) : Response
    {
        $data = $this->getContent($request);
        $idDevice = $this->getDoctrine()->getRepository(Tablet::class)->findOneBy(['idOneSignal' => $data['idDevice']]);
        if(is_null($idDevice)){
            $device = $oneSignalBLL->newDevice($data);

            return $this->getResponse(['allresponeses'  => $device], Response::HTTP_CREATED);
        }else {
            return $this->getResponse([200 => 'Tablet registrada en el sistema']);
        }
    }

    /**
     * @Route(
     *     "/oneSignal/push.{_format}",
     *     name="apiv1_post_push",
     *     defaults={"_format": "json"},
     *     methods={"POST"}
     * )
     * @param Request $request
     * @param OneSignalBLL $oneSignalBLL
     * @return JsonResponse
     */
    public function sendDevice(Request $request, OneSignalBLL $oneSignalBLL) : Response
    {
        $data = $this->getContent($request);

        $device = $oneSignalBLL->sendPushDevice($data);

        return $this->getResponse(['allresponeses'  => $device], Response::HTTP_CREATED);
    }
}