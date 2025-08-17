<?php
/**
 * Main Plugin Class
 *
 * @package AdminWelcomeModal
 */

namespace AWM;

class Plugin {
    
    /**
     * Settings instance
     */
    private $settings;
    
    /**
     * Render instance
     */
    private $render;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->init_hooks();
        $this->init_components();
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'init_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        // Screen ID helper removed in v1.1.2
    }
    // Screen ID helper removed in v1.1.2
    
    /**
     * Initialize plugin components
     */
    private function init_components() {
        $this->settings = new Settings();
        $this->render = new Render();
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            __('Admin Welcome Message', 'admin-welcome-message'),
            __('Admin Welcome Message', 'admin-welcome-message'),
            'manage_options',
            'admin-welcome-message',
            [$this->settings, 'render_settings_page']
        );
    }
    
    /**
     * Initialize settings
     */
    public function init_settings() {
        $this->settings->init();
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        // Enqueue on our settings page; be resilient to hook variations
        $is_our_page = (isset($_GET['page']) && $_GET['page'] === 'admin-welcome-message')
            || (is_string($hook) && strpos($hook, 'admin-welcome-message') !== false);
        if (!$is_our_page) {
            return;
        }
        
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style('dashicons');
        wp_enqueue_script('wp-color-picker');
        
        wp_enqueue_style(
            'awm-admin',
            AWM_PLUGIN_URL . 'assets/css/admin.css',
            [],
            AWM_VERSION
        );
        
        wp_enqueue_script(
            'awm-admin',
            AWM_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery', 'wp-color-picker'],
            AWM_VERSION,
            true
        );
        
        wp_localize_script('awm-admin', 'awmAdminData', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('awm_admin_nonce'),
            'strings' => [
                'saved' => __('Settings saved successfully!', 'admin-welcome-message'),
                'error' => __('Error saving settings.', 'admin-welcome-message')
            ]
        ]);
    }

    // Screen ID helper removed in v1.1.2
}
