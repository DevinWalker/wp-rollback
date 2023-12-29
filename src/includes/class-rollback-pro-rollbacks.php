<?php

class WP_Rollback_Pro_Rollbacks {

    public function __construct() {
        $this->register_hooks();
    }

    public function register_hooks() {
        add_filter( 'upgrader_package_options', [ $this, 'store_pro_rollback' ] );
    }

    public function store_pro_rollback( $options ) {
        if ( ! isset( $options['destination'], $options['hook_extra']['plugin'] ) ) {
            return $options;
        }

        // If package is from wordpress.org, don't create a rollback zip.
        if ( ! isset( $options['package'] ) ||
             ( strpos( $options['package'], 'downloads.wordpress.org' ) !== false ) ) {
            return $options;
        }

        $plugin = $options['hook_extra']['plugin'];
        $path   = sanitize_option( 'upload_path', $options['destination'] . '/' . $plugin );

        // If zip of this version is already created, don't do anything.
        if ( ! file_exists( $path ) ) {
            return $options;
        }

        $plugin_data = get_plugin_data( $path );
        $version = isset( $plugin_data['Version'] ) ? $plugin_data['Version'] : 'unknown';

        $this->create_rollback_directory();
        $rollback_dir = wp_upload_dir()['basedir'] . '/wp-rollback';
        $zip_filename = basename( $path, '.php' ) . '-' . $version . '.zip';
        $zip_path     = $rollback_dir . '/' . $zip_filename;

        if ( $this->zip_directory( dirname( $path ), $zip_path ) ) {
            // Zip creation successful
        } else {
            // Handle the error in zip creation
        }

        return $options;
    }

    public function create_rollback_directory() {
        $upload_dir   = wp_upload_dir();
        $rollback_dir = $upload_dir['basedir'] . '/wp-rollback';
        if ( ! file_exists( $rollback_dir ) ) {
            wp_mkdir_p( $rollback_dir );
        }
    }

    private function zip_directory( $source, $destination ): bool {
        if ( ! extension_loaded( 'zip' ) || ! file_exists( $source ) ) {
            return false;
        }

        $zip = new ZipArchive();
        if ( ! $zip->open( $destination, ZipArchive::CREATE | ZipArchive::OVERWRITE ) ) {
            return false;
        }

        $source = realpath( $source );
        if ( is_dir( $source ) ) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator( $source, RecursiveDirectoryIterator::SKIP_DOTS ),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ( $files as $file ) {
                $file = realpath( $file );
                if ( is_dir( $file ) ) {
                    $zip->addEmptyDir( str_replace( $source . '/', '', $file . '/' ) );
                } elseif ( is_file( $file ) ) {
                    $zip->addFile( $file, str_replace( $source . '/', '', $file ) );
                }
            }
        } elseif ( is_file( $source ) ) {
            $zip->addFile( $source, basename( $source ) );
        }

        return $zip->close();
    }
}

