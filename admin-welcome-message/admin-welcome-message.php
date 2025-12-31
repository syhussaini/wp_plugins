<?php
/**
 * Plugin Name: Admin Welcome Message
 * Plugin URI: https://wordpress.org/plugins/admin-welcome-message
 * Description: A customizable admin modal that displays welcome messages with configurable content, styling, and session behavior.
 * Version: 1.1.2
 * Author: Syed Hussaini
 * Author URI: https://www.zaha.in
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: zaha-admin-welcome-message
 * Domain Path: /languages
 * Requires at least: 5.8
 * Tested up to: 6.9
 * Requires PHP: 7.4
 *
 * @package AdminWelcomeMessage
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('AWM_VERSION', '1.1.2');
define('AWM_PLUGIN_FILE', __FILE__);
define('AWM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AWM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AWM_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Rely on WordPress.org language packs for translations (no manual load in WP 4.6+)

// Include required files
require_once AWM_PLUGIN_DIR . 'includes/class-awm-plugin.php';
require_once AWM_PLUGIN_DIR . 'includes/class-awm-settings.php';
require_once AWM_PLUGIN_DIR . 'includes/class-awm-render.php';
require_once AWM_PLUGIN_DIR . 'includes/helpers.php';

// Initialize the plugin (guard against re-declaration)
if (!function_exists('awm_init')) {
    function awm_init() {
        new AWM\Plugin();
    }
}
add_action('init', 'awm_init');

// Activation hook
if (!function_exists('awm_activate')) {
    function awm_activate() {
        // Set default options
        $default_options = [
            'title' => __('Welcome to Your Site', 'zaha-admin-welcome-message'),
            'message' => '<p>' . __('This is an important message for administrators. Please review the information below.', 'zaha-admin-welcome-message') . '</p>',
            'cta_text' => __('Access Help', 'zaha-admin-welcome-message'),
            'cta_url' => admin_url('admin.php?page=wp-help-documents'),
            'footer_note' => __("Don't show this again during my current session", 'zaha-admin-welcome-message'),
            'dismiss_mode' => 'cooldown',
            'enable_session_hide' => true,
            'cooldown_minutes' => 15,
            'roles' => [],
            'screens' => '',
            'cta_new_tab' => true,
            'close_on_esc' => true,
            'close_on_cta' => true,
            'close_on_overlay' => false,
            'show_cta' => true,
            'colors' => [
                'header_bg' => '#00463b',
                'header_text' => '#ffffff',
                'body_bg' => '#ffffff',
                'body_text' => '#111111',
                'footer_bg' => '#0E281D',
                'footer_text' => '#ffffff',
                'btn_bg' => '#00463b',
                'btn_text' => '#ffffff',
                'btn_bg_hover' => '#006b57',
                'btn_text_hover' => '#ffffff'
            ]
        ];
        
        add_option('awm_options', $default_options);
    }
}
register_activation_hook(__FILE__, 'awm_activate');

// Deactivation hook
if (!function_exists('awm_deactivate')) {
    function awm_deactivate() {
        // Clean up if needed
    }
}
register_deactivation_hook(__FILE__, 'awm_deactivate');
