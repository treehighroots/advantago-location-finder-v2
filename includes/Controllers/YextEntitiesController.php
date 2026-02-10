<?php
namespace AdvantagoLocationFinder\Controllers;

use AdvantagoLocationFinder\Services\YextApiClient;

class YextEntitiesController extends BaseController {
    public function handleShortcode($atts) {
        $client = new YextApiClient($this->config);
        // Allow optional shortcode atts to be forwarded as query params
        $atts = shortcode_atts(array(), $atts, 'alf_yext_entities');

        // Sanitize all attribute values to prevent injection
        $safe_atts = array();
        foreach ($atts as $k => $v) {
            $safe_atts[sanitize_key($k)] = is_array($v)
                ? array_map('sanitize_text_field', $v)
                : sanitize_text_field($v);
        }

        $result = $client->fetchEntities($safe_atts);

        return parent::render('yext-entities', [
            'result' => $result,
        ]);
    }
}
