<?php
namespace AdvantagoLocationFinder;

class Plugin {
    protected $config;

    public function __construct() {
        $this->load_config();
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
        $location_finder_controller = new Controllers\LocationFinderController($this->config);
        $yext_entities_controller = new Controllers\YextEntitiesController($this->config);

        add_shortcode('alf_location_finder', [$location_finder_controller, 'render']);
        add_shortcode('alf_yext_entities', [$yext_entities_controller, 'render']);
    }
}
