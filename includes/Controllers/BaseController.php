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

        // Prevent overwriting existing variables in the scope
        if (is_array($data)) {
            extract($data, EXTR_SKIP);
        }

        // Resolve template path safely (controller controls $template)
        $template = preg_replace('/[^a-zA-Z0-9\-\_\/]/', '', (string)$template);
        $template_path = ALF_PLUGIN_DIR . 'templates/' . $template . '.php';

        if (file_exists($template_path)) {
            ob_start();
            include $template_path;
            return ob_get_clean();
        }

        return "Template $template not found.";
    }

}
