<?php
return function ($arguments, $options) {
    $db = \Opine\container()->get('db');
    if (isset($options['title'])) {
        $blurb = $db->collection('partials')->findOne(['title' => $options['title']]);
    } elseif (isset($options['code'])) {
        $blurb = $db->collection('partials')->findOne(['code_name' => $options['code']]);
    } else {
        return '';
    }
    if (isset($blurb['_id'])) {
        return $blurb['body'];
    }
    return '';
};