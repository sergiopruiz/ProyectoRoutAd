<?php


namespace App\Repository;

use App\Entity\Anuncio;
use App\Entity\Tablet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AnuncioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Anuncio::class);
    }

    /*
     * Recibe la id de one signal y  devuelve los anuncios del usuario de  tablet que todavía no se han descargado.
    */
    public function findByOneSignalId(string $oneSignalID)
    {
        $id_usuario = $this->_em->getRepository(Tablet::class)->findCreatorWithOneSignalID($oneSignalID);

        $subQuery = $this->_em->createQueryBuilder()->select('TA')
            ->from('App:TabletAnuncio', 'TA')
            ->where('TA.anuncio = A.id');
        $qb = $this->createQueryBuilder('A');
        $qb->select('A.id')
            ->addSelect('A.video')
            ->addSelect('A.imagen')
            ->addSelect('A.hash')
            ->where('A.creador = :osid')
            ->andWhere('A.activo = 1')
            ->andWhere($qb->expr()->not($qb->expr()->exists($subQuery->getDQL())))
            ->setParameter('osid', $id_usuario);

        return $qb->getQuery()->execute();
    }

    public function findLastDel(int $id)
    {
        $anuncio = $this->_em->getRepository(Anuncio::class)->find($id);
        $qb = $this->createQueryBuilder('a');
        $qb->delete('App:Anuncio','a')
            ->where('a.id = :idanuncio')
            ->setParameter('idanuncio',$anuncio);
        $qb->getQuery()->execute();
    }

    public function findListado(array $data, bool $admin, int $idCreador = null): array
    {
        if (!isset($data['search']) || !isset($data['search']['value']) || !isset($data['columns']) ||
            !isset($data['length']) || !isset($data['start']) || !isset($data['order']))
            throw new BadRequestHttpException("Parámetros incorrectos en el datatable");

        $qb = $this->_em->createQueryBuilder();
        if ($admin) {
            $qb->select('a.id')
                ->addSelect('a.video')
                ->addSelect('a.duracion')
                ->addSelect('a.activo')
                ->from('App:Anuncio', 'a');
        } else {
            $qb->select('a.id')
                ->addSelect('a.video')
                ->addSelect('a.duracion')
                ->addSelect('a.activo')
                ->from('App:Anuncio', 'a')
                ->where('a.creador = :idCreador')
                ->setParameter('idCreador', $idCreador);
        }

        $this->addFiltros($qb,$data);

        if ($data['length'] !== -1) {
            $qb->setFirstResult($data['start'])
                ->setMaxResults($data['length']);
        }

        $anuncios = $qb->getQuery()->execute();

        $result['data'] = $anuncios;

        $result['recordsTotal'] = $this->getNumRegistros($admin, $idCreador);
        $result['recordsFiltered'] = $this->getNumRegistros($admin, $idCreador);
        $result['draw'] = (int)$data['draw'];

        return $result;

    }

    private function getNumRegistros(bool $admin, int $idCreador = null): int
    {
        $qb = $this->_em->createQueryBuilder('a');
        if ($admin) {
            $qb->select('count(a.id)');
            $qb->from('App:Anuncio', 'a');
        } else {
            $qb->select('count(a.id)');
            $qb->from('App:Anuncio', 'a')
                ->where('a.creador = :idCreador')
                ->setParameter('idCreador', $idCreador);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    private function addFiltros(QueryBuilder $qb,array $data)
    {
        $this->filtroBusqueda($qb,$data);
        $this->FiltroOrder($qb,$data);
    }

    private function filtroBusqueda(QueryBuilder $qb, array $data)
    {
        $busqueda = $data['search']['value'];
        if(!empty($busqueda))
        {
            $qb->Andwhere('a.video LIKE :busqueda')
            ->setParameter('busqueda','%'.$busqueda.'%');
            return $qb;
        }
    }

    private function FiltroOrder(QueryBuilder $qb, array $data)
    {
        $orden = 'ASC';
        if($data['order'][0]['dir'] == 'desc'){
            $orden = 'DESC';
        }

        switch ($data['order'][0]['column'])
        {
            case '0':
                $qb->orderBy('a.id',$orden);
                return $qb;
            case '1':
                $qb->orderBy('a.video',$orden);
                return $qb;
            case '2':
                $qb->orderBy('a.duracion',$orden);
                return $qb;
            case '3':
                $qb->orderBy('a.activo',$orden);
                return $qb;
            default:
                throw new BadRequestHttpException('Nombre de columna incorrecto');
        }
    }
}