<?php
return function ($args, $options) {
    $id = $args[0];
    $url = false;
    if (substr_count($id, 'http')) {
    	$match = [];
    	if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $id, $match)) {
            if (!empty($match[1])) {
                $id = $match[1];
            }
        }
    }
    if (!isset($options['width'])) {
        $options['width'] = 560;
    }
    if (!isset($options['height'])) {
        $options['height'] = 315;
    }
    return '<iframe width="' . $options['width'] . '" height="' . $options['height'] . '" src="https:/www.youtube.com/embed/' . $id . '" frameborder="0" allowfullscreen></iframe>';
};