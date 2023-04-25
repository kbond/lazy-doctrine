<?php

namespace App\Command;

use App\Doctrine\QueryCounter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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

    final protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $start = \time();

        $this->executeCommand($input, $io);

        $io->comment(sprintf("<info>Time:</info> %s, <info>Queries:</info> %s", Helper::formatTime(\time() - $start), count($this->queryCounter)));

        return self::SUCCESS;
    }

    abstract protected function executeCommand(InputInterface $input, SymfonyStyle $io): void;
}
