<?php
class WP_Rollback_API_Fetcher {
    public function fetch_plugin_or_theme_info($type, $slug) {
        $url = $type === 'theme' ?
            "https://api.wordpress.org/themes/info/1.2/?action=theme_information&request[slug]=$slug&request[fields][versions]=1" :
            "https://api.wordpress.org/plugins/info/1.2/?action=plugin_information&request[slug]=$slug&request[fields][versions]=1";

        $response = wp_remote_get($url);
        if (is_wp_error($response)) {
            return false;
        }

        return json_decode(wp_remote_retrieve_body($response), true);
    }
}
