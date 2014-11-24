<?php
namespace Helper;

class ArrayToCSV {
    public function render ($args, $options) {
        if (!is_array($args[0])) {
            return '';
        }
        return implode(', ', $args[0]);
    }
}