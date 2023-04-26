---
title: Teaching Doctrine to be Lazy
author: Kevin Bond
---

<!--
_footer: 'Image Credit: Sébastien Lavalaye'
_class: lead
-->

![bg right](https://images.unsplash.com/photo-1570314032164-6a08c8fa63d2?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=687&q=80)

# Teaching Doctrine to be Lazy

_by Kevin Bond_

---

![bg left](slides/kids.jpg)

# Me?

- From Ontario, Canada
- Husband, father of three
- Symfony user since `1.0`
- Symfony Core Team
- `@kbond` on GitHub/Slack
- `@zenstruck` on Twitter
- `@zenstruck` org on GitHub

---

# zenstruck?

- A GitHub organization where my open source packages live
  - `zenstruck/foundry`
  - `zenstruck/browser`
  - `zenstruck/messenger-test`
  - `zenstruck/filesystem` :eyes: _(wip)_
  - `zenstruck/schedule-bundle` _(for <6.3)_
  - _..._
- Many now co-maintained by Nicolas PHILIPPE (`@nikophil`)

---

<!--
header: Teaching Doctrine to be Lazy
footer: 'Kevin Bond &#x2022; _@zenstruck_ &#x2022; _github.com/kbond_'
paginate: true
-->

# Some Code

```php
#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private string $sku;
}
```
