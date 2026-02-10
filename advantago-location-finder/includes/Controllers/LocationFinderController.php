<?php
namespace AdvantagoLocationFinder\Controllers;

class LocationFinderController extends BaseController {
    public function render($atts) {
        return parent::render('hello-world', [
            'message' => 'hello world'
        ]);
    }
}
