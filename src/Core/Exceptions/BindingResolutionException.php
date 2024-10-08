<?php

/**
 * BindingResolutionException
 *
 * This class is responsible for handling binding resolution exceptions.
 *
 * @package WpRollback\Core\Exceptions
 * @unreleased
 */

declare(strict_types=1);

namespace WpRollback\Core\Exceptions;

use Exception;
use WpRollback\Core\Contracts\LoggableException;
use WpRollback\Core\Exceptions\Traits\Loggable;

/**
 * Class BindingResolutionException.
 *
 * @unreleased
 */
class BindingResolutionException extends Exception implements LoggableException
{
    use Loggable;
}
