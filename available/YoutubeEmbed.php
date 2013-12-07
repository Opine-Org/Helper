<?php
return function ($template, $context, $args, $source) {
	$args = str_getcsv(trim($args), ' ');
    $argsCount = count($args);
    $variableName = trim(str_replace(['{', '}'], '', $source));
    $width = 560;
    $height = 315;
    if ($argsCount > 0 && !empty($args[0])) {
    	$width = array_shift($args);
    }
    if ($argsCount > 0 && !empty($args[0])) {
    	$height = array_shift($args);
    }
    $embedded = false;
    if (substr_count($variableName, '.') == 1) {
    	$parts = explode('.', $variableName);
    	$variableName = $parts[0];
    	$embedded = $parts[1];
    }
    $id = $context->get($variableName);
    if (!is_array($id) && !empty($id)) {
    	return '<iframe width="' . $width . '" height="' . $height. '" src="//www.youtube.com/embed/' . $id . '" frameborder="0" allowfullscreen></iframe>';
    } elseif (is_array($id)) {
    	if ($embedded !== false && isset($id[$embedded])) {
    		$id = $id[$embedded];
    	} else {
    		return '<!-- invalid value: ' . $variableName . '.' . $embedded . ' -->';
    	}
    } else {
    	return '<!-- invalid value: ' . $variableName . ' -->';
    }
};