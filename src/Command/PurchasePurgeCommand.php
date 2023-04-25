<?php

namespace App\Command;

use App\Entity\Purchase;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'purchase:purge',
)]
class PurchasePurgeCommand extends BaseCommand
{
    protected function executeCommand(InputInterface $input, SymfonyStyle $io): void
    {
        $processor = $this->em
            ->getRepository(Purchase::class)
            ->between(to: new \DateTimeImmutable('-90 days'))
            ->batchProcess()
        ;
        $count = 0;

        foreach ($io->progressIterate($processor) as $purchase) {
            /** @var Purchase $purchase */
            $this->em->remove($purchase);
            ++$count;
        }

        $io->success(sprintf('Purged %d purchases', $count));
    }
}
