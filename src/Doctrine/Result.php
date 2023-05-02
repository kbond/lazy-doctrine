<?php

namespace App\Doctrine;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @template T of object
 * @implements \IteratorAggregate<T>
 */
final class Result implements \IteratorAggregate, \Countable
{
    private Query $query;

    public function __construct(Query|QueryBuilder $query)
    {
        $this->query = $query instanceof QueryBuilder ? $query->getQuery() : $query;
    }

    public function getIterator(): \Traversable
    {
        yield from $this->query->toIterable();
    }

    public function count(): int
    {
        return (new Paginator($this->query))->count();
    }

    public function batchIterate(int $size = 100): CountableBatchIterator
    {
        return new CountableBatchIterator($this, $this->query->getEntityManager(), $size);
    }

    public function batchProcess(int $size = 100): CountableBatchProcessor
    {
        return new CountableBatchProcessor($this, $this->query->getEntityManager(), $size);
    }
}
