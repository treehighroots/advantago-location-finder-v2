<?php
namespace AdvantagoLocationFinder\Controllers;

use AdvantagoLocationFinder\Services\YextApiClient;

class YextEntitiesController extends BaseController {
    public function render($atts) {
        $client = new YextApiClient($this->config);
        // Allow optional shortcode atts to be forwarded as query params
        $atts = shortcode_atts(array(), $atts, 'alf_yext_entities');

        $result = $client->fetchEntities($atts);

        return parent::render('yext-entities', [
            'result' => $result,
        ]);
    }
}
