<?php
namespace Helper;

class CategoriesCSV
{
    private $db;
    private static $cache = [];

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function render(Array $args, Array $options)
    {
        $categories = $args[0];
        if (empty($categories) || !is_array($categories)) {
            return '';
        }
        $categoryNames = [];
        foreach ($categories as $id) {
            if (!isset(self::$cache[(string) $id])) {
                $found = $this->db->collection('categories')->findOne(['_id' => $this->db->id($id)], ['title']);
                if (isset($found['_id'])) {
                    self::$cache[(string) $id] = $found['title'];
                }
            }
            if (isset(self::$cache[(string) $id])) {
                $categoryNames[] = '<div class="ui label">'.self::$cache[(string) $id].'</div>';
            }
        }

        return implode(' ', $categoryNames);
    }
}
