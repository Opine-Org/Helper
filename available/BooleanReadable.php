<?php
return function ($template, $context, $args, $source) {
    $engine = $template->getEngine();
    $variable = $engine->render($source, $context);
    if ($variable == 't') {
        return 'Yes';
    } elseif ($variable == 'f') {
        return 'No';
    } elseif ($variable === true || $variable === 1 || $variable === "1") {
        return 'Yes';
    } else {
        return 'No';
    }
};