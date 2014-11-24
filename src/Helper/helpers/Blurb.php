<?php
namespace Helper;

class Blurb {
    private $db;

    public function __constuct ($db) {
        $this->db = $db;
    }

    public function render ($arguments, $options) {
        if (isset($options['title'])) {
            $blurb = $this->db->collection('blurbs')->findOne(['title' => $options['title']]);
        } elseif (isset($options['tag'])) {
            $blurb = $this->db->collection('blurbs')->findOne(['tags' => $options['tag']]);
        } else {
            return '';
        }
        if (isset($blurb['_id'])) {
            return $blurb['body'];
        }
        return '';
    }
}