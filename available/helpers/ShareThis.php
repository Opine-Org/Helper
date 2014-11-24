<?php
return function () {
    $container = \Opine\container();
    $config = $container->get('config')->get('sharethis');
    if (!isset($config['code'])) {
        return '<!-- sharethis code not present -->';
    }
};