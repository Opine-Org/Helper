<?php
return function ($template, $context, $args, $source) {
    //usage: {{#MongoDate format="m/d/Y" field="created_date"}}

    $args = $template->htmlArgsToArray($args);
    if (!isset($args['field'])) {
        $variableName = trim(str_replace(['{', '}'], '', $source));
    } else {
        $variableName = $args['field'];
    }
    $format = 'm/d/Y';
    if (isset($args['format'])) {
        $format = $args['format'];
    }
    $date = $context->get($variableName);
    if (!isset($date['sec'])) {
    	return date($format, strtotime($date));
    }
    return date($format, $date['sec']);
};