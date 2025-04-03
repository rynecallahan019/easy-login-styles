<?php
/*
Plugin Name: Easy Login Styles
Plugin URI: https://github.com/rynecallahan019/easy-login-styles
GitHub Plugin URI: https://github.com/rynecallahan019/easy-login-styles
Description: Login plugin built for Callahan Media customers
Version: 1.0.5
Author: Callahan Media
Author URI: https://rynecallahan.com/
*/

// Include the updater library
require_once dirname(__FILE__) . '/plugin-update-checker/plugin-update-checker.php';

// Set up the updater
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/rynecallahan019/easy-login-styles/',
    __FILE__,
    'easy-login-styles'
);

// Set the branch that contains the stable release
$myUpdateChecker->setBranch('main');

// Enable GitHub release asset updates
$myUpdateChecker->getVcsApi()->enableReleaseAssets();

if( ! class_exists('ACF') ) {
    include_once plugin_dir_path(__FILE__) . 'acf/advanced-custom-fields-pro/acf.php';
}

add_filter('acf/settings/show_admin', '__return_true');


if( function_exists('acf_add_options_page') ) {
    acf_add_options_page([
        'page_title' => 'Login Page Settings',
        'menu_title' => 'Login Customizer',
        'menu_slug'  => 'login-customizer',
        'capability' => 'manage_options',
        'redirect'   => false
    ]);
}

function custom_login_logo_url() {
    return home_url(); // Redirects to the site's homepage
}
add_filter('login_headerurl', 'custom_login_logo_url');

function custom_login_logo_title() {
    return get_bloginfo('name'); // Shows the site name on hover
}
add_filter('login_headertext', 'custom_login_logo_title');


if( function_exists('acf_add_options_page') ) {
    acf_add_options_page([
        'page_title'  => 'Login Customizer',
        'menu_title'  => 'Login Customizer',
        'menu_slug'   => 'login-customizer',
        'capability'  => 'manage_options',
        'redirect'    => false,
        'position'    => 61, // Controls where it appears in the menu
        'icon_url'    => 'dashicons-admin-customizer' // Uses a built-in WordPress icon
    ]);
}


if( function_exists('acf_add_local_field_group') ) {
    acf_add_local_field_group([
        'key' => 'group_login_customizer',
        'title' => 'Login Customizer Settings',
        'fields' => [
            // General Settings
            [
                'key'   => 'field_description',
                'label' => '',
                'name'  => 'login_customizer_description',
                'type'  => 'message',
                'message' => '<strong>These settings apply to the Forgot Password page as well.</strong>',
            ],
            [
                'key' => 'field_general_tab',
                'label' => 'General Settings',
                'type' => 'tab',
                'placement' => 'left'
            ],
            [
                'key' => 'field_login_logo',
                'label' => 'Custom Logo',
                'name' => 'login_logo',
                'type' => 'image',
                'return_format' => 'url'
            ],
            [
                'key' => 'field_background_color',
                'label' => 'Background Color',
                'name' => 'background_color',
                'type' => 'color_picker'
            ],
            [
                'key' => 'field_background_transparent',
                'label' => 'Make Background Transparent',
                'name' => 'background_transparent',
                'type' => 'true_false',
                'ui' => 1
            ],
            [
                'key' => 'field_background_image',
                'label' => 'Background Image',
                'name' => 'background_image',
                'type' => 'image',
                'return_format' => 'url'
            ],

            // Form Appearance
            [
                'key' => 'field_form_tab',
                'label' => 'Form Appearance',
                'type' => 'tab',
                'placement' => 'left'
            ],
            [
                'key' => 'field_form_background_color',
                'label' => 'Form Background Color',
                'name' => 'form_background_color',
                'type' => 'color_picker'
            ],
            [
                'key' => 'field_form_background_transparent',
                'label' => 'Make Form Background Transparent',
                'name' => 'form_background_transparent',
                'type' => 'true_false',
                'ui' => 1
            ],
            [
                'key' => 'field_form_border_color',
                'label' => 'Form Border Color',
                'name' => 'form_border_color',
                'type' => 'color_picker'
            ],
            [
                'key' => 'field_form_border_transparent',
                'label' => 'Make Form Border Transparent',
                'name' => 'form_border_transparent',
                'type' => 'true_false',
                'ui' => 1
            ],
            [
                'key' => 'field_form_border_radius',
                'label' => 'Form Border Radius',
                'name' => 'form_border_radius',
                'type' => 'number',
                'instructions' => 'Set border radius in pixels (e.g., 10 for 10px).',
                'default_value' => 10
            ],
            [
                'key' => 'field_input_background_color',
                'label' => 'Text Field Background Color',
                'name' => 'input_background_color',
                'type' => 'color_picker'
            ],
            [
                'key' => 'field_input_background_transparent',
                'label' => 'Make Text Field Background Transparent',
                'name' => 'input_background_transparent',
                'type' => 'true_false',
                'ui' => 1
            ],

            // Button & Links
            [
                'key' => 'field_button_tab',
                'label' => 'Button & Links',
                'type' => 'tab',
                'placement' => 'left'
            ],
            [
                'key' => 'field_button_color',
                'label' => 'Button Color',
                'name' => 'button_color',
                'type' => 'color_picker'
            ],
            [
                'key' => 'field_label_color',
                'label' => 'Label & Link Color',
                'name' => 'label_color',
                'type' => 'color_picker',
                'default_value' => '#333333'
            ]
        ],
        'location' => [[
            ['param' => 'options_page', 'operator' => '==', 'value' => 'login-customizer']
        ]]
    ]);
}



