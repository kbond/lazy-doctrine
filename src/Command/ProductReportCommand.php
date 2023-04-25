<?php

namespace App\Command;

use App\Entity\Product;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'product:report',
)]
class ProductReportCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this
            ->addOption('lazy')
        ;
    }

    protected function executeCommand(InputInterface $input, SymfonyStyle $io): void
    {
        if ($input->getOption('lazy')) {
            $this->lazy($io);
        } else {
            $this->standard($io);
        }
    }

    private function standard(SymfonyStyle $io): void
    {
        $products = $this->em->getRepository(Product::class)->all()->batchIterate();

        foreach ($io->progressIterate($products) as $product) {
            /** @var Product $product */
            /** @var Collection|Selectable $purchases */
            $purchases = $product->getPurchases();

            $sku = $product->getSku();
            $lastPurchase = $purchases->first() ?: null;
            $purchaseCount = $purchases
                ->matching(
                    Criteria::create()->where(Criteria::expr()->gte('date', new \DateTimeImmutable('-30 days')))
                )
                ->count()
            ;
        }
    }

    private function lazy(SymfonyStyle $io): void
    {
        $products = $this->em->getRepository(Product::class)->all()->batchIterate();

        foreach ($io->progressIterate($products) as $product) {
            /** @var Product $product */
            /** @var Collection|Selectable $purchases */
            $purchases = $product->getLazyPurchases();

            $sku = $product->getSku();
            $lastPurchase = $purchases->slice(0, 1)[0] ?? null; // first() initializes all objects
            $purchaseCount = $purchases
                ->matching(
                    Criteria::create()->where(Criteria::expr()->gte('date', new \DateTimeImmutable('-30 days')))
                )
                ->count()
            ;
        }
    }
}
