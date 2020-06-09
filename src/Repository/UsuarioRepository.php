<?php


namespace App\Repository;


use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UsuarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Usuario::class);
    }

    public function findListado(array $data): array
    {
        if (!isset($data['search']) || !isset($data['search']['value']) || !isset($data['columns'])
            || !isset($data['length']) || !isset($data['start']) || !isset($data['order']))
            throw new BadRequestHttpException("Parámetros incorrectos en el datatable");

        $qb = $this->_em->createQueryBuilder();

            $qb->select('u.id')
                ->addSelect('u.username')
                ->addSelect('u.activado')
                ->from('App:Usuario', 'u')
                ->where('u.role = :rol')
                ->setParameter('rol', 'ROLE_USER');

        $this->addFiltros($qb,$data);

        if ($data['length'] !== -1) {
            $qb->setFirstResult($data['start'])
                ->setMaxResults($data['length']);
        }

        $servicios = $qb->getQuery()->execute();

        $result['data'] = $servicios;


        $result['recordsTotal'] = $this->getNumRegistros();
        $result['recordsFiltered'] = $this->getNumRegistros();
        $result['draw'] = (int)$data['draw'];

        return $result;

    }

    private function getNumRegistros(): int
    {
        $qb = $this->_em->createQueryBuilder('u');
        $qb->select('count(u.id)');
        $qb->from('App:Usuario', 'u')
            ->where('u.role = :rol')
            ->setParameter('rol', 'ROLE_USER');

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
            $qb->Andwhere('u.username LIKE :busqueda')
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
                $qb->orderBy('u.id',$orden);
                return $qb;
            case '1':
                $qb->orderBy('u.username',$orden);
                return $qb;
            case '2':
                $qb->orderBy('u.activado',$orden);
                return $qb;
            default:
                throw new BadRequestHttpException('Nombre de columna incorrecto');
        }
    }
}