<?php
namespace Helper;

class ImageResize
{
    private $imageResizer;

    public function __construct($imageResizer)
    {
        $this->imageResizer = $imageResizer;
    }

    public function render(Array $args, Array $options)
    {
        if (isset($options['image'])) {
            $image = $options['image'];
        } else {
            if (isset($args[0])) {
                $image = $args[0];
            }
        }
        if (empty($image) || !is_array($image) || !isset($image['url'])) {
            return '';
        }
        $path = $image['url'];
        if (!isset($options['height'])) {
            $options['height'] = 100;
        }
        if (!isset($options['width'])) {
            $options['width'] = 100;
        }
        if (!isset($options['cropratio'])) {
            $options['cropratio'] = false;
        }
        $tag = true;
        if (array_key_exists('notag', $options)) {
            $tag = false;
        }
        $optionsCount = count($options);
        if ($optionsCount > 0 && !empty($options['width'])) {
            if (substr_count($options['width'], '%') == 1) {
                $options['width'] = $image['width'] * (((integer) trim($options['width'], '%')) / 100);
            }
            if ($optionsCount == 1) {
                $options['height'] = floor($image['height'] * ($options['width'] / $image['width']));
            }
        }
        if ($optionsCount > 1 && !empty($options['height'])) {
            $options['height'] = $options['height'];
            if (substr_count($options['height'], '%') == 1) {
                $options['height'] = $image['height'] * (((integer) trim($options['height'], '%')) / 100);
            }
        }
        if ($optionsCount > 2 && !empty($options['cropratio'])) {
            $cropratio = $options['cropratio'];
        }
        $url = $this->imageResizer->getPath($path, $options['width'], $options['height']);
        if ($tag === false) {
            return $url;
        }

        return '<img src="'.$url.'" />';
    }
}
