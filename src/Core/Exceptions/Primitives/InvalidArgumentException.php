<?php

/**
 * UncaughtExceptionLogger
 *
 * This class is responsible for logging uncaught exceptions
 *
 * @package WpRollback\Core\Exceptions
 * @unreleased
 */

declare(strict_types=1);

namespace WpRollback\Core\Exceptions\Primitives;

use WpRollback\Core\Contracts\LoggableException;
use WpRollback\Core\Exceptions\Traits\Loggable;

/**
 * Class InvalidArgumentException
 *
 * @unreleased
 */
class InvalidArgumentException extends \InvalidArgumentException implements LoggableException
{
    use Loggable;
}
