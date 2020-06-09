<?php

namespace App\Controller;

use App\BLL\ReproduccionBLL;
use App\Entity\Anuncio;
use App\Entity\Usuario;
use App\Form\AnuncioType;
use App\Services\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/anuncio")
 */
class AnuncioController extends AbstractController
{
    /**
     * @Route("/",
     *      name="anuncio_index",
     *      methods={"GET"})
     * @param Security $security
     * @return Response
     */
    public function index(Security $security): Response
    {
        if ($usuario = $security->getUser() == null)
            return $this->redirect('security/login.html.twig');

        return $this->render('anuncio/index.html.twig');
    }

    /**
     * @Route("/datatables_anuncio",
     *     name="anuncio_datatables",
     *     methods={"POST"},
     *     options={"expose"=true}
     *     )
     *
     * @param Security $security
     * @param Request $request
     * @return JsonResponse
     */
    public function dataTable(Request $request, Security $security): JsonResponse
    {
        $response = new JsonResponse();
        $data = $request->request->all();
        if (is_null($data))
            throw new BadRequestHttpException("No se reciben correctamente los datos de Datatables");

        $usuario = $security->getUser();
        if ($this->isGranted('ROLE_ADMIN')) {
            $resultado = $this->getDoctrine()->getRepository(Anuncio::class)->findListado($data, true);
        } else {
            $resultado = $this->getDoctrine()->getRepository(Anuncio::class)->findListado($data, false, $usuario->getId());
        }

        $response->setContent(json_encode($resultado));
        return $response;
    }


    /**
     * @Route("/new",
     *     name="anuncio_new",
     *     methods={"GET","POST"}
     *     )
     * @param Request $request
     * @param FileUploader $fileUploader
     * @param Security $security
     * @return Response
     */
    public function new(Request $request, FileUploader $fileUploader, Security $security): Response
    {
        $anuncio = new Anuncio();
        $usuario = $this->getDoctrine()
            ->getRepository(Usuario::class)
            ->findOneBy([
                'id' => $security->getUser()->getId()
            ]);

        $form = $this->createForm(AnuncioType::class, $anuncio, ['block_name' => 'nuevo']);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            /** @var UploadedFile $video */
            $video = $form['video']->getData();
            if ($video) {
                $anuncio->setDuracion($form['duracion']->getData());
                $anuncio->setCreador($usuario);
            }
            $entityManager->persist($anuncio);
            $entityManager->flush();

            $idAnuncio = $anuncio->getId();

            $videoFileName = $fileUploader->upload($video, $idAnuncio);
            $anuncio->setVideo($videoFileName);
            $anuncio->setHash(md5_file('uploads/anuncios/' . $videoFileName));

            /** @var UploadedFile $imagen */
            $imagen = $form['imagen']->getData();
            if ($imagen) {
                $imagenFileName = $fileUploader->upload($imagen, $idAnuncio, explode('.mp4', $videoFileName)[0]);
                $anuncio->setImagen($imagenFileName);
            }
            try {
                $entityManager->persist($anuncio);
                $entityManager->flush();
            } catch (\Exception $exception){
                $this->getDoctrine()->getRepository(Anuncio::class)->findLastDel($idAnuncio);
                $error ='Ya existe el video del anuncio';
                return $this->render('anuncio/new.html.twig', [
                    'error' =>  $error,
                    'anuncio' => $anuncio,
                    'form' => $form->createView(),
                ]);
            }


            return $this->render('anuncio/index.html.twig');
        }

        return $this->render('anuncio/new.html.twig', [
            'anuncio' => $anuncio,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}",
     *     name="anuncio_show",
     *     methods={"GET"},
     *     options={"expose" = true}
     *     )
     * @param Anuncio $anuncio
     * @return Response
     */
    public function show(Anuncio $anuncio, ReproduccionBLL $reproduccionBLL): Response
    {
        $reproducciones = $reproduccionBLL->getNumeroReproducciones($anuncio->getId());
        return $this->render('anuncio/show.html.twig', [
            'anuncio' => $anuncio,
            'reproducciones' => $reproducciones
        ]);
    }

    /**
     * @Route("/{id}/edit",
     *     name="anuncio_edit",
     *     methods={"GET","POST"},
     *     options={"expose" = true})
     * @param Request $request
     * @param Anuncio $anuncio
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function edit(Request $request, Anuncio $anuncio, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(AnuncioType::class, $anuncio, ['block_name' => 'editar']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $idAnuncio = $anuncio->getId();

            $entityManager = $this->getDoctrine()->getManager();
            /** @var UploadedFile $video */
            $video = $form['video']->getData();
            if ($video) {
                $anuncio->setDuracion($form['duracion']->getData());

                $videoFileName = $fileUploader->upload($video, $idAnuncio);
                $anuncio->setVideo($videoFileName);
                $anuncio->setHash(md5_file('uploads/anuncios/' . $videoFileName));

                /** @var UploadedFile $imagen */
                $imagen = $form['imagen']->getData();
                if ($imagen) {
                    $imagenFileName = $fileUploader->upload($imagen, $idAnuncio, explode('.mp4', $videoFileName)[0]);
                    $anuncio->setImagen($imagenFileName);
                }
            }

            /** @var UploadedFile $imagen */
            $imagen = $form['imagen']->getData();
            if ($imagen && !$video) {
                $videoFileName = $this->getDoctrine()->getRepository(Anuncio::class)->findOneBy(['id' => $idAnuncio]);
                $imagenFileName = $fileUploader->upload($imagen, $idAnuncio, explode('.mp4', $videoFileName->getVideo())[0]);
                $anuncio->setImagen($imagenFileName);
            }

            try {
                $entityManager->persist($anuncio);
                $entityManager->flush();
            } catch (\Exception $exception){
                $error ='Ya existe el video del anuncio';
                return $this->render('anuncio/edit.html.twig', [
                    'error' =>  $error,
                    'anuncio' => $anuncio,
                    'form' => $form->createView(),
                ]);
            }

            return $this->render('anuncio/index.html.twig');
        }

        return $this->render('anuncio/edit.html.twig', [
            'anuncio' => $anuncio,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}",
     *     name="anuncio_delete",
     *     methods={"POST"},
     *     options={"expose" = true}
     *     )
     * @param Anuncio $anuncio
     * @return Response
     */
    public function delete(Anuncio $anuncio)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($anuncio);
        $entityManager->flush();
        return new JsonResponse(['ok' => false]);
    }

}
