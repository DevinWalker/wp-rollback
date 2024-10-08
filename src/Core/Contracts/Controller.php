<?php

/**
 * Controller Contract.
 *
 * This file provides contract for controller.
 *
 * @unreleased
 */

declare(strict_types=1);

namespace WpRollback\Core\Contracts;

use WpRollback\Core\Request;

/**
 * Class Controller.
 *
 * @unreleased
 */
abstract class Controller
{
    /**
     * @unreleased
     */
    protected Request $request;

    /**
     * Class constructor.
     *
     * @unreleased
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * This function returns the sanitize  data from the request.
     *
     * @unreleased
     */
    protected function getRequestData(): ?array
    {
        return $this->request->sanitize($this->request->all());
    }
}
