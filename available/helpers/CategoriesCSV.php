<?php
return function ($categories) {
    if (empty($categories) || !is_array($categories)) {
        return '';
    }
    $categoryNames = [];
    $container = \Opine\container();
    $db = $container->db;
    foreach ($categories as $id) {
        $key = 'categories-' . (string)$id;
        $name = \Opine\Framework::keyGet($key);
        if ($name !== false) {
            $categoryNames[] = $name;
        } else {
            $found = $db->collection('categories')->findOne(['_id' => $db->id($id)], ['title']);
            if (isset($found['_id'])) {
                $categoryNames[] = $found['title'];
                \Opine\Framework::keySet($key, $found['title']);
            }
        }
    }
    return implode(', ', $categoryNames);
};