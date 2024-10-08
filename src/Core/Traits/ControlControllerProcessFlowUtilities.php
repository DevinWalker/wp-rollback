<?php

/**
 * This trait provides methods to handle the controller process flow.
 *
 * @package WpRollback\Core\Traits
 * @unreleased
 */

declare(strict_types=1);

namespace WpRollback\Core\Traits;

/**
 * Trait HandlesProcessFlow
 *
 * @unreleased
 */
trait ControlControllerProcessFlowUtilities
{
    /**
     * This function is used to exit the process.
     *
     * Callable argument can be used to execute a function before exiting.
     * It provides benefit during mock testing because it can be mocked.
     *
     * @param callable|null $function The function to execute before exit
     *
     * @unreleased
     */
    protected function exit(callable $function = null): void
    {
        $function && $function();
        exit;
    }

    /**
     * This function is used to redirect to a URL.
     *
     * @param string $url The URL to redirect
     * @param bool $safeRedirect Whether to use safe redirect or not
     *
     * @unreleased
     */
    protected function redirectTo(string $url, bool $safeRedirect = true): void
    {
        if ($safeRedirect) {
            wp_safe_redirect($url);
            exit();
        }

        wp_redirect($url); // phpcs:ignore
        exit();
    }
}
