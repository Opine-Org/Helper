<?php
return function ($args, $options=[]) {
	//usage: {{MongoDate field format="m/d/Y"}}
	$dateObject = $args[0];
    $format = 'm/d/Y';
    if (isset($options['format'])) {
        $format = $options['format'];
    }
    if (!is_object($dateObject)) {
        return date($format, strtotime($dateObject));
    }
    return date($format, $dateObject->sec);
};