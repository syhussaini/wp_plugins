<?php
/**
 * Settings Class
 *
 * @package AdminWelcomeModal
 */

namespace AWM;

class Settings {
    
    /**
     * Option name
     */
    private $option_name = 'awm_options';
    
    /**
     * Initialize settings
     */
    public function init() {
        register_setting(
            'awm_options_group',
            $this->option_name,
            [$this, 'sanitize_options']
        );
        
        $this->add_settings_sections();
        $this->add_settings_fields();
    }
    
    /**
     * Add settings sections
     */
    private function add_settings_sections() {
        add_settings_section(
            'awm_content_section',
            __('Content Settings', 'admin-welcome-message'),
            [$this, 'render_content_section'],
            'admin-welcome-message'
        );
        
        add_settings_section(
            'awm_behavior_section',
            __('Behavior Settings', 'admin-welcome-message'),
            [$this, 'render_behavior_section'],
            'admin-welcome-message'
        );
        
        add_settings_section(
            'awm_appearance_section',
            __('Appearance Settings', 'admin-welcome-message'),
            [$this, 'render_appearance_section'],
            'admin-welcome-message'
        );
        
        add_settings_section(
            'awm_targeting_section',
            __('Targeting Settings', 'admin-welcome-message'),
            [$this, 'render_targeting_section'],
            'admin-welcome-message'
        );
        
        add_settings_section(
            'awm_preview_section',
            __('Live Preview', 'admin-welcome-message'),
            [$this, 'render_preview_section'],
            'admin-welcome-message'
        );
    }
    
    /**
     * Add settings fields
     */
    private function add_settings_fields() {
        // Content fields
        add_settings_field(
            'awm_title',
            __('Modal Title', 'admin-welcome-message'),
            [$this, 'render_text_field'],
            'admin-welcome-message',
            'awm_content_section',
            ['field' => 'title', 'description' => __('The title displayed in the modal header.', 'admin-welcome-message')]
        );
        
        add_settings_field(
            'awm_message',
            __('Modal Message', 'admin-welcome-message'),
            [$this, 'render_editor_field'],
            'admin-welcome-message',
            'awm_content_section',
            ['field' => 'message', 'description' => __('The main message content. HTML is allowed.', 'admin-welcome-message')]
        );
        
        add_settings_field(
            'awm_cta_text',
            __('CTA Button Text', 'admin-welcome-message'),
            [$this, 'render_text_field'],
            'admin-welcome-message',
            'awm_content_section',
            ['field' => 'cta_text', 'description' => __('Text for the call-to-action button.', 'admin-welcome-message')]
        );
        
        add_settings_field(
            'awm_cta_url',
            __('CTA Button URL', 'admin-welcome-message'),
            [$this, 'render_url_field'],
            'admin-welcome-message',
            'awm_content_section',
            ['field' => 'cta_url', 'description' => __('URL for the call-to-action button.', 'admin-welcome-message')]
        );
        
        add_settings_field(
            'awm_footer_note',
            __('Footer Note', 'admin-welcome-message'),
            [$this, 'render_text_field'],
            'admin-welcome-message',
            'awm_content_section',
            ['field' => 'footer_note', 'description' => __('Text for the footer checkbox.', 'admin-welcome-message')]
        );
        
        // Behavior fields
        add_settings_field(
            'awm_dismiss_mode',
            __('Dismissal Mode', 'admin-welcome-message'),
            [$this, 'render_radio_field'],
            'admin-welcome-message',
            'awm_behavior_section',
            [
                'field' => 'dismiss_mode',
                'options' => [
                    'session' => __('Per Session (until logout/tab closed)', 'admin-welcome-message'),
                    'cooldown' => __('Cooldown Minutes', 'admin-welcome-message')
                ],
                'description' => __('How the modal should behave when dismissed.', 'admin-welcome-message')
            ]
        );
        
        add_settings_field(
            'awm_cooldown_minutes',
            __('Cooldown Minutes', 'admin-welcome-message'),
            [$this, 'render_number_field'],
            'admin-welcome-message',
            'awm_behavior_section',
            [
                'field' => 'cooldown_minutes',
                'min' => 1,
                'max' => 1440,
                'description' => __('Minutes to wait before showing the modal again (1-1440).', 'admin-welcome-message')
            ]
        );
        
        add_settings_field(
            'awm_close_on_esc',
            __('Close on ESC Key', 'admin-welcome-message'),
            [$this, 'render_checkbox_field'],
            'admin-welcome-message',
            'awm_behavior_section',
            ['field' => 'close_on_esc', 'description' => __('Allow closing the modal with the ESC key.', 'admin-welcome-message')]
        );
        
        add_settings_field(
            'awm_close_on_cta',
            __('Close on CTA Click', 'admin-welcome-message'),
            [$this, 'render_checkbox_field'],
            'admin-welcome-message',
            'awm_behavior_section',
            ['field' => 'close_on_cta', 'description' => __('Close the modal when CTA button is clicked.', 'admin-welcome-message')]
        );
        
        add_settings_field(
            'awm_cta_new_tab',
            __('Open CTA in New Tab', 'admin-welcome-message'),
            [$this, 'render_checkbox_field'],
            'admin-welcome-message',
            'awm_behavior_section',
            ['field' => 'cta_new_tab', 'description' => __('Open the CTA link in a new tab.', 'admin-welcome-message')]
        );
        
        // Appearance fields
        $this->add_color_fields();
        
        // Targeting fields
        add_settings_field(
            'awm_roles',
            __('Restrict to User Roles', 'admin-welcome-message'),
            [$this, 'render_roles_field'],
            'admin-welcome-message',
            'awm_targeting_section',
            ['field' => 'roles', 'description' => __('Select roles that should see the modal. Leave empty for all roles.', 'admin-welcome-message')]
        );
        
        add_settings_field(
            'awm_screens',
            __('Restrict to Admin Screens', 'admin-welcome-message'),
            [$this, 'render_textarea_field'],
            'admin-welcome-message',
            'awm_targeting_section',
            [
                'field' => 'screens',
                'description' => __('Enter screen IDs (one per line) to restrict modal display. Leave empty for all screens.', 'admin-welcome-message'),
                'placeholder' => 'dashboard\npost\nedit-post\npage\nedit-page'
            ]
        );
    }
    
