<?php


namespace App\BLL;


use App\Entity\Anuncio;
use App\Entity\Tablet;
use App\Entity\TabletAnuncio;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TabletBLL extends BaseBLL
{
    public function getAll(): array
    {
        $tablets = $this->entityManager->getRepository(Tablet::class)->findAll();
        return $this->entitiesToArray($tablets);
    }

    public function updateMatricula(Tablet $tablet, string $matricula)
    {
        $checKTablet = $this->entityManager->getRepository(Tablet::class)->findOneBy(['matricula' => $matricula]);
        if(isset($checKTablet)){
            return false;
        }
        $tablet->setMatricula($matricula);

        return $this->guardaValidando($tablet);
    }

    public function checkMatricula(string $matricula): bool
    {
        if (preg_match('/^[0-9]{4}[a-zA-Z]{3}$/', $matricula)) {
            return true;
        }
        return false;
    }

    public function registroTabletAnuncio(array $data)
    {
        $createTabletAnuncio = new TabletAnuncio();
        $tablet = $this->entityManager->getRepository(Tablet::class)->findOneBy(['idOneSignal' => $data['idDevice']]);
        $anuncio = $this->entityManager->getRepository(Anuncio::class)->findOneBy(['video' => $data['video']]);
        if(!isset($tablet) || !isset($anuncio))
            throw new BadRequestHttpException('Revisar ids, son incorrectas');

        $createTabletAnuncio->setTablet($tablet);
        $createTabletAnuncio->setAnuncio($anuncio);

        $existe = $this->entityManager->getRepository(TabletAnuncio::class)->findOneBy(['tablet' => $tablet, 'anuncio' => $anuncio]);

        if (!$existe) {
            $this->entityManager->persist($createTabletAnuncio);
            $this->entityManager->flush();
            return $this->toArrayTabletAnuncio($createTabletAnuncio);
        } else {
            throw new BadRequestHttpException('La tablet con id: ' . $tablet->getId() . ' ya tiene disponible el anuncio: ' .
                $anuncio->getVideo());
        }

    }
    public function deleteTabletAnuncio(array $data): void
    {
        $tablet = $this->entityManager->getRepository(Tablet::class)->findOneBy(['idOneSignal' => $data['idDevice']]);
        $anuncio = $this->entityManager->getRepository(Anuncio::class)->findOneBy(['video' => $data['video']]);
        $existe = $this->entityManager->getRepository(TabletAnuncio::class)->findOneBy(['tablet' => $tablet, 'anuncio' => $anuncio]);

        if ($existe) {
            $this->entityManager->remove($existe);
            $this->entityManager->flush();
        } else {
            throw new BadRequestHttpException('La tablet con id: ' . $tablet->getId() . ' no tiene registrado el anuncio: ' .
                $anuncio->getVideo());
        }

    }

    public function toArray($tablet): array
    {
        return [
            'id' => $tablet->getId(),
            'matricula' => $tablet->getMatricula(),
            'idOneSignal' => $tablet->getIdOneSignal(),
            'fecha_update' => $tablet->getFechaUpdate()->format('d-m-Y H:i:s'),
            'imagenCorporativa' => $tablet->getImagenCorporativa(),
            'idUsuario' => $tablet->getIdUsuario(),
        ];
    }

    public function toArrayTabletAnuncio($tabletAnucio): array
    {
        return [
            'id' => $tabletAnucio->getId(),
            'tablet' => $tabletAnucio->getTablet()->getId(),
            'anuncio' => $tabletAnucio->getAnuncio()->getId(),
            'fechaDescarga' => $tabletAnucio->getFechaDescarga(),
        ];
    }


}