<?php

namespace App\DataFixtures;

use App\Factory\ProductFactory;
use App\Factory\PurchaseFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $products = ProductFactory::delayFlush(fn() => ProductFactory::createMany(1000));

        foreach (range(1, 50) as $i) {
            PurchaseFactory::delayFlush(
                fn() => PurchaseFactory::createMany(2000, fn() => ['product' => ProductFactory::faker()->randomElement($products)])
            );
        }
    }
}
