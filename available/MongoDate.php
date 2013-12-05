<?php
return function ($template, $context, $args, $source) {
	$args = str_getcsv(trim($args), ' ');
    $argsCount = count($args);
    $variableName = trim(str_replace(['{', '}'], '', $source));
    $format = 'm/d/Y';
    if ($argsCount > 0 && !empty($args[0])) {
    	$format = array_shift($args);
    }
    $date = $context->get($variableName);
    if (!isset($date['sec'])) {
    	return date($format, strtotime($date));
    }
    return date($format, $date['sec']);
};