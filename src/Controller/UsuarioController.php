<?php

namespace App\Controller;

use App\Entity\Anuncio;
use App\Entity\Tablet;
use App\Entity\Usuario;
use App\Form\UsuarioType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;


/**
 * @Route("/usuario")
 */
class UsuarioController extends AbstractController
{
    /**
     * @Route("/",
     *      name="usuario_index",
     *      methods={"GET"}
     * )
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(): Response
    {
        $usuarios = $this->getDoctrine()
            ->getRepository(Usuario::class)
            ->findBy(['role' => 'ROLE_USER']);

        return $this->render('usuario/index.html.twig', [
            'usuarios' => $usuarios,
        ]);
    }

    /**
     * @Route("/datatables_usuario",
     *     name="usuario_datatables",
     *     methods={"POST"},
     *     options={"expose"=true}
     *     )
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return JsonResponse
     */
    public function dataTable(Request $request): JsonResponse
    {
        $response = new JsonResponse();
        $data = $request->request->all();
        if (is_null($data))
            throw new BadRequestHttpException("No se reciben correctamente los datos de Datatables");

        $resultado = $this->getDoctrine()->getRepository(Usuario::class)->findListado($data, true);

        $response->setContent(json_encode($resultado));
        return $response;
    }

    /**
     * @Route("/registro/new",
     *      name="usuario_new",
     *      methods={"GET","POST"}
     * )
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $usuario = new Usuario();
        $form = $this->createForm(UsuarioType::class, $usuario, ['block_name' => 'nuevo']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $usuario->setPassword($passwordEncoder->encodePassword($usuario, $usuario->getPassword()));
            $entityManager->persist($usuario);
            $entityManager->flush();

            return $this->redirectToRoute('anuncio_index');
        }

        return $this->render('usuario/new.html.twig', [
            'usuario' => $usuario,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}",
     *      name="usuario_show",
     *      methods={"GET"},
     *      options={"expose" = true}
     * )
     * @IsGranted("ROLE_ADMIN")
     * @param Usuario $usuario
     * @return Response
     */
    public function show(Usuario $usuario): Response
    {
        $tablets = $this->getDoctrine()->getRepository(Tablet::class)->findBy(['idUsuario' => $usuario->getId()]);
        $anuncios = $this->getDoctrine()->getRepository(Anuncio::class)->findBy(['creador' => $usuario->getId()]);
        return $this->render('usuario/show.html.twig', [
            'usuario' => $usuario,
            'anuncios' => $anuncios,
            'tablets' => $tablets
        ]);
    }

    /**
     * @Route("/edit/{id}",
     *      name="usuario_edit",
     *      methods={"GET","POST"},
     *      options={"expose" = true}
     * )
     * @param Request $request
     * @param Usuario $usuario
     * @param Security $security
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function edit(Request $request, Usuario $usuario,Security $security, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $checkUser = $security->getUser();
        if($usuario->getUsername() == $checkUser->getUsername())
        {
            $form = $this->createForm(UsuarioType::class, $usuario, ['block_name' => 'editar']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager = $this->getDoctrine()->getManager();

                $usuario->setPassword($passwordEncoder->encodePassword($usuario, $usuario->getPassword()));
                $entityManager->persist($usuario);
                $entityManager->flush();

                return $this->redirectToRoute('anuncio_index');
            }

            return $this->render('usuario/edit.html.twig', [
                'usuario' => $usuario,
                'form' => $form->createView(),
            ]);
        }
        else{
            return $this->redirectToRoute('anuncio_index');
        }
    }

    /**
     * @Route("/{id}",
     *     name="usuario_delete",
     *     methods={"POST"},
     *     options={"expose" = true}
     * )
     * @IsGranted("ROLE_ADMIN")
     * @param Usuario $usuario
     * @return Response
     */
    public function delete(Usuario $usuario): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($usuario);
        $entityManager->flush();

        return $this->redirectToRoute('usuario_index');
    }

    /**
     * @Route("/{id}/estado",
     *     name="usuario_activado",
     *     methods={"POST"},
     *     options={"expose" = true}
     * )
     * @IsGranted("ROLE_ADMIN")
     * @param Usuario $usuario
     * @return Response
     */
    public function cambiarEstado(Usuario $usuario): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        if (!$usuario) {
            throw $this->createNotFoundException('No se encuentra el usuario: ' . $usuario->getUsername());
        }
        if ($usuario->getActivado()) {
            $usuario->setActivado(false);
        } else {
            $usuario->setActivado(true);
        }
        $entityManager->persist($usuario);
        $entityManager->flush();

        return $this->redirectToRoute('usuario_index');
    }
}
