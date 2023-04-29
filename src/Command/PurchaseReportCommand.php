<?php

namespace App\Command;

use App\Entity\Purchase;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Demonstrate "batch iterating".
 */
#[AsCommand(
    name: 'purchase:report',
)]
class PurchaseReportCommand extends BaseCommand
{
    private const ALL = 'all';
    private const SELECT = 'select';
    private const ITERABLE = 'iterable';
    private const LAZY = 'lazy';
    private const BATCH_PROCESS = 'batch-process';
    private const BATCH_ITERATE = 'batch-iterate';
    private const PAGINATE = 'paginate';
    private const TYPES = [
        self::ALL,
        self::SELECT,
        self::ITERABLE,
        self::PAGINATE,
        self::LAZY,
        self::BATCH_PROCESS,
        self::BATCH_ITERATE,
    ];

    protected function configure(): void
    {
        $this
            ->addArgument('type', null, '', self::ALL, self::TYPES)
        ;
    }

    protected function executeCommand(InputInterface $input, SymfonyStyle $io): void
    {
        foreach ($io->progressIterate($this->iterator($input->getArgument('type'))) as $purchase) {
            /** @var Purchase $purchase */
            // process
        }
    }

    private function iterator(string $type): iterable
    {
        $repo = $this->em->getRepository(Purchase::class);

        return match($type) {
            self::BATCH_PROCESS => $repo->all()->batchProcess(),
            self::BATCH_ITERATE => $repo->all()->batchIterate(),
            self::LAZY => $repo->all(),
            self::SELECT => $repo->matching(new Criteria()),
            self::ITERABLE => $repo->createQueryBuilder('p')->getQuery()->toIterable(),
            self::PAGINATE => new Paginator($repo->createQueryBuilder('p')->getQuery()),
            default => $repo->findAll(),
        };
    }
}
