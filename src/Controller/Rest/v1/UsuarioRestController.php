<?php


namespace App\Controller\Rest\v1;


use App\BLL\UsuarioBLL;
use App\Controller\Rest\BaseApiController;
use App\Entity\Usuario;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1")
 */
class UsuarioRestController extends BaseApiController
{


    /**
     * @Route(
     *     "/usuarios/.{_format}",
     *     name="apiv1_get_usuarios",
     *     defaults={"_format": "json"},
     *     requirements={"_format": "json"},
     *     methods={"GET"}
     * )
     * @param UsuarioBLL $usuarioBLL
     * @return JsonResponse
     */
    public function getAll(UsuarioBLL $usuarioBLL) : Response
    {
        $result = $usuarioBLL->getAll();

        return $this->getResponse($result);
    }

    /**
     * @Route(
     *     "/usuarios/{id}.{_format}",
     *     name="apiv1_get_usuario",
     *     defaults={"_format": "json"},
     *     requirements={
     *          "id": "\d+",
     *          "_format": "json"
     *     },
     *     methods={"GET"}
     * )
     * @param Usuario $usuario
     * @param UsuarioBLL $usuarioBLL
     * @return JsonResponse
     */
    public function getOne(Usuario $usuario, UsuarioBLL $usuarioBLL) : Response
    {
        return $this->getResponse($usuarioBLL->toArray($usuario));
    }

    /**
     * @Route(
     *     "/usuarios/{id}.{_format}",
     *     name="apiv1_delete_usuario",
     *     defaults={"_format": "json"},
     *     requirements={
     *          "id": "\d+",
     *          "_format": "json"
     *     },
     *     methods={"DELETE"}
     * )
     * @param Usuario $usuario
     * @param UsuarioBLL $usuarioBLL
     * @return JsonResponse
     */
    public function delete(Usuario $usuario, UsuarioBLL $usuarioBLL) : Response
    {
        $usuarioBLL->delete($usuario);

        return $this->getResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(
     *     "/usuarios/{id}.{_format}",
     *     name="api_v1_cambiar_rol",
     *     defaults={"_format": "json"},
     *     requirements={
     *          "_format": "json"
     *     },
     *     methods={"PATCH"}
     * )
     * @param Request $request
     * @param Usuario $usuario
     * @param UsuarioBLL $usuarioBLL
     * @return JsonResponse
     */
    public function cambiarRol(Request $request,Usuario $usuario, UsuarioBLL $usuarioBLL) : Response
    {
        $data = $this->getContent($request);

        if (is_null($data['role']))
            throw new BadRequestHttpException('No se ha recibido el rol');

        $editado = $usuarioBLL->cambiarRol($usuario,$data['role']);

        return $this->getResponse($editado);
    }

}