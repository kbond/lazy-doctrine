<?php

namespace App\Command;

use App\Entity\Product;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Demonstrate "batch updating".
 */
#[AsCommand(
    name: 'product:stock-update',
)]
class ProductStockUpdateCommand extends BaseCommand
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
        foreach ($io->progressIterate($this->em->getRepository(Product::class)->findAll()) as $product) {
            /** @var Product $product */
            $product->setStock($this->currentStockFor($product));
            $this->em->flush();
        }
    }

    private function moreEfficientBatchProcess(SymfonyStyle $io): void
    {
        foreach ($io->progressIterate($this->em->getRepository(Product::class)->findAll()) as $product) {
            /** @var Product $product */
            $product->setStock($this->currentStockFor($product));
        }

        $this->em->flush();
    }

    private function efficientBatchProcess(SymfonyStyle $io): void
    {
        $processor = $this->em->getRepository(Product::class)->all()->batchProcess();

        foreach ($io->progressIterate($processor) as $product) {
            /** @var Product $product */
            $product->setStock($this->currentStockFor($product));
        }
    }

    private function currentStockFor(Product $product): int
    {
        return \random_int(0, 100);
    }
}
