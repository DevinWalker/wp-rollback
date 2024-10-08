<?php

/**
 * Exception
 *
 * This class is responsible for handling exceptions.
 *
 * @package WpRollback\Core\Exceptions\Primitives
 * @unreleased
 */

declare(strict_types=1);

namespace WpRollback\Core\Exceptions\Primitives;

use WpRollback\Core\Contracts\LoggableException;
use WpRollback\Core\Exceptions\Traits\Loggable;

/**
 * Class Exception
 *
 * @unreleased
 */
class Exception extends \Exception implements LoggableException
{
    use Loggable;
}
