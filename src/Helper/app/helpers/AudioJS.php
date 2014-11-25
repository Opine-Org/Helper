<?php
namespace Helper;

class AudioJS {
    public function render (Array $args, Array $options) {
        $file = $args[0];
        $buffer = '';
        if (!isset($_SERVER['AudioJS-Included'])) {
            $_SERVER['AudioJS-Included'] = true;
            $buffer .= '
                <script src="/js/audiojs/audio.min.js"></script>
                <script>
                audiojs.events.ready(function() {
                    var as = audiojs.createAll();
                });
                </script>';
        }

        $buffer .= '<audio src="' . $file . '" preload="auto" />';

        return $buffer;
    }
}