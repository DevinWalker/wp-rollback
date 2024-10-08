<?php

/**
 * UncaughtExceptionLogger
 *
 * This class is responsible for logging logical exceptions
 *
 * @package WpRollback\Core\Exceptions
 * @unreleased
 */

declare(strict_types=1);

namespace WpRollback\Core\Exceptions\Primitives;

use WpRollback\Core\Contracts\LoggableException;
use WpRollback\Core\Exceptions\Traits\Loggable;

/**
 * Class LogicException
 *
 * @unreleased
 */
class LogicException extends \LogicException implements LoggableException
{
    use Loggable;
}
