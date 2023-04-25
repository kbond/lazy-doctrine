<?php

namespace App\Doctrine;

use Doctrine\DBAL\Logging\SQLLogger;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class QueryCounter implements SQLLogger, \Countable
{
    private int $count = 0;
    private bool $transaction = false;

    public function startQuery($sql, ?array $params = null, ?array $types = null): void
    {
        if ('"START TRANSACTION"' === $sql) {
            $this->transaction = true;
        }

        if ('"COMMIT"' === $sql) {
            $this->transaction = false;
        }
    }

    public function stopQuery(): void
    {
        if (!$this->transaction) {
            ++$this->count;
        }
    }

    public function count(): int
    {
        return $this->count;
    }
}
