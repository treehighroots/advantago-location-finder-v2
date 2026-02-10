<?php
namespace AdvantagoLocationFinder\Controllers;

abstract class BaseController {
    protected $config;

    public function __construct($config) {
        $this->config = $config;
    }

    protected function render($template, $data = []) {
        // Automatically inject global config into data
        $data['config'] = $this->config;

        extract($data);

        $template_path = ALF_PLUGIN_DIR . 'templates/' . $template . '.php';

        if (file_exists($template_path)) {
            ob_start();
            include $template_path;
            return ob_get_clean();
        }

        return "Template $template not found.";
    }
}
