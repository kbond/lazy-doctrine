<?php

namespace App\Command;

use App\Doctrine\BatchProcessor;
use App\Entity\Purchase;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Demonstrate "batch deleting".
 */
#[AsCommand(
    name: 'purchase:purge',
)]
class PurchasePurgeCommand extends BaseCommand
{
    protected function executeCommand(InputInterface $input, SymfonyStyle $io): void
    {
        $query = $this->em->getRepository(Purchase::class)
            ->createQueryBuilder('p')
            ->where('p.date <= :date')
            ->setParameter('date', new \DateTime('-90 days'))
            ->getQuery();
        ;

        $processor = new BatchProcessor($query->toIterable(), $this->em);

        foreach ($io->progressIterate($processor) as $purchase) {
            /** @var Purchase $purchase */
            $this->em->remove($purchase);
        }
    }
}
