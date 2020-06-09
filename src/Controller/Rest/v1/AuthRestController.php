<?php


namespace App\Controller\Rest\v1;

use App\BLL\UsuarioBLL;
use App\Controller\Rest\BaseApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1")
 */

class AuthRestController extends BaseApiController
{
    /**
     * @Route("/auth/login")
     */
    public function getTokenAction()
    {
        // The security layer will intercept this request
        return new Response('', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @Route(
     *     "/auth/register.{_format}", name="apiv1_register",
     *     requirements={
     *          "_format": "json"
     *     },
     *     defaults={"_format": "json"},
     *     methods={"POST"}
     * )
     * @param Request $request
     * @param UsuarioBLL $usuarioBLL
     * @return JsonResponse
     */
    public function register(Request $request, UsuarioBLL $usuarioBLL) : Response
    {
        $data = $this->getContent($request);

        $user = $usuarioBLL->nuevo($data);

        return $this->getResponse($user, Response::HTTP_CREATED);
    }

}