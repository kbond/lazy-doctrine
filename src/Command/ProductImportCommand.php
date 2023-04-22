<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Zenstruck\Collection\Doctrine\Batch\BatchProcessor;

#[AsCommand(
    name: 'product:import',
)]
class ProductImportCommand extends BaseCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->efficientBatchProcess($io);

        $io->comment('<info>Queries</info>: '.count($this->queryCounter));

        return Command::SUCCESS;
    }

    private function inefficientBatchProcess(SymfonyStyle $io): void
    {
        foreach ($io->progressIterate($this->products()) as $product) {
            /** @var Product $product */
            $this->em->persist($product);
            $this->em->flush();
        }
    }

    private function moreEfficientBatchProcess(SymfonyStyle $io): void
    {
        foreach ($io->progressIterate($this->products()) as $product) {
            /** @var Product $product */
            $this->em->persist($product);
        }

        $this->em->flush();
    }

    private function efficientBatchProcess(SymfonyStyle $io): void
    {
        $processor = BatchProcessor::for($this->products(), $this->em, 500);

        foreach ($io->progressIterate($processor) as $product) {
            /** @var Product $product */
            $this->em->persist($product);
        }
    }

    private function products(): iterable
    {
        foreach (range(1, 500) as $i) {
            yield new Product(\random_int(1, 1000000000), Category::random());
        }
    }
}
