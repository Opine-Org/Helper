<?php
return function ($template, $context, $args, $source) {
    $engine = $template->getEngine();
    $output = $engine->render($source, $context);
    return ucfirst($output);
};