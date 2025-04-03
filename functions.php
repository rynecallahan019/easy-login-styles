<?php

add_filter('pre_set_site_transient_update_plugins', 'els_check_for_plugin_update');
add_filter('plugins_api', 'els_plugin_update_info', 10, 3);

function els_check_for_plugin_update($transient) {
    if (empty($transient->checked)) {
        return $transient;
    }
    
    $plugin_slug = 'easy-login-styles/easy-login-styles.php';
    $github_user_repo = 'rynecallahan019/easy-login-styles';
    
    // Make sure our plugin is in the checked list
    if (!isset($transient->checked[$plugin_slug])) {
        return $transient;
    }
    
    $current_version = $transient->checked[$plugin_slug];
    
    // Get the latest release info from GitHub
    $response = wp_remote_get("https://api.github.com/repos/{$github_user_repo}/releases/latest", array(
        'headers' => array(
            'Accept' => 'application/vnd.github.v3+json',
            'User-Agent' => 'WordPress/' . get_bloginfo('version')
        )
    ));
    
    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
        return $transient;
    }
    
    $release = json_decode(wp_remote_retrieve_body($response));
    
    if (!isset($release->tag_name)) {
        return $transient;
    }
    
    $latest_version = ltrim($release->tag_name, 'v');
    
    if (version_compare($latest_version, $current_version, '>')) {
        $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin_slug);
        
        $transient->response[$plugin_slug] = (object)[
            'slug' => 'easy-login-styles',
            'plugin' => $plugin_slug,
            'new_version' => $latest_version,
            'url' => isset($plugin_data['PluginURI']) ? $plugin_data['PluginURI'] : $release->html_url,
            'package' => $release->zipball_url,
            'icons' => array(),
            'banners' => array(),
            'banners_rtl' => array(),
            'tested' => get_bloginfo('version'),
            'requires_php' => '5.6',
            'compatibility' => new stdClass(),
        ];
    }
    
    return $transient;
}

function els_plugin_update_info($false, $action, $args) {
    if ($action !== 'plugin_information') {
        return $false;
    }
    
    if (!isset($args->slug) || $args->slug !== 'easy-login-styles') {
        return $false;
    }
    
    $github_user_repo = 'rynecallahan019/easy-login-styles';
    
    $response = wp_remote_get("https://api.github.com/repos/{$github_user_repo}/releases/latest", array(
        'headers' => array(
            'Accept' => 'application/vnd.github.v3+json',
            'User-Agent' => 'WordPress/' . get_bloginfo('version')
        )
    ));
    
    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
        return $false;
    }
    
    $release = json_decode(wp_remote_retrieve_body($response));
    $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/easy-login-styles/easy-login-styles.php');
    
    return (object)[
        'name' => $plugin_data['Name'] ?? 'Easy Login Styles',
        'slug' => 'easy-login-styles',
        'version' => ltrim($release->tag_name, 'v'),
        'author' => $plugin_data['Author'] ?? '<a href="https://rynecallahan.com">Ryne Callahan</a>',
        'author_profile' => 'https://rynecallahan.com',
        'requires' => $plugin_data['RequiresWP'] ?? '5.0',
        'tested' => $plugin_data['TestedUpTo'] ?? get_bloginfo('version'),
        'requires_php' => $plugin_data['RequiresPHP'] ?? '5.6',
        'homepage' => $plugin_data['PluginURI'] ?? $release->html_url,
        'download_link' => $release->zipball_url,
        'last_updated' => $release->published_at,
        'sections' => [
            'description' => $plugin_data['Description'] ?? 'A custom login style plugin.',
            'installation' => $plugin_data['Installation'] ?? 'Upload the plugin to your WordPress site.',
            'changelog' => $release->body ?? 'See GitHub for changelog details.',
        ],
    ];
}
