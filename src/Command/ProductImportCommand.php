<?php

namespace App\Command;

use App\Doctrine\BatchProcessor;
use App\Entity\Category;
use App\Entity\Product;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Demonstrate "batch inserting".
 */
#[AsCommand(
    name: 'product:import',
)]
class ProductImportCommand extends BaseCommand
{
    private const INEFFICIENT = 'inefficient';
    private const MORE_EFFICIENT = 'more-efficient';
    private const EFFICIENT = 'efficient';
    private const TYPES = [
        self::INEFFICIENT,
        self::MORE_EFFICIENT,
        self::EFFICIENT,
    ];

    protected function configure(): void
    {
        $this
            ->addArgument('type', null, '', self::INEFFICIENT, self::TYPES)
        ;
    }

    protected function executeCommand(InputInterface $input, SymfonyStyle $io): void
    {
        match($input->getArgument('type')) {
            self::EFFICIENT => $this->efficientBatchProcess($io),
            self::MORE_EFFICIENT => $this->moreEfficientBatchProcess($io),
            default => $this->inefficientBatchProcess($io),
        };
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
        $processor = new BatchProcessor($this->products(), $this->em, 500);

        foreach ($io->progressIterate($processor) as $product) {
            /** @var Product $product */
            $this->em->persist($product);
        }
    }

    private function products(): iterable
    {
        foreach (range(1, 100000) as $i) {
            yield new Product(\random_int(1, \PHP_INT_MAX), Category::random());
        }
    }
}
