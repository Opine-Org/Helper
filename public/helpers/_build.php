<?php
$helpers = [];
$helpers["ArrayToCSV"] = function ($arrayIn) {
  	if (!is_array($arrayIn)) {
  		return '';
  	}
    return implode(', ', $arrayIn);
};

$helpers["AudioJS"] = function ($file) {
    $buffer = '';
    if (class_exists('\Opine\Framework')) {
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

$helpers["Blurb"] = function ($options) {
    $db = \Opine\container()->db;
    if (isset($options['title'])) {
        $blurb = $db->collection('blurbs')->findOne(['title' => $options['title']]);
    } elseif (isset($options['tag'])) {
        $blurb = $db->collection('blurbs')->findOne(['tags' => $options['tag']]);
    } else {
        return '';
    }
    if (isset($blurb['_id'])) {
        return $blurb['body'];
    }
    return '';
};

$helpers["BooleanReadable"] = function ($variable) {
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

$helpers["Capitalize"] = function ($word) {
	if (is_string($word)) {
		return ucfirst($word);
	}
    return '';
};

$helpers["CategoriesCSV"] = function ($categories) {
    if (empty($categories) || !is_array($categories)) {
        return '';
    }
    $categoryNames = [];
    $container = \Opine\container();
    $db = $container->db;
    foreach ($categories as $id) {
        $key = 'categories-' . (string)$id;
        $name = \Opine\Framework::keyGet($key);
        if ($name !== false) {
            $categoryNames[] = $name;
        } else {
            $found = $db->collection('categories')->findOne(['_id' => $db->id($id)], ['title']);
            if (isset($found['_id'])) {
                $categoryNames[] = $found['title'];
                \Opine\Framework::keySet($key, $found['title']);
            }
        }
    }
    return implode(', ', $categoryNames);
};

$helpers["DisqusComments"] = function () {
    $container = \Opine\container();
    $config = $container->config->disqus;
    if (!isset($config['shortname'])) {
        return '<!-- disqus shortname not present -->';
    }
    return '
        <div id="disqus_thread"></div>
        <script type="text/javascript">
            var disqus_shortname = "' . $config['shortname'] . '";        
            (function() {
                var dsq = document.createElement("script"); dsq.type = "text/javascript"; dsq.async = true;
                dsq.src = "http://" + disqus_shortname + ".disqus.com/embed.js";
                (document.getElementsByTagName("head")[0] || document.getElementsByTagName("body")[0]).appendChild(dsq);
            })();
        </script>
        <a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>';
};

$helpers["EachRowConditionalClass"] = function ($template, $context, $args, $source) {
    $engine = $template->getEngine();
    $source = '{{@index}}';
    $index = $engine->render($source, $context);
    $args = str_getcsv(trim($args), ' ');
    $modulus = 2;
    $class = 'even';
    if (isset($args[0])) {
        $modulus = $args[0];
    }
    if (isset($args[1])) {
        $class = $args[1];
    }
    if (($index + 1) % $modulus == 0) {
        return $class;
    }
    return '';
};

$helpers["FacebookComments"] = function () {
    return '<!-- facebook comments pending -->';
};

$helpers["ImageResize"] = function ($image, $options=[]) {
//    var_dump(func_get_args());
    print_r(func_get_args());
   exit;
    if (empty($image) || !is_array($image) || !isset($image['url'])) {
        return 'x';
    }
    $path = $image['url'];
    $height = 100;
    $width = 100;
    $cropratio = false;
    $tag = true;
    if (in_array('notag', $options)) {
        $tag = false;
        $args = array_diff($args, ['notag']);
    }
    $argsCount = count($options);
    if ($argsCount > 0 && !empty($args['width'])) {
        $width = $args['width'];
        if (substr_count($width, '%') == 1) {
            $width = $image['width'] * (((integer)trim($width, '%')) / 100);           
        }
        if ($argsCount == 1) {
            $height = floor($image['height'] * ($width / $image['width']));
        }
    }
    if ($argsCount > 1 && !empty($args['height'])) {
        $height = $args['height'];
        if (substr_count($height, '%') == 1) {
            $height = $image['height'] * (((integer)trim($height, '%')) / 100);           
        }
    }
    if ($argsCount > 2 && !empty($args['cropratio'])) {
        $cropratio = $args['cropratio'];
    }
    //$container = \Opine\container();
    //$url = $container->imageResizer->getPath($path, $width, $height);
    $url = $height . ',' . $width . ',' . $cropratio;
    if ($tag === false) {
        return $url;
    }
    return '<img src="' . $url . '" />';
};

$helpers["MongoDate"] = function ($dateObject, $options=[]) {
    //usage: {{MongoDate field format="m/d/Y"}}
    $format = 'm/d/Y';
    if (isset($options['format'])) {
        $format = $options['format'];
    }
    if (!isset($dateObject['sec'])) {
        return date($format, strtotime($dateObject));
    }
    return date($format, $dateObject['sec']);
};

$helpers["PaginationBootstrap"] = function ($pagination, $metadata) {
    ob_start();
    $baseUrl = '/' . $metadata['collection'] . '/' . $metadata['method'] . '/' . $pagination['limit'] . '/';
    //if ($pagination['pageCount'] == 1) {
    //    return '';
    //}
    $startPage = $pagination['page'] - 4;
    $endPage = $pagination['pageCount'] + 4;

    if ($startPage <= 0) {
        $endPage -= ($startPage - 1);
        $startPage = 1;
    }
    if ($endPage > $pagination['pageCount']) {
        $endPage = $pagination['pageCount'];
    }

    echo '
        <div class="pagination">
            <ul>';
    if ($startPage > 1) {
        echo '
                <li>
                    <a href="', $baseUrl, ($pagination['page'] - 1), '">&laquo;</a>
                </lii>';
    }

    for ($i = $startPage; $i <= $endPage; $i++) {
        $active = '';
        if ($i == $pagination['page']) {
            $active = ' class="active"';
        }
        echo '
                <li>
                    <a href="', $baseUrl, $i, '"', $active, '>', $i, '</a>
                </li>';
    }
    if ($endPage < $pagination['pageCount']) {
        echo '
                <li>
                    <a href="', $baseUrl, ($pagination['page'] - 1), '">&raquo;</a>
                </li>';
    }
    echo '
            </ul>
        </div>';

    $buffer = ob_get_clean();
    return $buffer;
};

$helpers["ShareThis"] = function () {
    $container = \Opine\container();
    $config = $container->config->sharethis;
    if (!isset($config['code'])) {
        return '<!-- sharethis code not present -->';
    }
};

$helpers["TwitterStream"] = function ($template, $context, $args, $source) {
    return '<!-- twitter stream pending -->';
};

$helpers["YoutubeEmbed"] = function ($template, $context, $args, $source) {
    $args = str_getcsv(trim($args), ' ');
    $argsCount = count($args);
    $variableName = trim(str_replace(['{', '}'], '', $source));
    $width = 560;
    $height = 315;
    if ($argsCount > 0 && !empty($args[0])) {
        $width = array_shift($args);
    }
    if ($argsCount > 0 && !empty($args[0])) {
        $height = array_shift($args);
    }
    $embedded = false;
    if (substr_count($variableName, '.') == 1) {
        $parts = explode('.', $variableName);
        $variableName = $parts[0];
        $embedded = $parts[1];
    }
    $id = $context->get($variableName);
    if (!is_array($id) && !empty($id)) {
        return '<iframe width="' . $width . '" height="' . $height. '" src="//www.youtube.com/embed/' . $id . '" frameborder="0" allowfullscreen></iframe>';
    } elseif (is_array($id)) {
        if ($embedded !== false && isset($id[$embedded])) {
            $id = $id[$embedded];
            return '<iframe width="' . $width . '" height="' . $height. '" src="//www.youtube.com/embed/' . $id . '" frameborder="0" allowfullscreen></iframe>';
        } else {
            return '<!-- invalid value: ' . $variableName . '.' . $embedded . ' -->';
        }
    } else {
        return '<!-- invalid value: ' . $variableName . ' -->';
    }
};

return $helpers;