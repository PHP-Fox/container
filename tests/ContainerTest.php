<?php

declare(strict_types=1);

use PHPFox\Container\Container;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertNotSame;
use function PHPUnit\Framework\assertSame;


it('must be a singleton', function () {
    $instance1 = Container::getInstance();
    $instance2 = Container::getInstance();

    assertSame(
        expected: $instance1,
        actual: $instance2,
    );
});

it('can provide instructions on how to resolve a class', function () {
    $container = Container::getInstance();

    $container->bind(
        abstract: SmtpMailer::class,
        concrete: fn() => new SmtpMailer(
            server: 'mail.example.com',
        ),
    );

    $mailer = $container->make(
        abstract: SmtpMailer::class,
    );

    assertInstanceOf(
        expected: SmtpMailer::class,
        actual: $mailer,
    );

    $another = $container->make(
        abstract: SmtpMailer::class,
    );

    assertNotSame(
        expected: $mailer,
        actual: $another,
    );
});

it('can use a string for a reference', function () {
    $container = Container::getInstance();

    $container->bind(
        abstract: 'mailer',
        concrete: fn() => new SmtpMailer(
            server: 'mail.example.com',
        ),
    );

    $mailer = $container->make(
        abstract: 'mailer',
    );

    assertInstanceOf(
        expected: SmtpMailer::class,
        actual: $mailer,
    );
});

it('can bind a concrete to an interface', function () {
    $container = Container::getInstance();

    $container->bind(
        abstract: MailerInterface::class,
        concrete: fn() => new SmtpMailer(
            server: 'mail.example.com',
        ),
    );

    $mailer = $container->make(
        abstract: MailerInterface::class,
    );

    assertInstanceOf(
        expected: SmtpMailer::class,
        actual: $mailer,
    );
});

it('can pass a concrete class as a second parameter', function () {
    $container = Container::getInstance();

    $container->bind(
        abstract: MailerInterface::class,
        concrete: ArrayMailer::class,
    );

    $mailer = $container->make(
        abstract: MailerInterface::class,
    );

    assertInstanceOf(
        expected: ArrayMailer::class,
        actual: $mailer,
    );
});

it('can make casses we do not know about: zero config resolution', function () {
    $container = Container::getInstance();

    $mailer = $container->make(
        abstract: ArrayMailer::class,
    );

    assertInstanceOf(
        expected: ArrayMailer::class,
        actual: $mailer,
    );
});

it('can recursively resolve dependencies', function () {
    $container = Container::getInstance();

    $container->bind(
        abstract: MailerInterface::class,
        concrete: SmtpMailer::class,
    );

    $container->bind(
        abstract: SmtpMailer::class,
        concrete: fn() => new SmtpMailer(
            server: 'mail.example.com',
        ),
    );

    $mailer = $container->make(
        abstract: MailerInterface::class,
    );

    assertInstanceOf(
        expected: SmtpMailer::class,
        actual: $mailer,
    );
});

it('can bind a singleton', function () {
    $container = Container::getInstance();

    $container->singleton(
        abstract: SmtpMailer::class,
        concrete: fn() => new SmtpMailer(
            server: 'mail.example.com',
        ),
    );

    $mailer1 = $container->make(
        abstract: SmtpMailer::class,
    );
    $mailer2 = $container->make(
        abstract: SmtpMailer::class,
    );

    assertSame(
        expected: $mailer1,
        actual: $mailer2,
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
