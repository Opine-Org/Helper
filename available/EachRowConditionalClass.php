<?php
return function ($template, $context, $args, $source) {
    $engine = $template->getEngine();
    $source = '{{@index}}';
    $index = $engine->render($source, $context);
    $args = explode(' ', trim($args));
    $modulus = 2;
    $class = 'even';
    if (isset($args[0])) {
    	$modulus = $args[0];
    }
    if (isset($args[1])) {
    	$class = $args[1];
    }
    if ($index % $modulus == 0) {
    	return $class;
    }
    return '';
};