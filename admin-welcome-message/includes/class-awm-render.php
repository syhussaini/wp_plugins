<?php
/**
 * Render Class
 *
 * @package AdminWelcomeModal
 */

namespace AWM;

class Render {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_footer', [$this, 'render_modal']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
    }
    
    /**
     * Enqueue scripts and styles for modal
     */
    public function enqueue_scripts() {
        if (!$this->should_show_modal()) {
            return;
        }
        
        wp_enqueue_style(
            'awm-modal',
            AWM_PLUGIN_URL . 'assets/css/admin.css',
            [],
            AWM_VERSION
        );
        
        wp_enqueue_script(
            'awm-modal',
            AWM_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery'],
            AWM_VERSION,
            true
        );
        
        $options = get_option('awm_options', []);
        wp_localize_script('awm-modal', 'awmModalData', [
            'options' => $options,
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('awm_modal_nonce')
        ]);
    }
    
    /**
     * Check if modal should be shown
     */
    private function should_show_modal() {
        // Only show in admin
        if (!is_admin()) {
            return false;
        }
        
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            return false;
        }
        
        $options = get_option('awm_options', []);
        
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
        
        // Check screen restrictions
        if (!empty($options['screens'])) {
            $current_screen = get_current_screen();
            if ($current_screen) {
                $raw = array_filter(array_map('trim', explode("\n", $options['screens'])));
                // Allow both exact screen IDs and partial/filename patterns
                $allowed_screens = array_map('strtolower', $raw);
                $screen_id = strtolower($current_screen->id);
                $base_match = false;
                foreach ($allowed_screens as $pattern) {
                    if ($pattern === '') continue;
                    // Normalize common entries like /upload.php to 'upload'
                    $normalized = $pattern;
                    if (strpos($normalized, '.php') !== false) {
                        $normalized = basename($normalized, '.php');
                    }
                    $normalized = trim($normalized, "/ ");
                    $normalized = strtolower($normalized);
                    if ($normalized !== '' && (strpos($screen_id, $normalized) !== false || $screen_id === $normalized)) {
                        $base_match = true;
                        break;
                    }
                }
                if (!$base_match) {
                    return false;
                }
            }
        }
        
        // Apply filters for developers
        return apply_filters('awm_should_show_modal', true, get_current_screen(), wp_get_current_user());
    }
    
    /**
     * Render the modal HTML
     */
    public function render_modal() {
        if (!$this->should_show_modal()) {
            return;
        }
        
        $options = get_option('awm_options', []);
        
        // Apply filters for developers
        $options = apply_filters('awm_modal_options', $options);
        
        // Build CSS variables for colors
        $css_vars = $this->build_css_variables($options);
        
        ?>
        <div id="awm-admin-modal-overlay" style="display:none;">
            <div id="awm-admin-modal" style="<?php echo esc_attr($css_vars); ?>">
                <div id="awm-admin-modal-header">
                    <?php echo esc_html($options['title'] ?? __('Welcome', 'admin-welcome-message')); ?>
                </div>
                
                <div id="awm-admin-modal-content">
                    <?php echo wp_kses_post($options['message'] ?? ''); ?>
                </div>
                
                <div id="awm-admin-modal-buttons">
                    <?php
                    $cta_attributes = '';
                    if (!empty($options['cta_new_tab'])) {
                        $cta_attributes = ' target="_blank" rel="noopener noreferrer"';
                    }
                    ?>
                    <?php if (!isset($options['show_cta']) || $options['show_cta']) : ?>
                        <a href="<?php echo esc_url($options['cta_url'] ?? '#'); ?>" 
                           class="awm-modal-btn" 
                           id="awm-access-help-btn"<?php echo $cta_attributes; ?>>
                            <?php echo esc_html($options['cta_text'] ?? __('Access Help', 'admin-welcome-message')); ?>
                        </a>
                    <?php endif; ?>
                    <button class="awm-modal-btn" id="awm-close-modal-btn">
                        <?php _e('Close', 'admin-welcome-message'); ?>
                    </button>
                </div>
                
                <div id="awm-admin-modal-footer">
                    <input type="checkbox" id="awm-hide-session-checkbox">
                    <label for="awm-hide-session-checkbox">
                        <?php echo esc_html($options['footer_note'] ?? __("Don't show this again during my current session", 'admin-welcome-message')); ?>
                    </label>
                </div>
            </div>
        </div>
        <?php
        
        // Do action for developers
        do_action('awm_modal_rendered', $options);
    }
    
    /**
     * Build CSS variables string
     */
    private function build_css_variables($options) {
        $colors = $options['colors'] ?? [];
        $vars = [];
        
        $color_mappings = [
            'header_bg' => '--awm-header-bg',
            'header_text' => '--awm-header-color',
            'body_bg' => '--awm-body-bg',
            'body_text' => '--awm-body-color',
            'footer_bg' => '--awm-footer-bg',
            'footer_text' => '--awm-footer-color',
            'btn_bg' => '--awm-btn-bg',
            'btn_text' => '--awm-btn-color',
            'btn_bg_hover' => '--awm-btn-bg-hover',
            'btn_text_hover' => '--awm-btn-color-hover'
        ];
        
        foreach ($color_mappings as $option_key => $css_var) {
            if (isset($colors[$option_key])) {
                $vars[] = $css_var . ': ' . esc_attr($colors[$option_key]) . ';';
            }
        }
        
        return implode(' ', $vars);
    }
}
