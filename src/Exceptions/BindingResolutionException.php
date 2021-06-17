<?php

declare(strict_types=1);

namespace PHPFox\Container\Exceptions;

use Exception;
use Psr\Container\ContainerExceptionInterface;

class BindingResolutionException extends Exception implements ContainerExceptionInterface
{

}
