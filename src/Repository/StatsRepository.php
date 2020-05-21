<?php

namespace App\Repository;

use App\Entity\Stats;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Stats|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stats|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stats[]    findAll()
 * @method Stats[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stats::class);
    }

    public function getBreakdown($id)
    {
        $qb = $this->createQueryBuilder('b');
        $qb->select('b.type, COUNT(b) as count')
            ->where('b.player = :id')
            ->groupBy('b.type')
            ->setParameter('id', $id);
        $results = $qb->getQuery()->getResult();
        return array_column($results, 'count', 'type');
    }
    public function getTopTargets($id)
    {
        $qb = $this->createQueryBuilder('b');
        $qb->select('b.playerHit, COUNT(b) as times')
            ->where('b.player = :id')
            ->groupBy('b.playerHit')
            ->orderBy('times', 'DESC')
            ->setParameter('id', $id);
        return $qb->getQuery()->getResult();
    }
}
