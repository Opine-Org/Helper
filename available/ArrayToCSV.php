<?php
return function ($template, $context, $args, $source) {
	$variableName = trim(str_replace(['{', '}'], '', $source));
    $data = $context->get($variableName);
    if (!is_array($data)) {
    	return $data;
    }
    return implode(', ', $data);
};