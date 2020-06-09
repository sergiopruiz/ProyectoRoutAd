<?php


namespace App\BLL;


use App\Entity\Anuncio;
use App\Entity\Reproduccion;
use App\Entity\Servicio;
use App\Repository\ReproduccionRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ReproduccionBLL extends BaseBLL
{
    public function getNumeroReproducciones(int $idAnuncio)
    {
        $numRepro = $this->entityManager->getRepository(Reproduccion::class)->findBy(['idAnuncio' => $idAnuncio]);
        if(is_null($numRepro)){
            throw new BadRequestHttpException('Id del anuncio incorrecto');
        }
        return count($numRepro);
    }

    public function save($data)
    {
        if(!isset($data['id_anuncio']) || !isset($data['id_servicio']) ||
            !isset($data['longitud']) || !isset($data['latitud']))
            throw new BadRequestHttpException('No se reciben los campos correctamente');

        $anuncio = $this->entityManager->getRepository(Anuncio::class)->find($data['id_anuncio']);
        $servicio = $this->entityManager->getRepository(Servicio::class)->find($data['id_servicio']);

        $reproduccion = new Reproduccion();
        $reproduccion->setIdAnuncio($anuncio);
        $reproduccion->setIdServicio($servicio);
        $reproduccion->setLatitud($data['latitud']);
        $reproduccion->setLongitud($data['longitud']);

        $this->entityManager->persist($reproduccion);
        $this->entityManager->flush();

        return $this->toArray($reproduccion);
    }

    public function toArray($reproduccion): array
    {
        return [
            'id' => $reproduccion->getId(),
            'id_anuncio' => $reproduccion->getIdAnuncio(),
            'id_servicio' => $reproduccion->getIdServicio(),
            'fecha' => $reproduccion->getFecha()->format('d-m-Y H:i:s'),
            'latitud' => $reproduccion->getLatitud(),
            'longitud' => $reproduccion->getLongitud(),
        ];
    }
}