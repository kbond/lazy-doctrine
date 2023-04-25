<?php

namespace App\Doctrine;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class BatchCountableProcessor extends BatchProcessor implements \Countable
{
    public function count(): int
    {
        if (!\is_countable($this->items)) {
            throw new \LogicException('The items must be countable.');
        }

        return \count($this->items);
    }
}
