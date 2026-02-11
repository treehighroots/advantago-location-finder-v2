<?php
namespace AdvantagoLocationFinder\Models;

use AdvantagoLocationFinder\Services\YextApiClientService;

class LocationModel {

    private $config;

    public function __construct($config) {
        $this->config = $config;
    }

    /**
     * Get all locations - either from mockdata or API
     *
     * @param array $params Optional query params for API
     * @return array Result with success, data, source, error keys
     */
    public function getAll($params = []) {
        $options = get_option('alf_settings', []);
        $use_mockdata = !empty($options['use_mockdata']);

        if ($use_mockdata) {
            return $this->getFromMockdata();
        }

        return $this->getFromApi($params);
    }

    /**
     * Load locations from mockdata JSON file
     *
     * @return array
     */
    private function getFromMockdata() {
        $mockdata_path = ALF_PLUGIN_DIR . '_mockdata/mockdata-locations.json';
        
        if (file_exists($mockdata_path)) {
            $json = file_get_contents($mockdata_path);
            $data = json_decode($json, true);
            return [
                'success' => true,
                'data' => isset($data['response']) ? $data['response'] : $data,
                'source' => 'mockdata',
            ];
        }

        return [
            'success' => false,
            'error' => 'Mockdata file not found.',
        ];
    }

    /**
     * Fetch locations from Yext API
     *
     * @param array $params Query params
     * @return array
     */
    private function getFromApi($params = []) {
        $client = new YextApiClientService($this->config);

        // Sanitize all attribute values to prevent injection
        $safe_params = [];
        foreach ($params as $k => $v) {
            $safe_params[sanitize_key($k)] = is_array($v)
                ? array_map('sanitize_text_field', $v)
                : sanitize_text_field($v);
        }

        return $client->fetchEntities($safe_params);
    }
}
