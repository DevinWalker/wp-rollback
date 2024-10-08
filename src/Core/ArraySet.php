<?php

/**
 * ArraySet class.
 *
 * This class is responsible to handle complex operations on array.
 *
 * @package StellarPay/Core
 * @unreleased
 */

declare(strict_types=1);

namespace WpRollback\Core;

/**
 * ArraySet class.
 *
 * This class is responsible for handling array related logic.
 *
 * @package StellarPay/Core
 * @unreleased
 */
class ArraySet
{
    /**
     * Get the difference between two arrays.
     *
     * Note:
     * - This method will only compare associative array.
     * - This method will only compare the values of the common keys between the two arrays.
     * - Value should be null, string, array, boolean, or integer. It will not compare objects.
     *
     * @param  array  $array1 The array to compare from.
     * @param  array  $array2 The array to compare against.
     * @param  bool  $returnOriginalOnMismatch If true, the full array value will be returned on value mismatch.
     *                                              Changed value in array otherwise. Default is false.
     *
     * @return array
     */
    public static function diffOnCommonKeys(
        array $array1,
        array $array2,
        bool $returnOriginalOnMismatch = false
    ): array {
        $result = [];

        // Get the keys that exist in both arrays.
        $commonKeys = array_intersect_key($array1, $array2);

        // Check if the value is of a scalar type or null or array.
        $isValidType = static function ($value) {
            return is_null($value) || is_scalar($value) || is_array($value);
        };

        foreach ($commonKeys as $key => $value) {
            // Check if the value is of a scalar type or null
            if (!$isValidType($value)) {
                throw new \InvalidArgumentException("Invalid type for value at key {$key}"); // phpcs:ignore
            }

            if (is_array($value)) {
                $changedValue = self::diffOnCommonKeys(
                    $value,
                    $array2[$key] ?? [], // If the key value is null in second array, default to an empty array.
                    $returnOriginalOnMismatch
                );
                if ($changedValue) {
                    $result[$key] = $returnOriginalOnMismatch ? $value : $changedValue;
                }
            } elseif ($value !== $array2[$key]) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
