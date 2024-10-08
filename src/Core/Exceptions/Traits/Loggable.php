<?php

/**
 * Loggable Traits
 *
 * This class is responsible for providing loggable traits to exceptions.
 *
 * @package WpRollback\Core\Exceptions\Primitives
 * @unreleased
 */

declare(strict_types=1);

namespace WpRollback\Core\Exceptions\Traits;

trait Loggable
{
    /**
     * Gets the Exception::getMessage() method
     *
     * @unreleased
     */
    abstract public function getMessage();

    /**
     * Returns the human-readable log message
     *
     * @unreleased
     */
    public function getLogMessage(): string
    {
        return $this->getMessage();
    }

    /**
     * Returns an array with the basic context details
     *
     * @unreleased
     *
     * @return array
     */
    public function getLogContext(): array
    {
        return [
            'category'  => 'Uncaught Exception',
            'exception' => [
                'File'    => basename($this->getFile()),
                'Line'    => $this->getLine(),
                'Message' => $this->getMessage(),
                'Code'    => $this->getCode(),
            ],
        ];
    }
}
