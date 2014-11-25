<?php
namespace Helper;

class BooleanReadable {
    public function render (Array $args, Array $options) {
    	$variable = $args[0];
        if ($variable == 't') {
            return 'Yes';
        } elseif ($variable == 'f') {
            return 'No';
        } elseif ($variable === true || $variable === 1 || $variable === "1") {
            return 'Yes';
        } else {
            return 'No';
        }
    }
}