<?php
return function () {
    $container = \Opine\container();
    $config = $container->get('config')->sharethis;
    if (!isset($config['code'])) {
        return '<!-- sharethis code not present -->';
    }
};