<?php

namespace App\Repository;

use App\Doctrine\Result;
use App\Entity\Purchase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Purchase>
 *
 * @method Purchase|null find($id, $lockMode = null, $lockVersion = null)
 * @method Purchase|null findOneBy(array $criteria, array $orderBy = null)
 * @method Purchase[]    findAll()
 * @method Purchase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Purchase::class);
    }

    public function between(?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null): Result
    {
        $qb = $this->createQueryBuilder('p');

        if ($from) {
            $qb->andWhere('p.date >= :from')
                ->setParameter('from', $from)
            ;
        }

        if ($to) {
            $qb->andWhere('p.date <= :to')
                ->setParameter('to', $to)
            ;
        }

        return new Result($qb);
    }
}
