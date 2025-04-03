<?php

add_filter('pre_set_site_transient_update_plugins', 'els_check_for_plugin_update');
add_filter('plugins_api', 'els_plugin_update_info', 10, 3);

function els_check_for_plugin_update($transient) {
    if (empty($transient->checked)) {
        return $transient;
    }

    $plugin_slug = 'easy-login-styles/easy-login-styles.php'; // Adjust if your plugin path is different
    $github_user_repo = 'rynecallahan019/easy-login-styles';

    // Get the latest release info from GitHub
    $response = wp_remote_get("https://api.github.com/repos/{$github_user_repo}/releases/latest");
    if (is_wp_error($response)) return $transient;

    $release = json_decode(wp_remote_retrieve_body($response));
    if (!isset($release->tag_name)) return $transient;

    $latest_version = ltrim($release->tag_name, 'v');
    $current_version = $transient->checked[$plugin_slug];

    if (version_compare($latest_version, $current_version, '>')) {
        $transient->response[$plugin_slug] = (object)[
            'slug' => 'easy-login-styles',
            'plugin' => $plugin_slug,
            'new_version' => $latest_version,
            'url' => $release->html_url,
            'package' => $release->zipball_url, // GitHub zip download link
        ];
    }

    return $transient;
}

function els_plugin_update_info($false, $action, $args) {
    if ($action !== 'plugin_information') return $false;
    if ($args->slug !== 'easy-login-styles') return $false;

    $github_user_repo = 'rynecallahan019/easy-login-styles';
    $response = wp_remote_get("https://api.github.com/repos/{$github_user_repo}/releases/latest");
    if (is_wp_error($response)) return $false;

    $release = json_decode(wp_remote_retrieve_body($response));

    return (object)[
        'name' => 'Easy Login Styles',
        'slug' => 'easy-login-styles',
        'version' => ltrim($release->tag_name, 'v'),
        'author' => '<a href="https://rynecallahan.com">Ryne Callahan</a>',
        'homepage' => $release->html_url,
        'download_link' => $release->zipball_url,
        'sections' => [
            'description' => $release->body ?? 'A custom login style plugin.',
            'changelog' => $release->body ?? '',
        ],
    ];
}
