<?php
/**
 * Uninstall Plugin
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('alf_settings');

// Clear Yext transients created by the plugin
global $wpdb;
$prefix = '_transient_alf_yext_entities_';
$like = $wpdb->esc_like($prefix) . '%';
$option_names = $wpdb->get_col($wpdb->prepare("SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s", $like));
if (is_array($option_names)) {
    foreach ($option_names as $option_name) {
        $key = substr($option_name, strlen('_transient_'));
        if (!empty($key)) {
            delete_transient($key);
        }
    }
}
