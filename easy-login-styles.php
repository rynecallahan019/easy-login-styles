<?php
/*
Plugin Name: Easy Login Styles
Plugin URI: https://github.com/rynecallahan019/easy-login-styles
GitHub Plugin URI: https://github.com/rynecallahan019/easy-login-styles
Description: Login plugin built for Callabridge customers
Version: 1.1.2
Author: Callabridge
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

// Add custom settings page
function els_register_settings_page() {
    add_menu_page(
        'Login Page Settings',
        'Login Customizer',
        'manage_options',
        'login-customizer',
        'els_settings_page_callback',
        'dashicons-admin-customizer',
        61
    );
}
add_action('admin_menu', 'els_register_settings_page');

// Register settings with sanitization
function els_register_settings() {
    // General Settings
    register_setting('els_settings_group', 'els_login_logo', ['sanitize_callback' => 'esc_url_raw']);
    register_setting('els_settings_group', 'els_background_color', ['sanitize_callback' => 'sanitize_hex_color']);
    register_setting('els_settings_group', 'els_background_transparent', ['sanitize_callback' => 'absint']);
    register_setting('els_settings_group', 'els_background_image', ['sanitize_callback' => 'esc_url_raw']);
    
    // Form Appearance
    register_setting('els_settings_group', 'els_form_background_color', ['sanitize_callback' => 'sanitize_hex_color']);
    register_setting('els_settings_group', 'els_form_background_transparent', ['sanitize_callback' => 'absint']);
    register_setting('els_settings_group', 'els_form_border_color', ['sanitize_callback' => 'sanitize_hex_color']);
    register_setting('els_settings_group', 'els_form_border_transparent', ['sanitize_callback' => 'absint']);
    register_setting('els_settings_group', 'els_form_border_radius', ['sanitize_callback' => 'absint']);
    register_setting('els_settings_group', 'els_input_background_color', ['sanitize_callback' => 'sanitize_hex_color']);
    register_setting('els_settings_group', 'els_input_background_transparent', ['sanitize_callback' => 'absint']);
    
    // Button & Links
    register_setting('els_settings_group', 'els_button_color', ['sanitize_callback' => 'sanitize_hex_color']);
    register_setting('els_settings_group', 'els_label_color', ['sanitize_callback' => 'sanitize_hex_color']);
    register_setting('els_settings_group', 'els_notice_color', ['sanitize_callback' => 'sanitize_hex_color']);
}
add_action('admin_init', 'els_register_settings');

// Settings page callback
function els_settings_page_callback() {
    ?>
    <div class="wrap">
        <h1>Login Customizer</h1>
        <p><strong>These settings apply to the Forgot Password page as well.</strong></p>
        <form method="post" action="options.php">
            <?php settings_fields('els_settings_group'); ?>
            <?php do_settings_sections('els_settings_group'); ?>
            
            <h2>General Settings</h2>
            <table class="form-table">
                <tr>
                    <th><label for="els_login_logo">Custom Logo</label></th>
                    <td>
                        <input type="text" name="els_login_logo" id="els_login_logo" value="<?php echo esc_attr(get_option('els_login_logo', '')); ?>" class="regular-text" />
                        <input type="button" class="button els-upload-image" value="Upload Image" />
                        <p class="description">Enter the URL of the logo or upload an image.</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="els_background_color">Background Color</label></th>
                    <td>
                        <input type="text" name="els_background_color" id="els_background_color" value="<?php echo esc_attr(get_option('els_background_color', '#ffffff')); ?>" class="els-color-picker" />
                    </td>
                </tr>
                <tr>
                    <th><label for="els_background_transparent">Make Background Transparent</label></th>
                    <td>
                        <input type="checkbox" name="els_background_transparent" id="els_background_transparent" value="1" <?php checked(get_option('els_background_transparent', 0), 1); ?> />
                    </td>
                </tr>
                <tr>
                    <th><label for="els_background_image">Background Image</label></th>
                    <td>
                        <input type="text" name="els_background_image" id="els_background_image" value="<?php echo esc_attr(get_option('els_background_image', '')); ?>" class="regular-text" />
                        <input type="button" class="button els-upload-image" value="Upload Image" />
                        <p class="description">Enter the URL of the background image or upload an image.</p>
                    </td>
                </tr>
            </table>
            
            <h2>Form Appearance</h2>
            <table class="form-table">
                <tr>
                    <th><label for="els_form_background_color">Form Background Color</label></th>
                    <td>
                        <input type="text" name="els_form_background_color" id="els_form_background_color" value="<?php echo esc_attr(get_option('els_form_background_color', '#ffffff')); ?>" class="els-color-picker" />
                    </td>
                </tr>
                <tr>
                    <th><label for="els_form_background_transparent">Make Form Background Transparent</label></th>
                    <td>
                        <input type="checkbox" name="els_form_background_transparent" id="els_form_background_transparent" value="1" <?php checked(get_option('els_form_background_transparent', 0), 1); ?> />
                    </td>
                </tr>
                <tr>
                    <th><label for="els_form_border_color">Form Border Color</label></th>
                    <td>
                        <input type="text" name="els_form_border_color" id="els_form_border_color" value="<?php echo esc_attr(get_option('els_form_border_color', '#dddddd')); ?>" class="els-color-picker" />
                    </td>
                </tr>
                <tr>
                    <th><label for="els_form_border_transparent">Make Form Border Transparent</label></th>
                    <td>
                        <input type="checkbox" name="els_form_border_transparent" id="els_form_border_transparent" value="1" <?php checked(get_option('els_form_border_transparent', 0), 1); ?> />
                    </td>
                </tr>
                <tr>
                    <th><label for="els_form_border_radius">Form Border Radius</label></th>
                    <td>
                        <input type="number" name="els_form_border_radius" id="els_form_border_radius" value="<?php echo esc_attr(get_option('els_form_border_radius', 10)); ?>" />
                        <p class="description">Set border radius in pixels (e.g., 10 for 10px).</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="els_input_background_color">Text Field Background Color</label></th>
                    <td>
                        <input type="text" name="els_input_background_color" id="els_input_background_color" value="<?php echo esc_attr(get_option('els_input_background_color', '#ffffff')); ?>" class="els-color-picker" />
                    </td>
                </tr>
                <tr>
                    <th><label for="els_input_background_transparent">Make Text Field Background Transparent</label></th>
                    <td>
                        <input type="checkbox" name="els_input_background_transparent" id="els_input_background_transparent" value="1" <?php checked(get_option('els_input_background_transparent', 0), 1); ?> />
                    </td>
                </tr>
            </table>
            
            <h2>Button & Links</h2>
            <table class="form-table">
                <tr>
                    <th><label for="els_button_color">Button Color</label></th>
                    <td>
                        <input type="text" name="els_button_color" id="els_button_color" value="<?php echo esc_attr(get_option('els_button_color', '#0073aa')); ?>" class="els-color-picker" />
                    </td>
                </tr>
                <tr>
                    <th><label for="els_label_color">Label & Link Color</label></th>
                    <td>
                        <input type="text" name="els_label_color" id="els_label_color" value="<?php echo esc_attr(get_option('els_label_color', '#333333')); ?>" class="els-color-picker" />
                    </td>
                </tr>
                <tr>
                    <th><label for="els_notice_color">Notice Message Label Color</label></th>
                    <td>
                        <input type="text" name="els_notice_color" id="els_notice_color" value="<?php echo esc_attr(get_option('els_notice_color', '#d63638')); ?>" class="els-color-picker" />
                        <p class="description">Color for error and success messages.</p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
    </div>
    <script>
    jQuery(document).ready(function($) {
        // Initialize color picker
        $('.els-color-picker').wpColorPicker();
        
        // Initialize media uploader
        $('.els-upload-image').on('click', function(e) {
            e.preventDefault();
            var $button = $(this);
            var $input = $button.prev('input');
            var uploader = wp.media({
                title: 'Select Image',
                button: { text: 'Select Image' },
                multiple: false
            }).on('select', function() {
                var attachment = uploader.state().get('selection').first().toJSON();
                $input.val(attachment.url).trigger('change');
            }).open();
        });
    });
    </script>
    <?php
}

// Enqueue WordPress color picker and media uploader scripts
function els_enqueue_admin_scripts($hook) {
    if ($hook !== 'toplevel_page_login-customizer') {
        return;
    }
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker', admin_url('js/color-picker.min.js'), ['jquery'], false, true);
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'els_enqueue_admin_scripts');

function custom_login_logo_url() {
    return home_url(); // Redirects to the site's homepage
}
add_filter('login_headerurl', 'custom_login_logo_url');

function custom_login_logo_title() {
    return get_bloginfo('name'); // Shows the site name on hover
}
add_filter('login_headertext', 'custom_login_logo_title');

function custom_login_styles() {
    // Get settings with defaults
    $logo = get_option('els_login_logo', '');
    $bg_color = get_option('els_background_color', '#ffffff');
    $bg_transparent = get_option('els_background_transparent', 0);
    $bg_image = get_option('els_background_image', '');
    $button_color = get_option('els_button_color', '#0073aa');
    $form_bg_color = get_option('els_form_background_color', '#ffffff');
    $form_bg_transparent = get_option('els_form_background_transparent', 0);
    $form_border_radius = get_option('els_form_border_radius', 10);
    $form_border_color = get_option('els_form_border_color', '#dddddd');
    $form_border_transparent = get_option('els_form_border_transparent', 0);
    $label_color = get_option('els_label_color', '#333333');
    $input_bg_color = get_option('els_input_background_color', '#ffffff');
    $input_bg_transparent = get_option('els_input_background_transparent', 0);
    $notice_color = get_option('els_notice_color', '#d63638');

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
    ?>
    <style type="text/css">
        /* Global Reset */
        body.login {
            display: flex !important;
            align-items: center;
            overflow: hidden;
            justify-content: center;
            height: 100vh !important;
            <?php echo $background_style; ?>
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

        @media (max-width: 520px) {
            #login {
                max-width: 100% !important;
                min-width: 100% !important;
            }
            body.login {
                height: 100%;
                border: none !important;
                border-radius: 0 !important;
                overflow: hidden;
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
            <?php echo $form_bg; ?>
            border-radius: <?php echo $form_border_radius; ?>px !important;
            <?php echo $form_border; ?>
            box-shadow: none !important;
            text-align: left !important;
        }

        /* Custom Logo - Show logo only if its uploaded */
        .login h1 a {
            background-image: url("<?php echo esc_url($logo); ?>") !important;
            background-size: contain !important;
            max-width: 100px !important;
            display: <?php echo $logo ? 'block' : 'none'; ?> !important;
            margin: 0 auto 20px !important;
        }

        /* Custom Input Fields */
        .login form input[type="text"],
        .login form input[type="password"] {
            width: 100% !important;
            padding: 12px !important;
            margin-bottom: 10px !important;
            border-radius: 5px !important;
            border: 1px solid <?php echo $form_border_color; ?> !important;
            font-size: 16px !important;
            text-align: left !important;
            box-sizing: border-box !important;
            <?php echo $input_bg; ?>
        }

        /* Label & Link Customization */
        .login label, 
        .login #nav a, 
        .login #backtoblog a {
            color: <?php echo $label_color; ?> !important;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
        }

        .login form p, .login form a {
            color: <?php echo $label_color; ?> !important;
            text-decoration: none !important;
        }

        /* Custom Button */
        .wp-core-ui .button-primary {
            background-color: <?php echo $button_color; ?> !important;
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
            background-color: <?php echo $button_color; ?> !important; /* Note: darken() not supported, using same color */
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
            width: 20px !important;
            height: 20px !important;
            position: relative !important;
            top: 0 !important;
            left: 0 !important;
        }

        /* Notice Messages (Error & Success) */
        .login #login_error,
        .login .message,
        .login .success, #login_error .login p, #login_error .login a, #login_error .login a:hover {
            color: <?php echo $notice_color; ?> !important;
            /*border-left-color: <?php //echo $notice_color; ?> !important;*/
        }

        div[style*="border-top:1px solid #ddd"] {
            border-top: none !important;
        }
    </style>
    <?php
}
add_action('login_enqueue_scripts', 'custom_login_styles');
