<?php
return function ($template, $context, $args, $source) {
    $variableName = trim(str_replace(['{', '}'], '', $source));
    $categories = $context->get($variableName);
    if (empty($categories) || !is_array($categories)) {
        return '';
    }
    $categoryNames = [];
    $container = \Framework\container();
    $db = $container->db;
    foreach ($categories as $id) {
        $key = 'categories-' . (string)$id;
        $name = \Framework\Framework::keyGet($key);
        if ($name !== false) {
            $categoryNames[] = $name;
        } else {
            $found = $db->collection('categories')->findOne(['_id' => $db->id($id)], ['title']);
            if (isset($found['_id'])) {
                $categoryNames[] = $found['title'];
                \Framework\Framework::keySet($key, $found['title']);
            }
        }
    }
    return implode(', ', $categoryNames);
};