    /**
     * Add color fields
     */
    private function add_color_fields() {
        $color_fields = [
            'header_bg' => __('Header Background', 'admin-welcome-message'),
            'header_text' => __('Header Text', 'admin-welcome-message'),
            'body_bg' => __('Body Background', 'admin-welcome-message'),
            'body_text' => __('Body Text', 'admin-welcome-message'),
            'footer_bg' => __('Footer Background', 'admin-welcome-message'),
            'footer_text' => __('Footer Text', 'admin-welcome-message'),
            'btn_bg' => __('Button Background', 'admin-welcome-message'),
            'btn_text' => __('Button Text', 'admin-welcome-message'),
            'btn_bg_hover' => __('Button Hover Background', 'admin-welcome-message'),
            'btn_text_hover' => __('Button Hover Text', 'admin-welcome-message')
        ];
        
        foreach ($color_fields as $field => $label) {
            add_settings_field(
                'awm_color_' . $field,
                $label,
                [$this, 'render_color_field'],
                'admin-welcome-message',
                'awm_appearance_section',
                ['field' => 'colors.' . $field, 'description' => sprintf(__('Color for %s.', 'admin-welcome-message'), strtolower($label))]
            );
        }
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        $options = get_option($this->option_name, []);
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('awm_options_group');
                do_settings_sections('admin-welcome-message');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Render text field
     */
    public function render_text_field($args) {
        $options = get_option($this->option_name, []);
        $field = $args['field'];
        $value = isset($options[$field]) ? $options[$field] : '';
        $description = isset($args['description']) ? $args['description'] : '';
        
        echo '<input type="text" id="awm_' . esc_attr($field) . '" name="' . esc_attr($this->option_name) . '[' . esc_attr($field) . ']" value="' . esc_attr($value) . '" class="regular-text" />';
        if ($description) {
            echo '<p class="description">' . esc_html($description) . '</p>';
        }
    }
    
    /**
     * Render URL field
     */
    public function render_url_field($args) {
        $options = get_option($this->option_name, []);
        $field = $args['field'];
        $value = isset($options[$field]) ? $options[$field] : '';
        $description = isset($args['description']) ? $args['description'] : '';
        
        echo '<input type="url" id="awm_' . esc_attr($field) . '" name="' . esc_attr($this->option_name) . '[' . esc_attr($field) . ']" value="' . esc_url($value) . '" class="regular-text" />';
        if ($description) {
            echo '<p class="description">' . esc_html($description) . '</p>';
        }
    }
    
    /**
     * Render number field
     */
    public function render_number_field($args) {
        $options = get_option($this->option_name, []);
        $field = $args['field'];
        $value = isset($options[$field]) ? $options[$field] : '';
        $min = isset($args['min']) ? $args['min'] : '';
        $max = isset($args['max']) ? $args['max'] : '';
        $description = isset($args['description']) ? $args['description'] : '';
        
        $attributes = '';
        if ($min !== '') $attributes .= ' min="' . esc_attr($min) . '"';
        if ($max !== '') $attributes .= ' max="' . esc_attr($max) . '"';
        
        echo '<input type="number" id="awm_' . esc_attr($field) . '" name="' . esc_attr($this->option_name) . '[' . esc_attr($field) . ']" value="' . esc_attr($value) . '" class="small-text"' . $attributes . ' />';
        if ($description) {
            echo '<p class="description">' . esc_html($description) . '</p>';
        }
    }
    
    /**
     * Render checkbox field
     */
    public function render_checkbox_field($args) {
        $options = get_option($this->option_name, []);
        $field = $args['field'];
        $checked = isset($options[$field]) && $options[$field] ? 'checked' : '';
        $description = isset($args['description']) ? $args['description'] : '';
        
        echo '<input type="checkbox" id="awm_' . esc_attr($field) . '" name="' . esc_attr($this->option_name) . '[' . esc_attr($field) . ']" value="1" ' . $checked . ' />';
        echo '<label for="awm_' . esc_attr($field) . '">' . esc_html($description) . '</label>';
    }
    
    /**
     * Render radio field
     */
    public function render_radio_field($args) {
        $options = get_option($this->option_name, []);
        $field = $args['field'];
        $current_value = isset($options[$field]) ? $options[$field] : '';
        $radio_options = $args['options'];
        $description = isset($args['description']) ? $args['description'] : '';
        
        foreach ($radio_options as $value => $label) {
            $checked = ($current_value === $value) ? 'checked' : '';
            echo '<label><input type="radio" name="' . esc_attr($this->option_name) . '[' . esc_attr($field) . ']" value="' . esc_attr($value) . '" ' . $checked . ' /> ' . esc_html($label) . '</label><br>';
        }
        if ($description) {
            echo '<p class="description">' . esc_html($description) . '</p>';
        }
    }
    
    /**
     * Render color field
     */
    public function render_color_field($args) {
        $options = get_option($this->option_name, []);
        $field_path = explode('.', $args['field']);
        $value = '';
        
        if (count($field_path) === 2) {
            $value = isset($options[$field_path[0]][$field_path[1]]) ? $options[$field_path[0]][$field_path[1]] : '';
        }
        
        $description = isset($args['description']) ? $args['description'] : '';
        
        echo '<input type="text" id="awm_' . esc_attr(str_replace('.', '_', $args['field'])) . '" name="' . esc_attr($this->option_name) . '[' . esc_attr($field_path[0]) . '][' . esc_attr($field_path[1]) . ']" value="' . esc_attr($value) . '" class="awm-color-picker" />';
        if ($description) {
            echo '<p class="description">' . esc_html($description) . '</p>';
        }
    }
    
    /**
     * Render roles field
     */
    public function render_roles_field($args) {
        $options = get_option($this->option_name, []);
        $field = $args['field'];
        $selected_roles = isset($options[$field]) ? $options[$field] : [];
        $description = isset($args['description']) ? $args['description'] : '';
        
        $roles = wp_roles()->get_names();
        
        foreach ($roles as $role_value => $role_name) {
            $checked = in_array($role_value, $selected_roles) ? 'checked' : '';
            echo '<label><input type="checkbox" name="' . esc_attr($this->option_name) . '[' . esc_attr($field) . '][]" value="' . esc_attr($role_value) . '" ' . $checked . ' /> ' . esc_html($role_name) . '</label><br>';
        }
        if ($description) {
            echo '<p class="description">' . esc_html($description) . '</p>';
        }
    }
    
    /**
     * Render textarea field
     */
    public function render_textarea_field($args) {
        $options = get_option($this->option_name, []);
        $field = $args['field'];
        $value = isset($options[$field]) ? $options[$field] : '';
        $description = isset($args['description']) ? $args['description'] : '';
        $placeholder = isset($args['placeholder']) ? $args['placeholder'] : '';
        
        echo '<textarea id="awm_' . esc_attr($field) . '" name="' . esc_attr($this->option_name) . '[' . esc_attr($field) . ']" rows="4" cols="50" class="large-text" placeholder="' . esc_attr($placeholder) . '">' . esc_textarea($value) . '</textarea>';
        if ($description) {
            echo '<p class="description">' . esc_html($description) . '</p>';
        }
    }
    
    /**
     * Render editor field
     */
    public function render_editor_field($args) {
        $options = get_option($this->option_name, []);
        $field = $args['field'];
        $value = isset($options[$field]) ? $options[$field] : '';
        $description = isset($args['description']) ? $args['description'] : '';
        
        wp_editor($value, 'awm_' . $field, [
            'textarea_name' => $this->option_name . '[' . $field . ']',
            'textarea_rows' => 5,
            'media_buttons' => false,
            'teeny' => true,
            'tinymce' => [
                'toolbar1' => 'bold,italic,underline,link,unlink,bullist,numlist',
                'toolbar2' => '',
                'toolbar3' => ''
            ]
        ]);
        
        if ($description) {
            echo '<p class="description">' . esc_html($description) . '</p>';
        }
    }
    
    /**
     * Render section descriptions
     */
    public function render_content_section() {
        echo '<p>' . esc_html__('Configure the content and appearance of your admin welcome modal.', 'admin-welcome-message') . '</p>';
    }
    
    public function render_behavior_section() {
        echo '<p>' . esc_html__('Control how the modal behaves when users interact with it.', 'admin-welcome-message') . '</p>';
    }
    
    public function render_appearance_section() {
        echo '<p>' . esc_html__('Customize the colors and visual appearance of your modal.', 'admin-welcome-message') . '</p>';
    }
    
    public function render_targeting_section() {
        echo '<p>' . esc_html__('Control which users and admin screens should display the modal.', 'admin-welcome-message') . '</p>';
    }
    
    public function render_preview_section() {
        echo '<p>' . esc_html__('Preview your modal with current settings (changes are not saved until you click Save Changes).', 'admin-welcome-message') . '</p>';
        echo '<div id="awm-preview-container"></div>';
    }
    
    /**
     * Sanitize options
     */
    public function sanitize_options($input) {
        $sanitized = [];
        
        // Text fields
        $text_fields = ['title', 'cta_text', 'footer_note'];
        foreach ($text_fields as $field) {
            if (isset($input[$field])) {
                $sanitized[$field] = sanitize_text_field($input[$field]);
            }
        }
        
        // URL field
        if (isset($input['cta_url'])) {
            $sanitized['cta_url'] = esc_url_raw($input['cta_url']);
        }
        
        // Message field (allow safe HTML)
        if (isset($input['message'])) {
            $sanitized['message'] = wp_kses_post($input['message']);
        }
        
        // Dismiss mode
        if (isset($input['dismiss_mode']) && in_array($input['dismiss_mode'], ['session', 'cooldown'])) {
            $sanitized['dismiss_mode'] = $input['dismiss_mode'];
        }
        
        // Cooldown minutes
        if (isset($input['cooldown_minutes'])) {
            $sanitized['cooldown_minutes'] = absint($input['cooldown_minutes']);
            if ($sanitized['cooldown_minutes'] < 1) $sanitized['cooldown_minutes'] = 1;
            if ($sanitized['cooldown_minutes'] > 1440) $sanitized['cooldown_minutes'] = 1440;
        }
        
        // Checkbox fields
        $checkbox_fields = ['cta_new_tab', 'close_on_esc', 'close_on_cta'];
        foreach ($checkbox_fields as $field) {
            $sanitized[$field] = isset($input[$field]) ? true : false;
        }
        
        // Roles
        if (isset($input['roles']) && is_array($input['roles'])) {
            $sanitized['roles'] = array_map('sanitize_text_field', $input['roles']);
        } else {
            $sanitized['roles'] = [];
        }
        
        // Screens
        if (isset($input['screens'])) {
            $sanitized['screens'] = sanitize_textarea_field($input['screens']);
        }
        
        // Colors
        if (isset($input['colors']) && is_array($input['colors'])) {
            $sanitized['colors'] = [];
            $color_fields = ['header_bg', 'header_text', 'body_bg', 'body_text', 'footer_bg', 'footer_text', 'btn_bg', 'btn_text', 'btn_bg_hover', 'btn_text_hover'];
            foreach ($color_fields as $color_field) {
                if (isset($input['colors'][$color_field])) {
                    $sanitized['colors'][$color_field] = sanitize_hex_color($input['colors'][$color_field]);
                }
            }
        }
        
        return $sanitized;
    }
}
