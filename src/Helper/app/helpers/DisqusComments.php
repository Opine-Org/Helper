<?php
namespace Helper;

class DisqusComments
{
    private $shortname;

    public function __construct(Array $config)
    {
        if (isset($config['shortname'])) {
            $this->shortname = $config['shortname'];
        }
    }

    public function render(Array $args, Array $options)
    {
        if (empty($this->shortname)) {
            return '<!-- disqus shortname not present -->';
        }

        return '
            <div id="disqus_thread"></div>
            <script type="text/javascript">
                var disqus_shortname = "'.$this->shortname.'";
                (function() {
                    var dsq = document.createElement("script"); dsq.type = "text/javascript"; dsq.async = true;
                    dsq.src = "http://" + disqus_shortname + ".disqus.com/embed.js";
                    (document.getElementsByTagName("head")[0] || document.getElementsByTagName("body")[0]).appendChild(dsq);
                })();
            </script>
            <a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>';
    }
}
