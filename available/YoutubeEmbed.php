<?php
return function ($template, $context, $args, $source) {
	$args = str_getcsv(trim($args), ' ');
    $argsCount = count($args);
    $variableName = trim(str_replace(['{', '}'], '', $source));
    $width = 560;
    $height = 315;
    if ($argsCount > 0) {
    	$width = array_shift($args);
    }
    if ($argsCount > 0) {
    	$height = array_shift($args);
    }
    $id = $context->get($variableName);
    if (!isset($date['sec'])) {
    	return date($format, strtotime($date));
    }
    return '<iframe width="' . $width . '" height="' . $height. '" src="//www.youtube.com/embed/' . $id . '" frameborder="0" allowfullscreen></iframe>';
};