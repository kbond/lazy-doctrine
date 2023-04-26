---
title: Teaching Doctrine to be Lazy
author: Kevin Bond
---

![bg right](https://images.unsplash.com/photo-1570314032164-6a08c8fa63d2?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=687&q=80)

# Teaching Doctrine to be Lazy

by Kevin Bond

---

<!--
header: Teaching Doctrine to be Lazy
footer: 'Kevin Bond &#x2022; _@zenstruck_ &#x2022; _github.com/kbond_'
paginate: true
-->

# Slide 2

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

---

# Slide 3

- One
- Two
- Three
