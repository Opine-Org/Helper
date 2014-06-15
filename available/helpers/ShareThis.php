<?php
return function () {
    $container = \Opine\container();
    $config = $container->config->sharethis;
    if (!isset($config['code'])) {
        return '<!-- sharethis code not present -->';
    }
};