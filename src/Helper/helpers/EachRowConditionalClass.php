<?php
namespace Helper;

class EachRowConditionalClass {
    public function render ($args, $options) {
        $index = (int)$args[0];
        if (!isset($options['modulus'])) {
            $options['modulus'] = 2;
        }
        if (!isset($options['class'])) {
            $options['class'] = 'even';
        }
        if (!isset($options['otherclass'])) {
            $options['otherclass'] = '';
        }
        if (($index + 1) % $options['modulus'] == 0) {
            return $options['class'];
        }
        return $options['otherclass'];
    }
}