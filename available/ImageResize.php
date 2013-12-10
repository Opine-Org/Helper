<?php
return function ($template, $context, $args, $source) {
    $variableName = trim(str_replace(['{', '}'], '', $source));
    $image = $context->get($variableName);
    if (empty($image) || !isset($image['url'])) {
    	return '';
    }
    $path = $image['url'];
    $args = str_getcsv(trim($args), ' ');
    $height = 100;
    $width = 100;
    $cropratio = false;
    $tag = true;
    if (in_array('notag', $args)) {
        $tag = false;
        $args = array_diff($args, ['notag']);
    }
    $argsCount = count($args);
    if ($argsCount > 0 && !empty($args[0])) {
    	$width = array_shift($args);
        if (substr_count($width, '%') == 1) {
            $width = $image['width'] * (((integer)trim($width, '%')) / 100);           
        }
        if ($argsCount == 1) {
            $height = floor($image['height'] * ($width / $image['width']));
        }
    }
    if ($argsCount > 1 && !empty($args[0])) {
    	$height = array_shift($args);
        if (substr_count($height, '%') == 1) {
            $height = $image['height'] * (((integer)trim($height, '%')) / 100);           
        }
    }
    if ($argsCount > 2 && !empty($args[0])) {
    	$cropratio = array_shift($args);
    }
    $container = \Framework\container();
    $url = $container->imageResizer->getPath($path, $width, $height);
    if ($tag === false) {
        return $url;
    }
    return '<img src="' . $url . '" />';
};