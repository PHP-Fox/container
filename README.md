# PHP-Fox Container

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

$container->add(
    id: Abstract::class,
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
    id: Abstract::class,
);
```
