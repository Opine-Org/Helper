<?php
return function () {
    $container = \Opine\container();
    $config = $container->get('config')->get('disqus');
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