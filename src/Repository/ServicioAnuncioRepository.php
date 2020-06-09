<?php


namespace App\Repository;

use App\Entity\ServicioAnuncio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ServicioAnuncioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServicioAnuncio::class);
    }

    public function findByIdServicioAnuncios(int $idServicioAnuncios)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('sa');
        $qb->from('App:ServicioAnuncio', 'sa')
            ->where('sa.idServicio = :id')
            ->setParameter('id', $idServicioAnuncios);

        return $qb->getQuery()->execute();
    }

}