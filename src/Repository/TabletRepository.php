<?php


namespace App\Repository;


use App\Entity\Tablet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @method Tablet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tablet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tablet[]    findAll()
 * @method Tablet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TabletRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tablet::class);
    }

    public function findCreatorWithOneSignalID(string $oneSignalID)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('usuario.id')->from('App:Tablet', 'tablet')
            ->innerJoin('tablet.idUsuario', 'usuario')
            ->where($qb->expr()->like('tablet.idOneSignal', ':osid'))
            ->setParameter('osid', '%' . $oneSignalID . '%');

        return $qb->getQuery()->getResult();
    }

    /*
     * Recibe la id de one signal y  devuelve los anuncios de la tablet que todavía no se han descargado.
    */
    public function findByOneSignalIdTablet(string $oneSignalID)
    {

        $conn = $this->getEntityManager()
            ->getConnection();
        $qb = 'SELECT A.id,A.video FROM anuncio A ' .
            'INNER JOIN tablet_anuncio AS TA ON TA.anuncio = A.id ' .
            'INNER JOIN tablet AS T ON T.id = TA.tablet ' .
            'WHERE T.id_one_signal = :idOneSignal ';

        $stmt = $conn->prepare($qb);
        $stmt->execute(['idOneSignal' => $oneSignalID]);
        return $stmt->fetchAll();
    }

    public function findListado(array $data, bool $admin, int $idUsuario = null): array
    {
        if (!isset($data['search']) || !isset($data['search']['value']) || !isset($data['columns']) ||
            !isset($data['length']) || !isset($data['start']) || !isset($data['order']))
            throw new BadRequestHttpException("Parámetros incorrectos en el datatable");

        $qb = $this->_em->createQueryBuilder();
        if ($admin) {
            $qb->select('t.id')
                ->addSelect('t.matricula')
                ->addSelect('t.idOneSignal')
                ->from('App:Tablet', 't');
        } else {
            $qb->select('t.id')
                ->addSelect('t.matricula')
                ->addSelect('t.idOneSignal')
                ->from('App:Tablet', 't')
                ->where('t.idUsuario = :idUsuario')
                ->setParameter('idUsuario', $idUsuario);
        }

        $this->addFiltros($qb, $data);

        if ($data['length'] !== -1) {
            $qb->setFirstResult($data['start'])
                ->setMaxResults($data['length']);
        }

        $anuncios = $qb->getQuery()->execute();

        $result['data'] = $anuncios;

        $result['recordsTotal'] = $this->getNumRegistros($admin, $idUsuario);
        $result['recordsFiltered'] = $this->getNumRegistros($admin, $idUsuario);
        $result['draw'] = (int)$data['draw'];

        return $result;
    }

    private function getNumRegistros(bool $admin, int $idUsuario = null): int
    {
        $qb = $this->_em->createQueryBuilder('a');
        if ($admin) {
            $qb->select('count(t.id)');
            $qb->from('App:Tablet', 't');
        } else {
            $qb->select('count(t.id)');
            $qb->from('App:Tablet', 't')
                ->where('t.idUsuario = :idUsuario')
                ->setParameter('idUsuario', $idUsuario);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    private function addFiltros(QueryBuilder $qb, array $data)
    {
        $this->filtroBusqueda($qb, $data);
        $this->FiltroOrder($qb, $data);
    }

    private function filtroBusqueda(QueryBuilder $qb, array $data)
    {
        $busqueda = $data['search']['value'];
        if (!empty($busqueda)) {
            $qb->Andwhere('t.matricula LIKE :busqueda')
                ->orWhere('t.idOneSignal LIKE :busqueda')
                ->setParameter('busqueda', '%' . $busqueda . '%');
            return $qb;
        }
    }

    private function FiltroOrder(QueryBuilder $qb, array $data)
    {
        $orden = 'ASC';
        if ($data['order'][0]['dir'] == 'desc') {
            $orden = 'DESC';
        }

        switch ($data['order'][0]['column']) {
            case '0':
                $qb->orderBy('t.id', $orden);
                return $qb;
            case '1':
                $qb->orderBy('t.matricula', $orden);
                return $qb;
            case '2':
                $qb->orderBy('t.idOneSignal', $orden);
                return $qb;
            default:
                throw new BadRequestHttpException('Nombre de columna incorrecto');
        }
    }

}