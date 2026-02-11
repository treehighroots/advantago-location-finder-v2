<?php
namespace AdvantagoLocationFinder\Controllers;

use AdvantagoLocationFinder\Models\LocationModel;

class LocationsController extends BaseController {

    public function handleShortcodeList($atts) {
        $atts = shortcode_atts(array(), $atts, 'alf_locations');
        
        $model = new LocationModel($this->config);
        $result = $model->getAll($atts);

        return parent::render('frontend/locations-list', [
            'result' => $result,
        ]);
    }
}
