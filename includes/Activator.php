<?php
namespace AdvantagoLocationFinder;

class Activator {
    public static function activate() {
        // Activation logic
        flush_rewrite_rules();
    }
}
