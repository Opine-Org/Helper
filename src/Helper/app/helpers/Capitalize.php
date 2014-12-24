<?php
namespace Helper;

class Capitalize
{
    public function render(Array $args, Array $options)
    {
        $word = $args[0];
        if (is_string($word)) {
            return ucfirst($word);
        }

        return '';
    }
}
