---
title: Teaching Doctrine to be Lazy
author: Kevin Bond
headingDivider: [1,2]
---

# Teaching Doctrine to be Lazy

<!--
_footer: 'Image Credit: Sébastien Lavalaye'
_class: lead
-->

![bg right](https://images.unsplash.com/photo-1570314032164-6a08c8fa63d2?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=687&q=80)

_by Kevin Bond_

![Symfony Online](slides/sfonlinejune2023.svg)

# Me?

![bg left](slides/kids.jpg)

- From Ontario, Canada :canada:
- Husband, father of three
- Symfony user since `1.0`
- Symfony Core Team
- Obsessed with clean, beautiful APIs
- `@kbond` on GitHub/Slack
- `@zenstruck` on Twitter

# zenstruck?

* A GitHub organization where my open source packages live
  - `zenstruck/foundry`
  - `zenstruck/browser`
  - `zenstruck/messenger-test`
  - `zenstruck/filesystem` :eyes: _(wip)_
  - `zenstruck/schedule-bundle` _(for <6.3)_
  - _..._
* Many now co-maintained by Nicolas PHILIPPE (`@nikophil`)

# What we'll cover

<!--
header: Teaching Doctrine to be Lazy
footer: 'Kevin Bond &#x2022; _@zenstruck_ &#x2022; _github.com/kbond_'
paginate: true
-->

* Hydration considerations
* Lazy batch iterating (readonly)
* Lazy batch processing
  - Updating/Deleting/Persisting
* Lazy relationships
* Alternate `ObjectRepository`
* Future ideas

# Sample App

```
                    +----------+       +------------+
                    | PRODUCT  |       | PURCHASE   |
                    |----------|       |------------|
                    | id       |---+   | id         |
                    | sku      |   +--<| product_id |
                    | stock    |       | date       |
                    | category |       | amount     |
                    +----------+       +------------+
```
- 1,000+ products, 100,000+ purchases

# Mongo, Something Else?

* With some tweaks, the demonstrated techniques should/could apply to any `doctrine/persistence` implementation.
* I'm using `doctrine/orm` for the examples in this talk.

# Part 1: Hydration Considerations

* Hydration is expensive
* Some _rules_
  * _Only hydrate what you need_ :white_check_mark:
  * _Only hydrate when you need it_ :white_check_mark:
  * _Cleanup after yourself_ :white_check_mark:

## Profiling Hydrations

<!--
header: Teaching Doctrine to be Lazy - Part 1: Hydration Considerations
-->

* Web Profiler?
  * ![query profiler](slides/profiler-query.png)
  * [`debesha/doctrine-hydration-profiler-bundle`](https://github.com/debesha/DoctrineProfileExtraBundle)
  * DoctrineBundle?
    * Needs a hook in [`doctrine/orm`](https://github.com/doctrine/orm/pull/9545)
    * ![hydration profiler](slides/profiler-hydrations.png)
* Blackfire.io `metrics.doctrine.entities.hydrated`

# Part 2: Batch Iterating

<!--
header: Teaching Doctrine to be Lazy
-->

* _Read-only_
* Use SQL?
* `purchase:report` command
  - Generates a report for all purchases

## `$repo->findAll()`

<!--
header: Teaching Doctrine to be Lazy - Part 2: Batch Iterating
-->

```
 100000/100000 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%  1 sec/1 sec  166.0 MiB

 // Time: 2 secs, Queries: 1
```

* _Only hydrate what you need_ :white_check_mark:
* _Only hydrate when you need it_ :x:
* _Cleanup after yourself_ :x:

## `$repo->matching(new Criteria())`

```
  100000/100000 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%  1 sec/1 sec  168.0 MiB

 // Time: 1 sec, Queries: 2
```

* _Only hydrate what you need_ :white_check_mark:
* _Only hydrate when you need it_ :x:
* _Cleanup after yourself_ :x:

## `Doctrine\ORM\Query::toIterable()`

```
 100000 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 2 secs 166.0 MiB

 // Time: 2 secs, Queries: 1
```

* _Only hydrate what you need_ :white_check_mark:
* _Only hydrate when you need it_ :white_check_mark:
* _Cleanup after yourself_ :x:

## `Doctrine\ORM\Tools\Pagination\Paginator`

```
 100000/100000 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%  1 sec/1 sec  168.0 MiB

 // Time: 1 sec, Queries: 2
```

* _Only hydrate what you need_ :white_check_mark:
* _Only hydrate when you need it_ :white_check_mark:
* _Cleanup after yourself_ :x:

## Batch Utilities - Iterator

* [`ocramius/doctrine-batch-utils`](https://github.com/Ocramius/DoctrineBatchUtils)
  * Takes an ORM Query object and iterates over the result set in batches
  * _Clear_ the `ObjectManager` after each _batch_ to free memory
* Enhanced:
  * Accepts _any_ `iterable` and _any_ `ObjectManager` instance
  * _Countable_ version

## `BatchIterator`

```php
final public function getIterator(): \Traversable
{
    $iteration = 0;
    foreach ($this->items as $key => $value) {
        yield $key => $value;

        if (++$iteration % $this->batchSize) {
            continue;
        }
        $this->em->clear();
    }
    $this->em->clear();
}
```

## Use `BatchIterator`

```php
$iterator = new BatchIterator($query->toIterable(), $this->em);
```

```
 100000 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100% 2 secs 20.0 MiB

 // Time: 2 secs, Queries: 1
```

* _Only hydrate what you need_ :white_check_mark:
* _Only hydrate when you need it_ :white_check_mark:
* _Cleanup after yourself_ :white_check_mark:

## Memory Stays Constant, Time Increases

200,000 purchases?

```
 200000 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100% 4 secs 20.0 MiB

 // Time: 4 secs, Queries: 1
```

1,000,000 purchases?

```
 1000000 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 19 secs 22.0 MiB

 // Time: 19 secs, Queries: 1
```

## 1,000,000 Purchases Using `$repo->findAll()`?

```
 1000000/1000000 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100% 2 secs/2 secs 1.5 GiB

 // Time: 16 secs, Queries: 1
```

1.5 GiB of memory? :scream:

# Part 3: Batch Processing

<!--
_class: title-slide
header: Teaching Doctrine to be Lazy
-->

## Batch Updating

<!--
header: Teaching Doctrine to be Lazy - Part 3: Batch Processing (Update)
-->

* `product:stock-update` Command
  * Loop through all products
  * Update stock level from a source (ie. CSV files, API, etc)

## `$repo->findAll()`

```php
foreach ($repo->findAll() as $product) {
    /** @var Product $product */
    $product->setStock($this->currentStockFor($product));
    $this->em->flush();
}
```

## `$repo->findAll()`

```php
foreach ($repo->findAll() as $product) {
    /** @var Product $product */
    $product->setStock($this->currentStockFor($product));
    $this->em->flush();
}
```

```
 1000/1000 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100% 8 secs/8 secs 16.0 MiB

 // Time: 8 secs, Queries: 988
```

## `$repo->findAll()`, Delay _Flush_

```php
foreach ($repo->findAll() as $product) {
    /** @var Product $product */
    $product->setStock($this->currentStockFor($product));
}
$this->em->flush();
```

## `$repo->findAll()`, Delay _Flush_

```php
foreach ($repo->findAll() as $product) {
    /** @var Product $product */
    $product->setStock($this->currentStockFor($product));
}
$this->em->flush();
```

```
 1000/1000 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100% < 1 sec/< 1 sec 16.0 MiB

 // Time: < 1 sec, Queries: 2
```

## Batch Utilities - Processor

* [`ocramius/doctrine-batch-utils`](https://github.com/Ocramius/DoctrineBatchUtils)
    * Takes an ORM Query object and iterates over the result set in batches
    * _Flush_ **and** _clear_ the `ObjectManager` after each _batch_ to free memory and save changes
    * Wrap everything in a transaction
* Enhanced:
    * Accepts _any_ `iterable` and _any_ `ObjectManager` instance
    * _Countable_ version

## Using `BatchProcessor`

```php
$processor = new BatchProcessor($query->toIterable(), $this->em);

foreach ($processor as $product) {
    /** @var Product $product */
    $product->setStock($this->currentStockFor($product));
}
// no need for "flush"
```

```
 1000 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] < 1 sec 16.0 MiB

 // Time: < 1 sec, Queries: 1
```

## Using `BatchProcessor` - 10,000 Products

```
 10000 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓]  1 sec 18.0 MiB

 // Time: 1 sec, Queries: 1
```

## Batch Deleting

<!--
header: Teaching Doctrine to be Lazy - Part 3: Batch Processing (Delete)
-->

* DQL `DELETE` statement?
* `PreRemove`/`PostRemove` events? Audit?
* `purchase:purge` Command
  * Delete all purchases older than 90 days

## Using `BatchProcessor`

```php
$query = $this->createQueryBuilder('p')
    ->where('p.purchasedAt <= :date')
    ->setParameter('date', new \DateTime('-90 days'))
    ->getQuery();
;

$processor = new BatchProcessor($query->toIterable(), $this->em);

foreach ($processor as $purchase) {
    /** @var Purchase $purchase */
    $this->em->remove($purchase); // no need for "flush"
}
```

## Using `BatchProcessor`

```
 75237 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100% 9 secs 18.0 MiB

 // Time: 9 secs, Queries: 1
```

## Using `BatchProcessor` - 1,000,000 Purchases

```
 753854 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%  1 min  18.0 MiB

 // Time: 1 min, Queries: 1
```

## Batch Persisting

<!--
header: Teaching Doctrine to be Lazy - Part 3: Batch Processing (Persist)
-->

* `product:import` Command
  * Imports products from a _source_ (ie. CSV files, API, etc)
* :exclamation:_Requires_ enhanced `BatchProcessor`
  * Accepts _any_ iterable
  * We'll use a `Generator` to _yield_ `Product` instances

## Using `BatchProcessor`

```php
$processor = new BatchProcessor(
    $this->products(), // Product[] - our "source"
    $this->em,
);

foreach ($processor as $product) {
    /** @var Product $product */
    $this->em->persist($product); // no need for "flush"
}
```

## Using `BatchProcessor` - Import 1,000

```
 1000 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] < 1 sec 16.0 MiB

 // Time: < 1 sec, Queries: 1
```

## Using `BatchProcessor` - Import 100,000

```
 100000 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 12 secs 16.0 MiB

 // Time: 12 secs, Queries: 1
```

# Part 4: Lazy Relationships

<!--
_class: title-slide
header: Teaching Doctrine to be Lazy
-->

## todo

<!--
header: Teaching Doctrine to be Lazy - Part 4: Lazy Relationships
-->

# Part 5: Alternate `ObjectRepository`

<!--
_class: title-slide
header: Teaching Doctrine to be Lazy
-->

## todo

<!--
header: Teaching Doctrine to be Lazy - Part 5: Alternate ObjectRepository
-->

# Part 6: Future Ideas

<!--
_class: title-slide
header: Teaching Doctrine to be Lazy
-->

## todo

<!--
header: Teaching Doctrine to be Lazy - Part 6: Future Ideas
-->

# Thank You!

<!--
header: Teaching Doctrine to be Lazy
-->

- Sample Code: [https://github.com/kbond/lazy-doctrine](https://github.com/kbond/lazy-doctrine)
- [`zenstruck/collection`](https://github.com/zenstruck/collection)

![Symfony Online w:500](slides/sfonlinejune2023.svg)
