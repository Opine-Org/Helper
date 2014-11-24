<?php
namespace Helper;

class Capitalize {
    public function render ($args) {
        $word = $args[0];
        if (is_string($word)) {
            return ucfirst($word);
        }
        return '';
    }
}