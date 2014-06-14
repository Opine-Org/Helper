<?php
return function ($template, $context, $args, $source) {
    $engine = $template->getEngine();
    $file = $engine->render($source, $context);

    $buffer = '';
    if (class_exists(\Opine\Framework)) {
        $included = \Opine\Framework::keyGet('audiojs');

        if ($included === false) {
            \Opine\Framework::keySet('audiojs', true);
            $buffer .= '
                <script src="/js/audiojs/audio.min.js"></script>
                <script>
                audiojs.events.ready(function() {
                    var as = audiojs.createAll();
                  });
                </script>';
        }
    }

    $buffer .= '
        <audio src="' . $file . '" preload="auto" />';

    return $buffer;
};