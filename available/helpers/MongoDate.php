<?php
return function ($dateObject, $options=[]) {
    //usage: {{MongoDate field format="m/d/Y"}}
    $format = 'm/d/Y';
    if (isset($options['format'])) {
        $format = $options['format'];
    }
    if (!isset($dateObject['sec'])) {
        return date($format, strtotime($dateObject));
    }
    return date($format, $dateObject['sec']);
};