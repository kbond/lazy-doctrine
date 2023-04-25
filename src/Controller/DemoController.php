<?php

namespace App\Controller;

use App\Doctrine\EntityRepository;
use App\Doctrine\EntityRepositoryFactory;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Entity\Purchase\Between;
use Doctrine\ORM\QueryBuilder;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class DemoController
{
    public function demo1(EntityRepositoryFactory $factory)
    {
        $repo = $factory->create(Product::class);

        $product = $repo->find(6);

        $product = $repo->find(['sku' => 'ABC123']);

        $product = $repo->find(function(QueryBuilder $qb, string $root) {
            $qb->where('...');
        });
    }

    public function demo2(EntityRepositoryFactory $factory)
    {
        $repo = $factory->create(Purchase::class);
        $newerThan = new \DateTimeImmutable('-1 year');

        $purchases = $repo->filter(function(QueryBuilder $qb, string $root) use ($newerThan) {
            $qb->where(sprintf('%s.date > :newerThan', $root))
                ->setParameter('newerThan', $newerThan)
            ;
        });

        $purchases = $repo->filter(new Between(from: $newerThan));
    }

    public function future1(Product $product)
    {
        $purchases = $product->getPurchases()->filter(new Between(from: new \DateTimeImmutable('-1 year')));
    }

    public function future2(#[ForClass(Purchase::class)] EntityRepository $purchases)
    {
        $purchases = $purchases->filter(
            Spec::andX(
                new Between(from: new \DateTimeImmutable('-1 year')), // in last year
                new WithProducts( // join with product
                    Spec::equals('category', Category::BOOKS) // filter products by category
                ),
                Spec::sortDesc('date'), // sort by date
            )
        );
    }
}
