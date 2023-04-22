<?php

namespace App\Command;

use App\Doctrine\QueryCounter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class BaseCommand extends Command
{
    protected QueryCounter $queryCounter;

    public function __construct(protected EntityManagerInterface $em)
    {
        parent::__construct();

        $this->queryCounter = new QueryCounter();
        $this->em->getConnection()->getConfiguration()->setSQLLogger($this->queryCounter);
    }
}
