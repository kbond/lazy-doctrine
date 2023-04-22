<?php

namespace App\Doctrine;

use Doctrine\DBAL\Logging\SQLLogger;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class QueryCounter implements SQLLogger, \Countable
{
    private int $count = 0;

    public function startQuery($sql, ?array $params = null, ?array $types = null): void
    {
    }

    public function stopQuery(): void
    {
        ++$this->count;
    }

    public function count(): int
    {
        return $this->count;
    }
}
