<?php

declare(strict_types=1);

use PHPFox\Container\Container;

use PHPFox\Container\Exceptions\BindingResolutionException;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertNotSame;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

it('must be a singleton', function () {
    $instance1 = Container::getInstance();
    $instance2 = Container::getInstance();

    assertSame(
        expected: $instance1,
        actual: $instance2,
    );
});

it('can be provided a set of instructions for resolving a class', function () {
    $container = Container::getInstance();
    $container->flush();

    /*
     * - Great when a class needs help to be instantiated, such as pulling in config values.
     * - This is an example of "inversion of control" or IoC.
     * - Note: The closure is only called when we "resolve" or "make" the class.
     */
    $container->bind(SmtpMailer::class, function () {
        return new SmtpMailer('mail.example.com');
    });

    /*
     * The logic for creating the instance is now centralised and shared.
     */
    $smtpMailer = $container->make(SmtpMailer::class);
    assertInstanceOf(SmtpMailer::class, $smtpMailer);

    /*
     * Double check that we get a new instance each time
     */
    $anotherSmtpMailer = $container->make(SmtpMailer::class);
    assertNotSame($smtpMailer, $anotherSmtpMailer);
});

it('can also use a string for a key', function () {
    $container = Container::getInstance();
    $container->flush();

    $container->bind('mailer', fn () => new SmtpMailer('mail.example.com'));

    $smtpMailer = $container->make('mailer');

    assertInstanceOf(SmtpMailer::class, $smtpMailer);
});

it('can also bind to an interface', function () {
    // Note: Just because we can use interfaces, doesn't mean we always should!
    $container = Container::getInstance();
    $container->flush();

    $container->bind(MailerInterface::class, fn () => new SmtpMailer('mail.example.com'));

    $smtpMailer = $container->make(MailerInterface::class);

    assertInstanceOf(SmtpMailer::class, $smtpMailer);
});

it('can accept a concrete as a second parameter', function () {
    $container = Container::getInstance();
    $container->flush();

    /*
     * Sometimes we don't need to provide instructions for resolving a class
     */
    $container->bind(MailerInterface::class, ArrayMailer::class);

    $smtpMailer = $container->make(MailerInterface::class);

    assertInstanceOf(ArrayMailer::class, $smtpMailer);
});

it('can make a class we have yet to see, zero config resolution', function () {
    $container = Container::getInstance();
    $container->flush();

    $smtpMailer = $container->make(ArrayMailer::class);

    assertInstanceOf(ArrayMailer::class, $smtpMailer);
});

it('can recursively resolve dependencies', function () {
    $container = Container::getInstance();
    $container->flush();

    $container->bind(MailerInterface::class, SmtpMailer::class);
    $container->bind(SmtpMailer::class, fn () => new SmtpMailer('smtp.example.com'));

    $mailer = $container->make(MailerInterface::class);

    assertInstanceOf(SmtpMailer::class, $mailer);
});

it('can also bind a singleton', function () {
    $container = Container::getInstance();
    $container->flush();

    $container->singleton(SmtpMailer::class, fn () => new SmtpMailer('mail.example.com'));

    $smtpMailer1 = $container->make(SmtpMailer::class);
    $smtpMailer2 = $container->make(SmtpMailer::class);

    assertSame($smtpMailer1, $smtpMailer2);
    assertInstanceOf(SmtpMailer::class, $smtpMailer1);
});

it('binds a singleton by passing the instance', function () {
    $container = Container::getInstance();
    $container->flush();

    $instance = new ArrayMailer();
    $container->instance(ArrayMailer::class, $instance);
    $resolved = $container->make(ArrayMailer::class);

    assertSame($instance, $resolved);
});

it('binds a singleton by class name only', function () {
    $container = Container::getInstance();
    $container->flush();

    $container->singleton(ArrayMailer::class);

    $smtpMailer1 = $container->make(ArrayMailer::class);
    $smtpMailer2 = $container->make(ArrayMailer::class);

    assertSame($smtpMailer1, $smtpMailer2);
    assertInstanceOf(ArrayMailer::class, $smtpMailer1);
});

it('does dependency injection', function () {
    $container = Container::getInstance();
    $container->flush();

    $mailer = $container->make(ApiMailer::class);

    assertInstanceOf(ApiMailer::class, $mailer);
});

it('can check if the container contains a binding', function () {
    $container = Container::getInstance();
    $container->flush();

    assertFalse(
        condition: $container->contains(
            abstract: ArrayMailer::class,
        ),
    );

    $container->bind(
        abstract: ArrayMailer::class,
    );

    assertTrue(
        condition: $container->contains(
            abstract: ArrayMailer::class,
        ),
    );
});

it('throws a BindingResolutionException when a target class does not exist', function () {
    $container = Container::getInstance();
    $container->flush();

    $container->make(
        abstract: FakeBinding::class,
    );
})->throws(
    exceptionClass: BindingResolutionException::class
);

it('throws a BindingResolutionException when a target class cannot be instantiated', function () {
    $container = Container::getInstance();
    $container->flush();

    $container->make(
        abstract: MakeBreak::class,
    );

})->throws(
    exceptionClass: BindingResolutionException::class
);

it('implements ArrayAccess properly', function () {
    $container = Container::getInstance();
    $container->flush();

    // Add ArrayMailer to container
    $container[ArrayMailer::class] = ArrayMailer::class;

    // Check it exists
    assertTrue(
        condition: isset($container[ArrayMailer::class]),
    );

    // Check we can get it from the container
    assertInstanceOf(
        expected: ArrayMailer::class,
        actual: $container[ArrayMailer::class],
    );

    // Check we can remove it
    unset($container[ArrayMailer::class]);

    assertFalse(
        condition: isset($container[ArrayMailer::class]),
    );

});

it('implements ContainerInterface for PSR-11 properly', function () {
    $container = Container::getInstance();
    $container->flush();

    $container->bind(
        abstract: ArrayMailer::class,
    );

    assertInstanceOf(
        expected: ArrayMailer::class,
        actual: $container->get(
            id: ArrayMailer::class,
        ),
    );

    assertTrue(
        condition: $container->has(
            id: ArrayMailer::class,
        ),
    );
});


interface MailerInterface
{
    public function send($message);
}

class ArrayMailer implements MailerInterface
{
    public function send($message)
    {
        // ..
    }
}

class SmtpMailer implements MailerInterface
{
    public function __construct(public string $server)
    {
    }

    public function send($message)
    {
        // ...
    }
}

class ApiMailer implements MailerInterface
{
    public function __construct(public Api $api)
    {
    }

    public function send($message)
    {
        // ...
    }
}

class Api
{
}

class MakeBreak
{
    private function __construct() {}

    public static function build(): static
    {
        return new static();
    }
}
