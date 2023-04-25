<?php

namespace App\Doctrine;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class EntityRepositoryFactory
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
    }

    /**
     * @param class-string $class
     */
    public function create(string $class): EntityRepository
    {
        return new EntityRepository($class, $this->registry->getManagerForClass($class));
    }
}
