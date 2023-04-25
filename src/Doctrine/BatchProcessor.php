<?php

namespace App\Doctrine;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class BatchProcessor implements \IteratorAggregate
{
    public function __construct(
        protected readonly iterable $items,
        private readonly EntityManagerInterface $em,
        private readonly int $batchSize = 100,
    ) {
    }

    public function getIterator(): \Traversable
    {
        $this->em->beginTransaction();
        $iteration = 0;

        try {
            foreach ($this->items as $key => $value) {
                yield $key => $value;

                if (++$iteration % $this->batchSize) {
                    continue;
                }

                $this->em->flush();
                $this->em->clear();
            }
        } catch (\Throwable $e) {
            $this->em->rollback();

            throw $e;
        }

        $this->em->flush();
        $this->em->clear();
        $this->em->commit();
    }
}
