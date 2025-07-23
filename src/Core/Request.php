<?php

/**
 * Request
 *
 * This file extends the shared core Request functionality for the Free plugin.
 *
 * @package WpRollback\Free\Core
 * @since 1.0.0
 */

declare(strict_types=1);

namespace WpRollback\Free\Core;

use WpRollback\SharedCore\Core\Request as SharedRequest;

/**
 * Class Request
 *
 * @since 1.0.0
 */
class Request extends SharedRequest
{
    /**
     * @var Constants
     */
    protected Constants $constants;
    
    /**
     * Constructor.
     *
     * @param Constants $constants The Constants instance
     */
    public function __construct(Constants $constants)
    {
        parent::__construct();
        $this->constants = $constants;
    }
    
    /**
     * This function is used to check if the request has a valid nonce.
     * Overrides the parent method to use the Free plugin's Constants.
     *
     * @param string $action The action to check the nonce for
     * @param string $nonceName Optional custom nonce name
     *
     * @since 1.0.0
     */
    public function hasValidNonce(string $action, string $nonceName = ''): bool
    {
        if (empty($nonceName)) {
            $nonceName = $this->constants->getNonce();
        }
        
        $requestedData = $this->all();
        return array_key_exists($nonceName, $requestedData)
            && wp_verify_nonce($requestedData[$nonceName], $action);
    }
}
