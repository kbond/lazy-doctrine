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

# Me?

![bg left](slides/kids.jpg)

- From Ontario, Canada :canada:
- Husband, father of three

# Me?

![bg left](slides/pool.jpg)

- Symfony user since `1.0`
- Symfony Core Team
- Obsessed with clean, beautiful APIs
- `@kbond` on GitHub/Slack
- `@zenstruck` on Twitter
- `@zenstruck` org on GitHub

# zenstruck?

- A GitHub organization where my open source packages live
  - `zenstruck/foundry`
  - `zenstruck/browser`
  - `zenstruck/messenger-test`
  - `zenstruck/filesystem` :eyes: _(wip)_
  - `zenstruck/schedule-bundle` _(for <6.3)_
  - _..._
- Many now co-maintained by Nicolas PHILIPPE (`@nikophil`)

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

- Schema:
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
- 1,000 products, 100,000 purchases

# Mongo?

<!--
_class: title-slide
-->

<!--
With some tweaks, this should/could apply to any `doctrine/persistence` implementation.
-->

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

* Blackfire.io `metrics.doctrine.entities.hydrated`
* Web Profiler?
  * ![query profiler](slides/profiler-query.png)
  * [`debesha/doctrine-hydration-profiler-bundle`](https://github.com/debesha/DoctrineProfileExtraBundle)
  * DoctrineBundle?
    * Needs a hook in [`doctrine/orm`](https://github.com/doctrine/orm/pull/9545)
    * ![hydration profiler](slides/profiler-hydrations.png)

# Part 2: Batch Iterating

<!--
header: Teaching Doctrine to be Lazy
-->

* _Read-only_
* Use SQL?
* `purchase:report` command
  - Generates a report for all purchases

## `ObjectRepository` (`EntityRepository`)

<!--
header: Teaching Doctrine to be Lazy - Part 2: Batch Iterating
-->

```php
interface ObjectRepository
{
    // ...

    public function findAll(): array;

    public function findBy(array $criteria, ...): array;
}
```

## `ObjectRepository` (`EntityRepository`)

```php
interface ObjectRepository
{
    // ...

    public function findAll(): iterable;

    public function findBy(array $criteria, ...): iterable;
}
```

## `$repo->findAll()`

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

# Part 3: Batch Processing

<!--
_class: title-slide
header: Teaching Doctrine to be Lazy
-->

## Batch Updating

<!--
header: Teaching Doctrine to be Lazy - Part 3: Batch Processing
-->

## Batch Deleting

## Batch Persisting

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
