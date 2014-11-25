<?php
namespace Helper;

class MongoDate {
    public function render (Array $args, Array $options) {
    	//usage: {{MongoDate field format="m/d/Y"}}
        $dateObject = $args[0];
        $format = 'm/d/Y';
        if (isset($options['format'])) {
            $format = $options['format'];
        }
        if (is_object($dateObject)) {
            return date($format, $dateObject->sec);
        }
        if (is_array($dateObject)) {
            if (isset($dateObject['sec'])) {
                return date($format, $dateObject['sec']);
            } else {
                return '[Array]';
            }
        }
        return date($format, strtotime($dateObject));
    }
}