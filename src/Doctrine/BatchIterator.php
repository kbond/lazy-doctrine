<?php

namespace App\Doctrine;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class BatchIterator implements \IteratorAggregate
{
    public function __construct(
        protected readonly iterable $items,
        private readonly EntityManagerInterface $em,
        private readonly int $batchSize = 100,
    ) {
    }

    final public function getIterator(): \Traversable
    {
        $iteration = 0;

        foreach ($this->items as $key => $value) {
            yield $key => $value;

            if (++$iteration % $this->batchSize) {
                continue;
            }

            $this->em->clear();
        }

        $this->em->clear();
    }
}
