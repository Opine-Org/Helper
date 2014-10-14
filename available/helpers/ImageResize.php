<?php
return function ($options, $options=[]) {
    $image = $options['image'];
    if (empty($image) || !is_array($image) || !isset($image['url'])) {
        return '';
    }
    $path = $image['url'];
    $height = 100;
    $width = 100;
    $cropratio = false;
    $tag = true;
    if (in_array('notag', $options)) {
        $tag = false;
        $options = array_diff($options, ['notag']);
    }
    $optionsCount = count($options);
    if ($optionsCount > 0 && !empty($options['width'])) {
        $width = $options['width'];
        if (substr_count($width, '%') == 1) {
            $width = $image['width'] * (((integer)trim($width, '%')) / 100);           
        }
        if ($optionsCount == 1) {
            $height = floor($image['height'] * ($width / $image['width']));
        }
    }
    if ($optionsCount > 1 && !empty($options['height'])) {
        $height = $options['height'];
        if (substr_count($height, '%') == 1) {
            $height = $image['height'] * (((integer)trim($height, '%')) / 100);           
        }
    }
    if ($optionsCount > 2 && !empty($options['cropratio'])) {
        $cropratio = $options['cropratio'];
    }
    $container = \Opine\container();
    $url = $container->imageResizer->getPath($path, $width, $height);
    if ($tag === false) {
        return $url;
    }
    return '<img src="' . $url . '" />';
};