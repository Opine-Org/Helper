<?php
return function ($template, $context, $args, $source) {
	$container = \Framework\container();
	$config = $container->config->sharethis;
	if (!isset($config['code'])) {
		return '<!-- sharethis code not present -->';
	}
};