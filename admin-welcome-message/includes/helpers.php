<?php
/**
 * Helper Functions
 *
 * @package AdminWelcomeModal
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get plugin options with defaults
 */
function awm_get_options() {
    $defaults = [
        'title' => __('Welcome to Your Site', 'zaha-admin-welcome-message'),
        'message' => '<p>' . __('This is an important message for administrators. Please review the information below.', 'zaha-admin-welcome-message') . '</p>',
        'cta_text' => __('Access Help', 'zaha-admin-welcome-message'),
        'cta_url' => admin_url('admin.php?page=wp-help-documents'),
        'footer_note' => __("Don't show this again during my current session", 'zaha-admin-welcome-message'),
        'dismiss_mode' => 'session',
        'cooldown_minutes' => 15,
        'roles' => [],
        'screens' => '',
        'cta_new_tab' => true,
        'close_on_esc' => true,
        'close_on_cta' => true,
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
    
    $options = get_option('awm_options', []);
    return wp_parse_args($options, $defaults);
}

/**
 * Check if current user can see the modal
 */
function awm_user_can_see_modal() {
    if (!is_admin()) {
        return false;
    }
    
    if (!current_user_can('manage_options')) {
        return false;
    }
    
    $options = awm_get_options();
    
    // Check role restrictions
    if (!empty($options['roles'])) {
        $user = wp_get_current_user();
        $user_roles = $user->roles;
        $allowed_roles = $options['roles'];
        
        $has_allowed_role = false;
        foreach ($user_roles as $role) {
            if (in_array($role, $allowed_roles)) {
                $has_allowed_role = true;
                break;
            }
        }
        
        if (!$has_allowed_role) {
            return false;
        }
    }
    
    return true;
}

/**
 * Check if current screen should show modal
 */
function awm_screen_should_show_modal() {
    $options = awm_get_options();
    
    if (empty($options['screens'])) {
        return true;
    }
    
    $current_screen = get_current_screen();
    if (!$current_screen) {
        return false;
    }
    
    $allowed_screens = array_filter(array_map('trim', explode("\n", $options['screens'])));
    return in_array($current_screen->id, $allowed_screens);
}

/**
 * Get modal display conditions
 */
function awm_get_modal_conditions() {
    return [
        'user_can_see' => awm_user_can_see_modal(),
        'screen_allowed' => awm_screen_should_show_modal(),
        'is_admin' => is_admin()
    ];
}

/**
 * Sanitize hex color
 */
function awm_sanitize_hex_color($color) {
    if ('' === $color) {
        return '';
    }
    
    // 3 or 6 hex digits, or the empty string.
    if (preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color)) {
        return $color;
    }
    
    return '';
}

/**
 * Get user roles for settings
 */
function awm_get_user_roles() {
    $roles = wp_roles()->get_names();
    return apply_filters('awm_user_roles', $roles);
}

/**
 * Get admin screens for settings
 */
function awm_get_admin_screens() {
    global $wp_admin_bar;
    
    $screens = [];
    
    if (function_exists('get_current_screen')) {
        $current_screen = get_current_screen();
        if ($current_screen) {
            $screens[] = $current_screen->id;
        }
    }
    
    // Common admin screens
    $common_screens = [
        'dashboard',
        'post',
        'edit-post',
        'page',
        'edit-page',
        'upload',
        'edit-comments',
        'users',
        'profile',
        'tools',
        'options-general',
        'themes',
        'plugins'
    ];
    
    $screens = array_merge($screens, $common_screens);
    $screens = array_unique($screens);
    sort($screens);
    
    return apply_filters('awm_admin_screens', $screens);
}
