# PHP-Fox Container

<!-- BADGES_START -->
![GitHub release (latest by date)](https://img.shields.io/github/v/release/php-fox/container)
![tests](https://github.com/PHP-Fox/container/workflows/run-tests/badge.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/phpfox/container.svg?style=flat-square)](https://packagist.org/packages/phpfox/container)
![GitHub](https://img.shields.io/github/license/php-fox/container)

<!-- BADGES_END -->

This is the repository for the DI Container used in the PHP-Fox framework.


## Installation

You should not need to install this package when using the PHP-Fox framework, however if you wish to use this outside of the framework please use:

```bash
composer require phpfox/container
```

## Usage

To use the container, all you need to do is:

```php
$container = Container::getInstance();

$container->bind(
    abstract: Abstract::class,
    concrete: Concrete::class,
    shared: false, // defaults to false - true turns this into a singleton.
);

/**
 * @var bool
 */
$exists = $container->has(
    id: Abstract::class,
);

/**
 * @var Concrete
 */
$concrete = $container->make(
    abstract: Abstract::class,
);
```

Container implementation inspired by [example repo](https://github.com/jessarcher/service-container-from-scratch) from [Jess Archer](https://github.com/jessarcher), which provides a great and simple base. 
