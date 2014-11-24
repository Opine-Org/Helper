<?php
return function ($arguments, $options) {
    $db = \Opine\container()->get('db');
    if (isset($options['title'])) {
        $blurb = $db->collection('blurbs')->findOne(['title' => $options['title']]);
    } elseif (isset($options['tag'])) {
        $blurb = $db->collection('blurbs')->findOne(['tags' => $options['tag']]);
    } else {
        return '';
    }
    if (isset($blurb['_id'])) {
        return $blurb['body'];
    }
    return '';
};