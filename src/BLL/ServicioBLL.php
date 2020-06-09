<?php


namespace App\BLL;

use App\Entity\Anuncio;
use App\Entity\Servicio;
use App\Entity\ServicioAnuncio;
use App\Entity\Tablet;
use App\Entity\Usuario;
use App\Services\MapBox;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class ServicioBLL extends BaseBLL
{
    function distanceCalculation(string $lat1, string $lon1, string $lat2, string $lon2)
    {
        $degrees = rad2deg(acos((sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($lon1 - $lon2)))));

        $distance = $degrees * 111.13384;

        return round($distance, 2);
    }

    // La distancia define los metros que tienen que haber para comprobar el punto de control con el anuncio.
    // De esta forma definimos un radio de acción.
    function checkAnuncio(Anuncio $anuncio, array $checkpoint, int $distancia = 3)
    {
        $enRango = false;
        $distanciaAnuncio = $this->distanceCalculation($anuncio->getLatitud(), $anuncio->getLongitud(), $checkpoint[1], $checkpoint[0]);
        if ($distanciaAnuncio < $distancia) {
            $enRango = true;
        }
        return $enRango;
    }
    public function getAll(): array
    {
        $servicios = $this->entityManager->getRepository(Servicio::class)->findAll();
        return $this->entitiesToArray($servicios);
    }

    public function nuevo(array $data)
    {
        $mapBox = new MapBox;
        if (!isset($data['matricula']) || !isset($data['latitud_origen']) || !isset($data['latitud_destino'])
            || !isset($data['longitud_origen']) || !isset($data['longitud_destino']))
            throw new BadRequestHttpException('Revisar campos de entrada');

        $tablet = $this->entityManager->getRepository(Tablet::class)->findOneBy(['matricula' => $data['matricula']]);

        $servicio = new Servicio();
        if(is_null($tablet)){
            throw new BadRequestHttpException('La matricula incorrecta o inexistente');
        }
        $servicio->setTabletId($tablet);

        $servicio->setLatitudOrigen($data['latitud_origen']);
        $servicio->setLatitudDestino($data['latitud_destino']);

        $servicio->setLongitudOrigen($data['longitud_origen']);
        $servicio->setLongitudDestino($data['longitud_destino']);

        $distancia = $this->distanceCalculation($servicio->getLatitudOrigen(), $servicio->getLongitudOrigen(),
            $servicio->getLatitudDestino(), $servicio->getLongitudDestino());
        $servicio->setDuracionRuta(intval($distancia));
        $this->entityManager->persist($servicio);
        $this->entityManager->flush();

        $ruta = $mapBox->getRoute($servicio->getLatitudOrigen(), $servicio->getLongitudOrigen(),
            $servicio->getLatitudDestino(), $servicio->getLongitudDestino());
        $checkpoint = $ruta->routes[0];

        $usuario = $this->entityManager->getRepository(Usuario::class)->find($tablet->getIdUsuario());

        $anuncios = $this->entityManager->getRepository(Anuncio::class)->findBy(['creador' => $usuario->getId() ]);

        foreach ($anuncios as $anuncio) {
            foreach ($checkpoint->geometry->coordinates as $checkP) {
                if ($this->checkAnuncio($anuncio, $checkP)) {
                    if($this->verificarServicioAnuncio($servicio,$anuncio))
                    {
                        $servicioAnuncios = new ServicioAnuncio();
                        $servicioAnuncios->setIdServicio($servicio);
                        $servicioAnuncios->setIdAnuncio($anuncio);
                        $servicioAnuncios->setSegundoReproduccion(10);
                        $this->entityManager->persist($servicioAnuncios);
                        $this->entityManager->flush();
                    }
                }
            }
        }

        return $this->toArray($servicio);
    }


    public function getListadoService(int $id): array
    {
        $duracionAnuncios = 0;
        $listadoAnuncios = [];

        $duracionTotalRuta = $this->entityManager->getRepository(Servicio::class)->find($id);
        $duracionTotalRuta = $duracionTotalRuta->getDuracionRuta() * 60;

        $listadoServicioAnuncio = $this->entityManager->getRepository(ServicioAnuncio::class)->findByIdServicioAnuncios($id);

        if (isset($listadoServicioAnuncio)) {
            foreach ($listadoServicioAnuncio as $servicio) {
                if ($duracionAnuncios < $duracionTotalRuta) {
                    $duracionAnuncios += $servicio->getIdAnuncio()->getDuracion();
                    $addAnuncio = $servicio->getIdAnuncio()->getVideo();
                    array_push($listadoAnuncios,$addAnuncio);
                }
            }
            return $listadoAnuncios = [
                        'anuncios' => $listadoAnuncios,
                        'idServicio' => $id,
                        'waitTime' => 30
            ];
        }
        return ['resultado' => 'No hay anuncios en la ruta'];
    }

    private function verificarServicioAnuncio(Servicio $servicio,Anuncio $anuncio) : bool
    {
        $anuncioServicio = $this->entityManager->getRepository(ServicioAnuncio::class)->findOneBy(['idAnuncio' => $anuncio->getId(),'idServicio' => $servicio->getId()]);
        if(is_null($anuncioServicio)){
            return true;
        } else {
            return false;
        }
    }

    public function toArray($servicio): array
    {
        return [
            'id' => $servicio->getId(),
            'tablet_id' => $servicio->getTabletId(),
            'fecha' => $servicio->getFecha()->format('d-m-Y H:i:s'),
            'latitud_origen' => $servicio->getLatitudOrigen(),
            'longitud_origen' => $servicio->getLongitudOrigen(),
            'latitud_destino' => $servicio->getLatitudDestino(),
            'longitud_destino' => $servicio->getLongitudDestino(),
            'duracion_ruta' => $servicio->getDuracionRuta()
        ];
    }

    public function toArrayServiceList($servicioAnuncio): array
    {
        return [
            'id' => $servicioAnuncio->getId(),
            'id_servicio' => $servicioAnuncio->getId_anuncio(),
            'id_anuncio' => $servicioAnuncio->getId_servicio(),
        ];
    }


}