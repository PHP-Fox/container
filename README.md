# PHP-Fox Container

<!-- BADGES_START -->
[![Latest Version][badge-release]][packagist]
[![PHP Version][badge-php]][php]
![tests](https://github.com/PHP-Fox/container/workflows/run-tests/badge.svg)

[badge-release]: https://img.shields.io/packagist/v/phpfox/container.svg?style=flat-square&label=release
[badge-php]: https://img.shields.io/packagist/php-v/phpfox/container.svg?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/phpfox/container.svg?style=flat-square&colorB=mediumvioletred

[packagist]: https://packagist.org/packages/phpfox/container
[php]: https://php.net
[downloads]: https://packagist.org/packages/phpfox/container
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
