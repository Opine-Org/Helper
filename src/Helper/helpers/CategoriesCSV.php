<?php
namespace Helper;

class CategoriesCSV {
    private $db;

    public function __constuct ($db) {
        $this->db = $db;
    }

    public function render (Array $args, Array $options) {
        $categories = $args[0];
        if (empty($categories) || !is_array($categories)) {
            return '';
        }
        $categoryNames = [];
        foreach ($categories as $id) {
            $found = $this->db->collection('categories')->findOne(['_id' => $db->id($id)], ['title']);
            if (isset($found['_id'])) {
                $categoryNames[] = $found['title'];
            }
        }
        return implode(', ', $categoryNames);
    }
}