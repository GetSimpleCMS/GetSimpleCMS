# Rector Rules for Doctrine

See available [Doctrine rules](/docs/rector_rules_overview.md)

## Install

This package is already part of [rector/rector](http://github.com/rectorphp/rector) package, so it works out of the box.

All you need to do is install the main package, and you're good to go:

```bash
composer require rector/rector --dev
```

## Use Sets

To add a set to your config, use `Rector\Doctrine\Set\DoctrineSetList` class and pick one of constants:

```php
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        DoctrineSetList::DOCTRINE_CODE_QUALITY,
    ]);
};
```

<br>

## Learn Rector Faster

Rector is a tool that [we develop](https://getrector.org/) and share for free, so anyone can save hundreds of hours on refactoring. But not everyone has time to understand Rector and AST complexity. You have 2 ways to speed this process up:

* read a book - <a href="https://leanpub.com/rector-the-power-of-automated-refactoring">The Power of Automated Refactoring</a>
* hire our experienced team to <a href="https://getrector.org/contact">improve your code base</a>

Both ways support us to and improve Rector in sustainable way by learning from practical projects.
