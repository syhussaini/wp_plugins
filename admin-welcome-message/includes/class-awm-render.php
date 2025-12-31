<?php
/**
 * Render Class
 *
 * @package AdminWelcomeModal
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

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
            'options' => $options
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
                    <?php echo esc_html($options['title'] ?? __('Welcome', 'zaha-admin-welcome-message')); ?>
                </div>
                
                <div id="awm-admin-modal-content">
                    <?php echo wp_kses_post($options['message'] ?? ''); ?>
                </div>
                
                <div id="awm-admin-modal-buttons">
                    <?php
                    $open_new = !empty($options['cta_new_tab']);
                    ?>
                    <?php if (!isset($options['show_cta']) || $options['show_cta']) : ?>
                        <a href="<?php echo esc_url($options['cta_url'] ?? '#'); ?>" 
                           class="awm-modal-btn" 
                           id="awm-access-help-btn"<?php if ($open_new) { printf(' target="%s" rel="%s"', esc_attr('_blank'), esc_attr('noopener noreferrer')); } ?>>
                            <?php echo esc_html($options['cta_text'] ?? __('Access Help', 'zaha-admin-welcome-message')); ?>
                        </a>
                    <?php endif; ?>
                    <button class="awm-modal-btn" id="awm-close-modal-btn">
                        <?php echo esc_html__('Close', 'zaha-admin-welcome-message'); ?>
                    </button>
                </div>
                
                <div id="awm-admin-modal-footer">
                    <input type="checkbox" id="awm-hide-session-checkbox">
                    <label for="awm-hide-session-checkbox">
                        <?php echo esc_html($options['footer_note'] ?? __("Don't show this again during my current session", 'zaha-admin-welcome-message')); ?>
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
