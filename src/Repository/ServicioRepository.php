<?php


namespace App\Repository;

use App\Entity\Servicio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ServicioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Servicio::class);
    }

    public function findListado(array $data, int $idTablet): array
    {
        if (!isset($data['search']) || !isset($data['search']['value']) || !isset($data['columns']) ||
            !isset($data['length']) || !isset($data['start']) || !isset($data['order']))
            throw new BadRequestHttpException("Parámetros incorrectos en el datatable");

        $qb = $this->_em->createQueryBuilder();

            $qb->select('s.id')
                ->addSelect('s.fecha')
                ->addSelect('s.latitudOrigen')
                ->addSelect('s.longitudOrigen')
                ->addSelect('s.latitudDestino')
                ->addSelect('s.longitudDestino')
                ->addSelect('s.duracionRuta')
                ->from('App:Servicio', 's')
                ->where('s.tabletId = :tablet')
                ->setParameter('tablet', $idTablet);

        $this->addFiltros($qb,$data);

        if ($data['length'] !== -1) {
            $qb->setFirstResult($data['start'])
                ->setMaxResults($data['length']);
        }

        $servicios = $qb->getQuery()->execute();

        $result['data'] = $servicios;


        $result['recordsTotal'] = $this->getNumRegistros($idTablet);
        $result['recordsFiltered'] = $this->getNumRegistros($idTablet);
        $result['draw'] = (int)$data['draw'];

        return $result;

    }

    private function getNumRegistros(int $idTablet): int
    {
        $qb = $this->_em->createQueryBuilder('s');
        $qb->select('count(s.id)');
        $qb->from('App:Servicio', 's')
            ->where('s.tabletId = :tablet')
            ->setParameter('tablet', $idTablet);


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
            $qb->Andwhere('s.fecha LIKE :busqueda')
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
                $qb->orderBy('s.id',$orden);
                return $qb;
            case '1':
                $qb->orderBy('s.fecha',$orden);
                return $qb;
            case '2':
                $qb->orderBy('s.latitudOrigen',$orden);
                return $qb;
            case '3':
                $qb->orderBy('s.longitudOrigen',$orden);
                return $qb;
            case '4':
                $qb->orderBy('s.latitudDestino',$orden);
                return $qb;
            case '5':
                $qb->orderBy('s.longitudDestino',$orden);
                return $qb;
            case '6':
                $qb->orderBy('s.duracionRuta',$orden);
                return $qb;
            default:
                throw new BadRequestHttpException('Nombre de columna incorrecto');
        }
    }


}