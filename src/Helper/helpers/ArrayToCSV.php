<?php
namespace Helper;

class ArrayToCSV {
    public function render (Array $args, Array $options) {
        if (!is_array($args[0])) {
            return '';
        }
        return implode(', ', $args[0]);
    }
}