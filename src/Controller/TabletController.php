<?php


namespace App\Controller;


use App\Entity\Tablet;
use App\Form\TabletType;
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
 * @Route("/tablet")
 */
class TabletController extends AbstractController
{
    /**
     * @Route("/",
     *      name="tablet_index",
     *      methods={"GET"}
     * )
     * @param Security $security
     * @return Response
     */
    public function index(Security $security): Response
    {
        if ($usuario = $security->getUser() == null) {
            return $this->redirect('security/login.html.twig');
        }
        return $this->render('tablet/index.html.twig');
    }
    /**
     * @Route("/datatables_tablet",
     *     name="tablet_datatables",
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
            $resultado = $this->getDoctrine()->getRepository(Tablet::class)->findListado($data, true);
        } else {
            $resultado = $this->getDoctrine()->getRepository(Tablet::class)->findListado($data, false, $usuario->getId());
        }

        $response->setContent(json_encode($resultado));
        return $response;
    }

    /**
     * @Route("/{id}",
     *      name="tablet_show",
     *      methods={"GET"},
     *      options={"expose"=true}
     * )
     * @param Tablet $tablet
     * @return Response
     */
    public function show(Tablet $tablet): Response
    {
       $anuncios = $this->getDoctrine()->getRepository(Tablet::class)->findByOneSignalIdTablet($tablet->getIdOneSignal());

        return $this->render('tablet/show.html.twig', [
            'tablet' => $tablet,
            'anuncios' => $anuncios,
        ]);
    }

    /**
     * @Route("/{id}/edit",
     *      name="tablet_edit",
     *      methods={"GET","POST"},
     *      options={"expose"=true}
     * )
     * @param Request $request
     * @param Tablet $tablet
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function edit(Request $request, Tablet $tablet, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(TabletType::class, $tablet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imagen */
            $imagen = $form['imagenCorporativa']->getData();
            if ($imagen) {
                $imagenFileName = $fileUploader->uploadImagenCorp($imagen);
                $tablet->setImagenCorporativa($imagenFileName);
            }

            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($tablet);
                $entityManager->flush();
            }catch (\Exception $exception)
            {
                $error = 'La matricula ya se encuentra registrada';
                return $this->render('tablet/edit.html.twig', [
                    'error' => $error,
                    'tablet' => $tablet,
                    'form' => $form->createView(),
                ]);
            }


            return $this->redirectToRoute('tablet_index');
        }

        return $this->render('tablet/edit.html.twig', [
            'tablet' => $tablet,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}",
     *      name="tablet_delete",
     *      methods={"POST"},
     *      options={"expose" = true}
     * )
     * @param Tablet $tablet
     * @return Response
     */
    public function delete(Tablet $tablet): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($tablet);
        $entityManager->flush();

        return $this->redirectToRoute('tablet_index');
    }
}