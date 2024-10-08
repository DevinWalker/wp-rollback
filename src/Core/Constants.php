<?php

namespace WpRollback\Core;

class Constants {
    /**
     * @var string
     * @unreleased
     */
    public const TEXT_DOMAIN = 'wp-rollback';

    /**
     * @var string
     * @unreleased
     */
    public const VERSION = '3.0.0';

    /**
     * @var string
     * @unreleased
     */
    public const PLUGIN_SLUG = 'wp-rollback';

    /**
     * @var string
     * @unreleased
     */
    public const NONCE_NAME = 'wp-rollback-nonce';

    /**
     * @unreleased
     */
    public static ?string $PLUGIN_URL;

    /**
     * @unreleased
     */
    public static ?string $PLUGIN_DIR;

    /**
     * @unreleased
     */
    public static ?string $PLUGIN_ROOT_FILE_RELATIVE_PATH;

    /**
     * @var string
     * @unreleased
     */
    public static ?string $PLUGIN_ROOT_FILE;

    /**
     * Constants constructor.
     *
     * @unreleased
     */
    public function __construct() {
        $pluginFile = sprintf(
            dirname( __DIR__, 2 ) . '/%1$s.php',
            self::PLUGIN_SLUG
        );

        self::$PLUGIN_URL                     = untrailingslashit( plugins_url( '', $pluginFile ) );
        self::$PLUGIN_DIR                     = untrailingslashit( plugin_dir_path( $pluginFile ) );
        self::$PLUGIN_ROOT_FILE_RELATIVE_PATH = plugin_basename( $pluginFile );
        self::$PLUGIN_ROOT_FILE = $pluginFile;


    }
}
