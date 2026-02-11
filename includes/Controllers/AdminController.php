<?php
namespace AdvantagoLocationFinder\Controllers;

class AdminController extends BaseController {

    public function __construct($config) {
        parent::__construct($config);
    }

    public function init() {
        add_action('admin_menu', [$this, 'add_menu_page']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('wp_ajax_alf_clear_cache', [$this, 'clear_cache_callback']);
    }

    public function add_menu_page() {
        add_menu_page(
            __('Location Finder V2', 'advantago-location-finder'),
            __('Location Finder V2', 'advantago-location-finder'),
            'manage_options',
            'advantago-location-finder',
            [$this, 'render_admin_page'],
            'dashicons-location-alt'
        );
    }

    public function register_settings() {
        register_setting('alf_settings_group', 'alf_settings', [
            'sanitize_callback' => [$this, 'sanitize_settings']
        ]);

        add_settings_section(
            'alf_main_section',
            __('Main Settings', 'advantago-location-finder'),
            null,
            'advantago-location-finder'
        );

        // Settings will be handled manually in the template for better control over the layout
    }

    /**
     * Sanitize and validate settings
     *
     * @param array $input
     * @return array
     */
    public function sanitize_settings($input) {
        $sanitized = [];

        // Text fields
        if (isset($input['yext_api_key'])) {
            $sanitized['yext_api_key'] = sanitize_text_field($input['yext_api_key']);
        }
        if (isset($input['google_maps_api_key'])) {
            $sanitized['google_maps_api_key'] = sanitize_text_field($input['google_maps_api_key']);
        }
        if (isset($input['map_service_id'])) {
            $sanitized['map_service_id'] = sanitize_text_field($input['map_service_id']);
        }

        // Numbers and constrained values
        if (isset($input['locations_per_page'])) {
            $val = absint($input['locations_per_page']);
            if ($val < 5) { $val = 5; }
            if ($val > 100) { $val = 100; }
            $sanitized['locations_per_page'] = $val;
        }

        // Page selections
        if (isset($input['detail_page'])) {
            $sanitized['detail_page'] = absint($input['detail_page']);
        }
        if (isset($input['hitlist_page'])) {
            $sanitized['hitlist_page'] = absint($input['hitlist_page']);
        }

        // Cache lifespan - allow only predefined values
        if (isset($input['cache_lifespan'])) {
            $allowed = ['900', '1800', '3600'];
            $val = in_array((string)$input['cache_lifespan'], $allowed, true) ? (string)$input['cache_lifespan'] : '900';
            $sanitized['cache_lifespan'] = $val;
        }

        // Color picker
        if (isset($input['main_color'])) {
            $color = sanitize_hex_color($input['main_color']);
            $sanitized['main_color'] = $color ? $color : '#000000';
        }

        // Checkboxes
        $sanitized['show_logo_detail'] = isset($input['show_logo_detail']) ? 1 : 0;
        $sanitized['show_location_name_detail'] = isset($input['show_location_name_detail']) ? 1 : 0;
        $sanitized['activate_autocomplete'] = isset($input['activate_autocomplete']) ? 1 : 0;
        $sanitized['use_mockdata'] = isset($input['use_mockdata']) ? 1 : 0;

        // Country codes field is disabled in UI; ignore incoming value for safety, set default
        $sanitized['country_codes'] = 'de';

        return $sanitized;
    }

    public function enqueue_scripts($hook) {
        if ($hook !== 'toplevel_page_advantago-location-finder') {
            return;
        }
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        
        // Inline script for color picker and AJAX clear cache
        wp_add_inline_script('wp-color-picker', "
            jQuery(document).ready(function($) {
                $('.alf-color-picker').wpColorPicker();

                $('#alf-clear-cache-btn').on('click', function(e) {
                    e.preventDefault();
                    var btn = $(this);
                    btn.prop('disabled', true);
                    
                    $.post(ajaxurl, {
                        action: 'alf_clear_cache',
                        _ajax_nonce: '" . wp_create_nonce('alf_clear_cache_nonce') . "'
                    }, function(response) {
                        // Per requirement, output an error number: 0 on success, HTTP-like code on error
                        if (response && response.success) {
                            alert('0');
                        } else {
                            var code = (response && response.data) ? response.data : 500;
                            alert(code);
                        }
                        btn.prop('disabled', false);
                    });
                });
            });
        ");
    }

    public function render_admin_page() {
        $options = get_option('alf_settings', []);
        $pages = get_pages();
        
        echo $this->render('admin/admin-settings', [
            'options' => $options,
            'pages' => $pages
        ]);
    }

    public function clear_cache_callback() {
        check_ajax_referer('alf_clear_cache_nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(403);
        }

        global $wpdb;
        $prefix = '_transient_alf_yext_entities_';
        $like = $wpdb->esc_like($prefix) . '%';
        $option_names = $wpdb->get_col($wpdb->prepare("SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s", $like));

        if ($option_names === null) {
            wp_send_json_error(500);
        }

        foreach ($option_names as $option_name) {
            // Extract transient key name from option_name
            $key = substr($option_name, strlen('_transient_'));
            if (!empty($key)) {
                delete_transient($key);
            }
        }

        wp_send_json_success();
    }
}
