<?php

namespace App\Repository;

use App\Entity\Motto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Motto|null find($id, $lockMode = null, $lockVersion = null)
 * @method Motto|null findOneBy(array $criteria, array $orderBy = null)
 * @method Motto[]    findAll()
 * @method Motto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MottoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Motto::class);
    }
    public function findMotto($id)
    {
        $qb = $this->createQueryBuilder('m');
        $qb->select('m.motto')
            ->where('m.player = :id')
            ->setMaxResults('1')
            ->setParameter('id', $id);
        return $qb->getQuery()->getOneOrNullResult();
    }

    // /**
    //  * @return Motto[] Returns an array of Motto objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Motto
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
