<?php

namespace App\Command;

use App\Entity\Purchase;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'purchase:report',
)]
class PurchaseReportCommand extends BaseCommand
{
    private const ALL = 'all';
    private const SELECT = 'select';
    private const BATCH_PROCESS = 'batch-process';
    private const BATCH_ITERATE = 'batch-iterate';
    private const COUNTABLE_BATCH_PROCESS = 'countable-batch-process';
    private const COUNTABLE_BATCH_ITERATE = 'countable-batch-iterate';
    private const TYPES = [
        self::ALL,
        self::SELECT,
        self::BATCH_PROCESS,
        self::BATCH_ITERATE,
        self::COUNTABLE_BATCH_PROCESS,
        self::COUNTABLE_BATCH_ITERATE,
    ];

    protected function configure(): void
    {
        $this
            ->addArgument('type', null, '', self::ALL, self::TYPES)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($io->progressIterate($this->iterator($input->getArgument('type'))) as $purchase) {
            /** @var Purchase $purchase */
            // process
        }

        $io->comment('<info>Queries</info>: '.count($this->queryCounter));

        return self::SUCCESS;
    }

    private function iterator(string $type): iterable
    {
        $repo = $this->em->getRepository(Purchase::class);

        return match($type) {
            self::COUNTABLE_BATCH_PROCESS => $repo->countableBatchProcessor(),
            self::COUNTABLE_BATCH_ITERATE => $repo->countableBatchIterator(),
            self::BATCH_PROCESS => $repo->batchProcessor(),
            self::BATCH_ITERATE => $repo->batchIterator(),
            self::SELECT => $repo->matching(new Criteria()),
            default => $repo->findAll(),
        };
    }
}
