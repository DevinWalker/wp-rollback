<?php

/**
 * Request
 *
 * This class is used to manage the request data.
 * It also provides methods to retrieve, sanitize, and redirect the request.
 *
 * @package WpRollback\Core
 * @unreleased
 */

declare(strict_types=1);

namespace WpRollback\Core;

use WpRollback\Vendors\StellarWP\SuperGlobals\SuperGlobals;

/**
 * Class Request
 *
 * @unreleased
 */
class Request
{
    /**
     * This function is used to retrieve data from the request.
     *
     * @param string $key The key to retrieve from the request
     * @param mixed $default The default value to return if the key is not found
     *
     * @unreleased
     * @return mixed Sanitized data from the request
     */
    public function get(string $key, $default = null)
    {
        return SuperGlobals::get_get_var($key, $default);
    }

    /**
     * This function is used to retrieve data from the request.
     *
     * @param string $key The key to retrieve from the request
     * @param mixed $default The default value to return if the key is not found
     *
     * @unreleased
     * @return mixed Sanitized data from the request
     */
    public function post(string $key, $default = null)
    {
        return SuperGlobals::get_post_var($key, $default);
    }

    /**
     * This function is used to retrieve all data from the request.
     *
     * @unreleased
     * @return array Request raw data from the GET and POST super globals
     */
    public function all(): array
    {
        return array_merge(
            SuperGlobals::get_raw_superglobal('GET'),
            SuperGlobals::get_raw_superglobal('POST')
        );
    }

    /**
     * This function is used to check if a key exists in the request.
     *
     * @param string $key The key to check for in the request
     *
     * @unreleased
     */
    public function has(string $key): bool
    {
        $all = $this->all();
        return $all && array_key_exists($key, $all);
    }

    /**
     * This function is used to sanitize data.
     *
     * @param array|string $data The data to sanitize
     *
     * @unreleased
     * @return array|string
     */
    public function sanitize($data)
    {
        // If the data is a string, sanitize it.
        if (! is_array($data)) {
            return sanitizeTextField($data);
        }

        return array_map([ $this, __FUNCTION__], $data);
    }

    /**
     * Gets the incoming request headers.
     *
     * Some servers are not using Apache and "getallheaders()" will not work, so we may need to
     * build our own headers.
     *
     * @unreleased
     */
    public function getRequestHeaders(): array
    {
        $headers = [];

        if (function_exists('getallheaders')) {
            $_server = getallheaders();

            foreach ($_server as $name => $value) {
                $headers[strtoupper($name)] = $value;
            }

            return $headers;
        }

        foreach ($_SERVER as $name => $value) {
            if (strpos($name, 'HTTP_') === 0) {
                $formattedName = str_replace('_', ' ', substr($name, 5));
                $arrayKey = str_replace(' ', '-', $formattedName);
                $headers[strtoupper($arrayKey)] = $value;
            }
        }

        return $headers;
    }

    /**
     * This function is used to retrieve the request body.
     *
     * @unreleased
     */
    public function getBody(): string
    {
        $function = 'file_get_contents';

        if (function_exists('wpcom_vip_file_get_contents')) {
            $function = 'wpcom_vip_file_get_contents';
        }

        return $function('php://input');
    }

    /**
     * This function is used to check if the request has a valid nonce.
     *
     * @param string $action The action to check the nonce for
     *
     * @unreleased
     */
    public function hasValidNonce(string $action): bool
    {
        $nonceName = Constants::NONCE_NAME;
        $requestedData = $this->all();
        return array_key_exists($nonceName, $requestedData)
            && wp_verify_nonce($requestedData[$nonceName], $action);
    }

    /**
     * This function is used to check if the request has a valid capability.
     *
     * @param string $capability The ability to check for
     *
     * @unreleased
     */
    public function hasPermission(string $capability): bool
    {
        return current_user_can($capability);
    }

    /**
     * This function is used to check if the request method is valid.
     *
     * @unreleased
     */
    public function usesHttpMethod(string $type): bool
    {
        $server = SuperGlobals::get_raw_superglobal('SERVER');
        return isset($server['REQUEST_METHOD'])
              && ( strtoupper($type) === $server['REQUEST_METHOD'] );
    }

    /**
     * This function is used to check if the request uses the GET method.
     *
     * @unreleased
     */
    public function usesGetMethod(): bool
    {
        return $this->usesHttpMethod('GET');
    }

    /**
     * This function is used to check if the request uses the POST method.
     *
     * @unreleased
     */
    public function usesPostMethod(): bool
    {
        return $this->usesHttpMethod('POST');
    }
}
