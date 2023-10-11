 <?php

class idg_view_html_renderer_mobile_navigation extends idg_view_node_obj
{

    function __construct(&$parent)
	{
		parent::__construct($parent);
    }

    function render(&$document, &$view)
    {
        global $elements;

        $idg_id = $this->get_idg_id();

        $css = "div#$idg_id { overflow: hidden; background-color: #3b456d; "
            . " position: fixed; top: 0; left: 0; width: 100%; font-size: 180%; }\n"
            . "div#$idg_id #myLinks { display: none; }\n"
            . "div#$idg_id #myLinks a { padding: 20px; }\n"
            . "div#$idg_id a { color: white; padding: 14px 16px 0 16px;"
            . " display: block; text-decoration: none; background-color: black;"
            . " font-size: 80%; }\n"
            . "div#$idg_id a.icon { background: #aaa; display: block;"
            . " position: absolute; right: 0; top: 0; }\n"
            . "div#$idg_id a:hover {  background-color: #ddd; color: black; }\n"
            . "div#title { color: white; height: 130px; font-size: 80%;"
            . " padding: 24px 0 8px 32px; margin: 0 -32px -32px 0; }\n";

        $view->stream_append('css', $css);

        $title = strtoupper($document->_get_default_title());
        $title_image = $view->get_property('icon');

        $body = "<div id=\"$idg_id\">\n"
            . "<div id=\"title\">"
            . "<img src=\"$title_image\" style=\"border: 0; "
            . "float: left; margin-right: 0.3em; margin-top: 5px;\" alt=\"indigo logo\">"
            . $title . "</div>\n "
            . "<div id=\"myLinks\">\n";

        $this->datasource->rewind();

        while ($node = $this->datasource->get_token()) {
            $path =& $node->properties['path'];

            if ($path) {
                $name =& $node->properties['name'];
                $comment =& $node->properties['navigation-comment'];

                $body .= "    <a href=\"?display=$path\">$name</a>\n";
            }
        }

        $body .= " </div>\n  <a href=\"javascript:void(0);\" class=\"icon\" "
            . 'onclick="myFunction()">'
            . "<img src=\"$elements/Hamburger_icon.png\" style=\"border: 0;"
            . " padding-right: 4px;\" alt=\"menu\"></a>"
            . "\n</div>\n";

        $view->stream_append('html-body', $body);

        $script = "function myFunction() {\n"
            . "  var x = document.getElementById(\"myLinks\");\n"
            . "  if (x.style.display === \"block\") {\n"
            . "    x.style.display = \"none\";\n"
            . "  } else {\n"
            . "    x.style.display = \"block\";\n"
            . "  }\n}\n";

        $view->stream_append('js', $script);
    }
}

?>
