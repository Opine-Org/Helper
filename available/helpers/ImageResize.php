<?php
return function ($image, $options=[]) {
//    var_dump(func_get_args());
    print_r(func_get_args());
   exit;
    if (empty($image) || !is_array($image) || !isset($image['url'])) {
        return 'x';
    }
    $path = $image['url'];
    $height = 100;
    $width = 100;
    $cropratio = false;
    $tag = true;
    if (in_array('notag', $options)) {
        $tag = false;
        $args = array_diff($args, ['notag']);
    }
    $argsCount = count($options);
    if ($argsCount > 0 && !empty($args['width'])) {
        $width = $args['width'];
        if (substr_count($width, '%') == 1) {
            $width = $image['width'] * (((integer)trim($width, '%')) / 100);           
        }
        if ($argsCount == 1) {
            $height = floor($image['height'] * ($width / $image['width']));
        }
    }
    if ($argsCount > 1 && !empty($args['height'])) {
        $height = $args['height'];
        if (substr_count($height, '%') == 1) {
            $height = $image['height'] * (((integer)trim($height, '%')) / 100);           
        }
    }
    if ($argsCount > 2 && !empty($args['cropratio'])) {
        $cropratio = $args['cropratio'];
    }
    //$container = \Opine\container();
    //$url = $container->imageResizer->getPath($path, $width, $height);
    $url = $height . ',' . $width . ',' . $cropratio;
    if ($tag === false) {
        return $url;
    }
    return '<img src="' . $url . '" />';
};