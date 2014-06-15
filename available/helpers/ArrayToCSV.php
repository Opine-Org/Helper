<?php
return function ($arrayIn) {
  	if (!is_array($arrayIn)) {
  		return '';
  	}
    return implode(', ', $arrayIn);
};