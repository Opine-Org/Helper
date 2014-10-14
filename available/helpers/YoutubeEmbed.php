<?php
return function ($args, $options) {
    $id = $args[0];
    if (!isset($options['width'])) {
        $options['width'] = 560;
    }
    if (!isset($options['height'])) {
        $options['height'] = 315;
    }
    return '<iframe width="' . $options['width'] . '" height="' . $options['height'] . '" src="https//www.youtube.com/embed/' . $id . '" frameborder="0" allowfullscreen></iframe>';
};