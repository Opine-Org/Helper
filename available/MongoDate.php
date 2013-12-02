<?php
return function ($template, $context, $args, $source) {
	$args = str_getcsv(trim($args), ' ');
    $argsCount = count($args);
    if ($argsCount == 0) {
    	return '';
    }
    $variableName = array_shift($args);
    $format = 'm/d/Y';
    if ($argsCount > 1) {
    	$format = array_shift($args);
    }
    $date = $context->get($variableName);
    if (!isset($date['sec'])) {
    	return '';
    }
    return date($format, $date['sec']);
};