function custom_login_styles() {
    if ( ! function_exists('get_field') ) {
        return; // Prevent errors if ACF is missing
    }

    // Get ACF fields
    $logo = get_field('login_logo', 'option');
    $bg_color = get_field('background_color', 'option');
    $bg_transparent = get_field('background_transparent', 'option');
    $bg_image = get_field('background_image', 'option');
    $button_color = get_field('button_color', 'option') ?: '#0073aa';
    $form_bg_color = get_field('form_background_color', 'option');
    $form_bg_transparent = get_field('form_background_transparent', 'option');
    $form_border_radius = get_field('form_border_radius', 'option') ?: 10;
    $form_border_color = get_field('form_border_color', 'option');
    $form_border_transparent = get_field('form_border_transparent', 'option');
    $label_color = get_field('label_color', 'option') ?: '#333333';
    $input_bg_color = get_field('input_background_color', 'option');
    $input_bg_transparent = get_field('input_background_transparent', 'option');

    // Ensure user selects only one background type
    $background_style = $bg_image ? 
        "background: url('$bg_image') no-repeat center center / cover;" : 
        ($bg_transparent ? "background: transparent;" : "background: $bg_color;");

    // Form background color logic
    $form_bg = $form_bg_transparent ? "background: transparent;" : "background: $form_bg_color;";

    // Form border color logic
    $form_border = $form_border_transparent ? "border: none;" : "border: 1px solid $form_border_color;";

    // Input field background logic
    $input_bg = $input_bg_transparent ? "background: transparent;" : "background: $input_bg_color;";
    
    // Generate the CSS dynamically
    echo '<style type="text/css">
        /* Global Reset */
        body.login {
            display: flex !important;
            align-items: center;
            justify-content: center;
            height: 100vh !important;
            ' . $background_style . '
        }

        @media (max-width: 1024px) {
            body.login {
                align-items: flex-start !important;
            }
        }

         @media (max-width: 1024px) {
            input[type="radio"], input[type="checkbox"] {
                height: 1rem !important;
                width: 1rem !important;
            }
        }

        @media (max-width: 1024px) {
            #login {
            width: 100% !important;
            }
        }

        .login form {
            background: transparent !important;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
        }

        /* Centered & Larger Form */
        #login {
            width: 400px !important;
            max-width: 90% !important;
            padding: 60px 20px 20px 20px !important;
            ' . $form_bg . '
            border-radius: ' . $form_border_radius . 'px !important;
            ' . $form_border . '
            box-shadow: none !important;
            text-align: left !important;
        }

        /* Custom Logo - Show logo only if its uploaded */
        .login h1 a {
            background-image: url("' . ($logo ? $logo : '') . '") !important;
            background-size: contain !important;
            max-width: 100px !important;
            display: ' . ($logo ? 'block' : 'none') . ' !important;
            margin: 0 auto 20px !important;
        }

        /* Custom Input Fields */
        .login form input[type="text"],
        .login form input[type="password"] {
            width: 100% !important;
            padding: 12px !important;
            margin-bottom: 10px !important;
            border-radius: 5px !important;
            border: 1px solid ' . $form_border_color . ' !important;
            font-size: 16px !important;
            text-align: left !important;
            box-sizing: border-box !important;
            ' . $input_bg . '
        }

        /* Label & Link Customization */
        .login label, 
        .login #nav a, 
        .login #backtoblog a {
            color: ' . $label_color . ' !important;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
        }

        .login p, .login a {
            color: ' . $label_color . ' !important;
            text-decoration: none !important
        }

        /* Custom Button */
        .wp-core-ui .button-primary {
            background-color: ' . $button_color . ' !important;
            border: none !important;
            border-radius: 5px !important;
            padding: 8px !important;
            font-size: 16px !important;
            transition: 0.3s !important;
            width: 100% !important;
            margin-top: 25px !important;
            outline: none !important;
            box-shadow: none !important;
        }

        /* Button Hover */
        .wp-core-ui .button-primary:hover {
            background-color: darken(' . $button_color . ', 10%) !important;
        }

        .login .button.wp-hide-pw, 
        .login .button.wp-hide-pw:hover, 
        .login .button.wp-hide-pw:active, 
        .login .button.wp-hide-pw:focus {
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            padding: 0px !important;
            width: auto !important;
            height: auto !important;
            min-width: 47px !important;
            min-height: 47px !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
        }

        .login form .wp-hide-pw {
            border: none !important;
        }

        /* Center the icon properly */
        .login .button.wp-hide-pw .dashicons {
            width: 20px !important; /* Adjust if needed */
            height: 20px !important; /* Adjust if needed */
            position: relative !important;
            top: 0 !important;
            left: 0 !important;
        }

        div[style*="border-top:1px solid #ddd"] {
            border-top: none !important;
        }

    </style>';
}
add_action('login_enqueue_scripts', 'custom_login_styles');
