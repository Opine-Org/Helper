<?php
return function ($word) {
	if (is_string($word)) {
		return ucfirst($word);
	}
    return '';
};