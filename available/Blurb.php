<?php
return function ($template, $context, $args, $source) {
    $db = \Opine\container()->db;
    $args = $template->htmlArgsToArray($args);
    if (isset($args['title'])) {
        $blurb = $db->collection('blurbs')->findOne(['title' => $args['title']]);
    } elseif (isset($args['tag'])) {
        $blurb = $db->collection('blurbs')->findOne(['tags' => $args['tag']]);
    } else {
        return '';
    }
    if (isset($blurb['_id'])) {
        return $blurb['body'];
    }
    return '';
};