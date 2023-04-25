<?php

namespace App\Entity\Purchase;

use App\Doctrine\Result;
use Doctrine\ORM\QueryBuilder;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Between
{
    public function __construct(private ?\DateTimeInterface $from = null, private ?\DateTimeInterface $to = null)
    {
    }

    public function __invoke(QueryBuilder $qb, string $root): void
    {
        if ($this->from) {
            $qb->andWhere('p.date >= :from')
                ->setParameter('from', $this->from)
            ;
        }

        if ($this->to) {
            $qb->andWhere('p.date <= :to')
                ->setParameter('to', $this->to)
            ;
        }
    }
}
