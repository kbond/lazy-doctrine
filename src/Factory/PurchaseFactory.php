<?php

namespace App\Factory;

use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Purchase>
 *
 * @method        Purchase|Proxy create(array|callable $attributes = [])
 * @method static Purchase|Proxy createOne(array $attributes = [])
 * @method static Purchase|Proxy find(object|array|mixed $criteria)
 * @method static Purchase|Proxy findOrCreate(array $attributes)
 * @method static Purchase|Proxy first(string $sortedField = 'id')
 * @method static Purchase|Proxy last(string $sortedField = 'id')
 * @method static Purchase|Proxy random(array $attributes = [])
 * @method static Purchase|Proxy randomOrCreate(array $attributes = [])
 * @method static PurchaseRepository|RepositoryProxy repository()
 * @method static Purchase[]|Proxy[] all()
 * @method static Purchase[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Purchase[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Purchase[]|Proxy[] findBy(array $attributes)
 * @method static Purchase[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Purchase[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class PurchaseFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'amount' => self::faker()->randomFloat(2, 10, 10_000),
            'date' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 year')),
            'product' => ProductFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Purchase $purchase): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Purchase::class;
    }
}
