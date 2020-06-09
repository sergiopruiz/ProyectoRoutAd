<?php


namespace App\BLL;

use App\Entity\Anuncio;

class AnuncioBLL extends BaseBLL
{

    public function getAll(): array
    {
        $anuncios = $this->entityManager->getRepository(Anuncio::class)->findAll();
        return $this->entitiesToArray($anuncios);
    }

    public function getVideoURL(int $idAnuncio): string
    {

        $video = $this->entityManager->getRepository(Anuncio::class)->find($idAnuncio)->getVideo();

        return $this->BASE_URL . $this->VIDEO_DIR . $video;
    }

    public function checkAnucios(string $oneSignalID): array
    {
        return $this->entityManager->getRepository(Anuncio::class)->findByOneSignalId($oneSignalID);
    }

    public function toArray($anuncio): array
    {
        return [
            'id' => $anuncio->getId(),
            'video' => $anuncio->getVideo(),
            'hash' => $anuncio->getHash(),
            'url' => $anuncio->getUrl(),
            'imagen' => $anuncio->getImagen(),
            'duracion' => $anuncio->getDuracion(),
            'latitud' => $anuncio->getLatitud(),
            'longitud' => $anuncio->getLongitud(),
            'activo' => $anuncio->getActivo(),
        ];
    }
}