<?php
namespace AdvantagoLocationFinder;

class Plugin {
    protected $config;

    public function __construct() {
        $this->load_config();
    }

    public function load_textdomain() {
        load_plugin_textdomain('advantago-location-finder', false, dirname(plugin_basename(ALF_PLUGIN_DIR . 'advantago-location-finder.php')) . '/languages');
    }

    protected function load_config() {
        $config_file = ALF_PLUGIN_DIR . 'config/config.php';
        if (file_exists($config_file)) {
            $this->config = require $config_file;
        } else {
            // Fallback to example config to avoid fatal errors in development
            $example = ALF_PLUGIN_DIR . 'config/config.example.php';
            $this->config = file_exists($example) ? require $example : [];
        }
    }

    public function run() {
        add_action('init', [$this, 'load_textdomain']);

        $admin_controller = new Controllers\AdminController($this->config);
        $admin_controller->init();

        $location_finder_controller = new Controllers\LocationFinderController($this->config);
        $yext_entities_controller = new Controllers\YextEntitiesController($this->config);

        add_shortcode('alf_yext_entities', [$yext_entities_controller, 'handleShortcode']);
    }
}
