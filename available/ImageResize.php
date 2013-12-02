<?php
return function ($template, $context, $args, $source) {
    $variableName = trim(str_replace(['{', '}'], '', $source));
    $image = $context->get($variableName);
    if (empty($image) || !isset($image['url'])) {
    	return '';
    }
    $path = $image['url'];
    $args = str_getcsv(trim($args), ' ');
    $argsCount = count($args);
    $height = 100;
    $width = 100;
    $cropratio = false;
    if ($argsCount > 0 && !empty($args[0])) {
    	$width = array_shift($args);
    }
    if ($argsCount > 1 && !empty($args[0])) {
    	$height = array_shift($args);
    }
    if ($argsCount > 2 && !empty($args[0])) {
    	$cropratio = array_shift($args);
    }
    $container = \Framework\container();
    return '<img src="' . $container->imageResizer->getPath($path, $width, $height) . '" />';
};