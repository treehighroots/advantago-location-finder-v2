<?php
/**
 * Plugin Name: Advantago Location Finder
 * Description: An object-oriented, MVC-based WordPress plugin for finding locations.
 * Version: 1.0.0
 * Author: Advantago
 * Text Domain: advantago-location-finder
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define constants
define('ALF_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ALF_PLUGIN_URL', plugin_dir_url(__FILE__));

// Autoloader (Simple PSR-4-like)
spl_autoload_register(function ($class) {
    $prefix = 'AdvantagoLocationFinder\\';
    $base_dir = ALF_PLUGIN_DIR . 'includes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Initialize the plugin
function alf_init_plugin() {
    $plugin = new \AdvantagoLocationFinder\Plugin();
    $plugin->run();
}

add_action('plugins_loaded', 'alf_init_plugin');

// Activation, Deactivation, Deletion hooks
register_activation_hook(__FILE__, ['\AdvantagoLocationFinder\Activator', 'activate']);
register_deactivation_hook(__FILE__, ['\AdvantagoLocationFinder\Deactivator', 'deactivate']);
// Uninstall hook is usually in uninstall.php, but the prompt asked for "deletion link" logic.
// In WordPress, register_uninstall_hook or uninstall.php is used.
