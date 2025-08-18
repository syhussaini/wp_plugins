<?php
/**
 * Uninstall Admin Welcome Modal Plugin
 *
 * @package AdminWelcomeModal
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('awm_options');

// Clean up any transients
delete_transient('awm_modal_cache');

// Remove any scheduled hooks
wp_clear_scheduled_hook('awm_cleanup_session_data');

// Clean up user meta if we implemented "once per login" feature
delete_metadata('user', 0, 'awm_modal_shown', '', true);

// Clean up any custom database tables if created in future versions
// $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}awm_modal_logs");

// Clear any cached data
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
}
