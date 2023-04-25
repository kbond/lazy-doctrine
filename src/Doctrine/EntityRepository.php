<?php

namespace App\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\QueryBuilder;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @template T of object
 * @implements \IteratorAggregate<T>
 */
class EntityRepository implements \IteratorAggregate, \Countable
{
    /**
     * @param class-string<T> $class
     */
    public function __construct(private string $class, private EntityManagerInterface $em)
    {
    }

    /**
     * @param mixed|array<string,mixed>|callable(QueryBuilder,string):void $specification
     *
     * @return T|null
     */
    final public function find(mixed $specification): ?object
    {
        $qb = $this->qb('e');

        if (\is_callable($specification)) {
            $specification($qb, 'e');

            return $qb->getQuery()->getOneOrNullResult();
        }

        try {
            return $this->em->find($this->class, $specification);
        } catch (ORMException $e) {
            if (!\is_array($specification) || \array_is_list($specification)) {
                throw $e;
            }

            return $this->em->getRepository($this->class)->findOneBy($specification);
        }
    }

    /**
     * @param array<string,mixed>|callable(QueryBuilder,string):void $specification
     *
     * @return Result<T>
     */
    final public function filter(array|callable $specification): Result
    {
        $qb = $this->qb('e');

        if (\is_callable($specification)) {
            $specification($qb, 'e');

            return $this->result($qb);
        }

        foreach ($specification as $field => $value) {
            $qb->andWhere("e.{$field} = :{$field}")->setParameter($field, $value);
        }

        return $this->result($qb);
    }

    final public function getIterator(): \Traversable
    {
        return $this->result();
    }

    final public function count(): int
    {
        return $this->result()->count();
    }

    final protected function result(?QueryBuilder $qb = null): Result
    {
        return new Result($qb ?? $this->qb());
    }

    final protected function qb(string $root = 'e'): QueryBuilder
    {
        return $this->em->createQueryBuilder()->select($root)->from($this->class, $root);
    }
}
