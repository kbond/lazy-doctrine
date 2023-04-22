<?php

namespace App\Repository;

use App\Entity\Purchase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineBatchUtils\BatchProcessing\SelectBatchIteratorAggregate;
use DoctrineBatchUtils\BatchProcessing\SimpleBatchIteratorAggregate;
use Zenstruck\Collection\Doctrine\Batch\CountableBatchIterator;
use Zenstruck\Collection\Doctrine\Batch\CountableBatchProcessor;
use Zenstruck\Collection\Doctrine\ORM\Result;

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

    public function batchProcessor(int $chunk = 100): SimpleBatchIteratorAggregate
    {
        return SimpleBatchIteratorAggregate::fromQuery($this->createQueryBuilder('p')->getQuery(), $chunk);
    }

    public function batchIterator(int $chunk = 100): SelectBatchIteratorAggregate
    {
        return SelectBatchIteratorAggregate::fromQuery($this->createQueryBuilder('p')->getQuery(), $chunk);
    }

    public function countableBatchProcessor(int $chunk = 100): CountableBatchProcessor
    {
        return CountableBatchProcessor::for(
            new Result($this->createQueryBuilder('p')),
            $this->_em,
            $chunk,
        );
    }

    public function countableBatchIterator(int $chunk = 100): CountableBatchIterator
    {
        return CountableBatchIterator::for(
            new Result($this->createQueryBuilder('p')),
            $this->_em,
            $chunk,
        );
    }
}
