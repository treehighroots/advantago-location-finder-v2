<?php
namespace AdvantagoLocationFinder;

class Deactivator {
    public static function deactivate() {
        // Deactivation logic
        flush_rewrite_rules();
    }
}
