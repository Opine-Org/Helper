<?php
return function ($args) {
	$word = $args[0];
	if (is_string($word)) {
		return ucfirst($word);
	}
    return '';
};