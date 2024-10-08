<?php

/**
 * Hooks
 *
 * This class is responsible for managing hooks.
 *
 * @package WpRollback/Core
 * @unreleased
 */

declare(strict_types=1);

namespace WpRollback\Core;

use WpRollback\Core\Exceptions\BindingResolutionException;
use WpRollback\Core\Exceptions\Primitives\InvalidArgumentException;

/**
 * Class Hooks
 *
 * @unreleased
 */
class Hooks
{
    /**
     * A function which extends the WordPress add_action method to handle the instantiation of a class
     * once the action is fired. This prevents the need to instantiate a class before adding it to hook.
     *
     * @unreleased
     *
     * @param string $tag The name of the action to be hooked to the $class.
     * @param string $class The class to be instantiated when the action is fired.
     * @param string $method The method to be called on the $class when the action is fired.
     * @param int $priority The priority at which the $class method should be fired.
     * @param int $acceptedArgs The number of arguments the $class method should accept.
     *
     * @throws InvalidArgumentException
     * @throws BindingResolutionException
     */
    public static function addAction(
        string $tag,
        string $class,
        string $method = '__invoke',
        int $priority = 10,
        int $acceptedArgs = 1
    ): void {
        if (! method_exists($class, $method)) {
            throw new InvalidArgumentException("The method $method does not exist on $class"); // phpcs:ignore
        }

        add_action(
            $tag,
            static function () use ($tag, $class, $method) {
                // Provide a way of disabling the hook
                if (
                    apply_filters("wpr_disable_hook-{$tag}", false) || apply_filters(
                        "wpr_disable_hook-{$tag}:{$class}@{$method}",
                        false
                    )
                ) {
                    return;
                }

                $instance = container($class);

                call_user_func_array([$instance, $method], func_get_args());
            },
            $priority,
            $acceptedArgs
        );
    }

    /**
     * A function which extends the WordPress add_filter method to handle the instantiation of a class
     * once the filter is fired. This prevents the need to instantiate a class before adding it to hook.
     *
     * @unreleased
     *
     * @param string $tag The name of the filter to hook the $class to.
     * @param string $class The class to be instantiated when the filter is fired.
     * @param string $method The method to be called on the $class when the filter is fired.
     * @param int $priority The priority at which the $class method should be fired.
     * @param int $acceptedArgs The number of arguments the $class method should accept.
     *
     *
     * @throws InvalidArgumentException
     * @throws BindingResolutionException
     */
    public static function addFilter(
        string $tag,
        string $class,
        string $method = '__invoke',
        int $priority = 10,
        int $acceptedArgs = 1
    ): void {
        if (! method_exists($class, $method)) {
            throw new InvalidArgumentException("The method $method does not exist on $class"); // phpcs:ignore
        }

        add_filter(
            $tag,
            static function () use ($tag, $class, $method) {
                // Provide a way of disabling the hook
                if (
                    apply_filters("stellarpay_disable_hook-{$tag}", false) || apply_filters(
                        "stellarpay_disable_hook-{$tag}:{$class}@{$method}",
                        false
                    )
                ) {
                    return func_get_arg(0);
                }

                $instance = container($class);

                return call_user_func_array([$instance, $method], func_get_args());
            },
            $priority,
            $acceptedArgs
        );
    }
}
