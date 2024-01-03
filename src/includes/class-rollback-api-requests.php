<?php

class WP_Rollback_API_Fetcher {

    /**
     * @param WP_REST_Request $request
     *
     * @return array|array[]|false|mixed|null
     */
    public function fetch_plugin_or_theme_info( WP_REST_Request $request ) {

        if ( $request['rollback_type'] === 'pro' ) {

            $pro_rollbacks = new WP_Rollback_Pro_Rollbacks();
            $pro_versions  = $pro_rollbacks->get_rollback_pro_versions( $request['slug'] );
            $additional_data = get_plugin_data( $this->get_plugin_file_path( $request['slug'] ) );

            return array_merge( $additional_data, [ 'versions' => $pro_versions, 'request' => $request->get_params() ] );

        }

        $url = $request['type'] === 'theme' ?
            "https://api.wordpress.org/themes/info/1.2/?action=theme_information&request[slug]=" . $request['slug'] . "&request[fields][versions]=1" :
            "https://api.wordpress.org/plugins/info/1.2/?action=plugin_information&request[slug]=" . $request['slug'] . "&request[fields][versions]=1";

        $response = wp_remote_get( $url );
        if ( is_wp_error( $response ) ) {
            return false;
        }

        return json_decode( wp_remote_retrieve_body( $response ), true );
    }


    public function get_plugin_file_path( $plugin_slug ) {
        // Include the plugin.php file if not already included
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        // Retrieve all installed plugins
        $plugins = get_plugins();

        // Loop through the plugins to find the one matching our slug
        foreach ( $plugins as $plugin_path => $plugin_data ) {
            if ( dirname( $plugin_path ) === $plugin_slug ) {
                // Return the full path to the plugin file
                return WP_PLUGIN_DIR . '/' . $plugin_path;
            }
        }

        // Return false if the plugin is not found
        return false;
    }


}
