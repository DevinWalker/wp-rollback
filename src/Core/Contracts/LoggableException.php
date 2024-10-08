<?php

/**
 * Loggable Exception
 *
 * This file is responsible for providing loggable exception contract.
 *
 * @package WpRollback\Core\Exceptions\Contracts
 * @unreleased
 */

declare(strict_types=1);

namespace WpRollback\Core\Contracts;

interface LoggableException
{
    /**
     * Returns the human-readable message for the log
     *
     * @unreleased
     */
    public function getLogMessage(): string;

    /**
     * Returns an associated array with additional context for the log
     *
     * @unreleased
     */
    public function getLogContext(): array;
}
