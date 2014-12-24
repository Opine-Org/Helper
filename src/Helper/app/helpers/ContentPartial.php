<?php
namespace Helper;

class ContentPartial
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function render(Array $args, Array $options)
    {
        if (isset($options['title'])) {
            $partial = $this->db->collection('partials')->findOne(['title' => $options['title']]);
        } elseif (isset($options['code'])) {
            $partial = $this->db->collection('partials')->findOne(['code_name' => $options['code']]);
        } else {
            return '';
        }
        if (isset($partial['_id'])) {
            return $partial['body'];
        }

        return '';
    }
}
