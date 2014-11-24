<?php
namespace Helper;

class ShareThis {
    private $code;

    public function __construct (Array $config) {
        if (isset($config['code'])) {
            $this->code = $config['code'];
        }
    }

    public function render () {
        if (empty($this->code)) {
            return '<!-- sharethis code not present -->';
        }
        return '';
    }
}