<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/** @package Mobile */

namespace Indigo\Design\Mobile;

class navigation extends \idg_fragment_implementation {

    function _render(\idg_document $document, \idg_view $view) {
        $elements = $view->template()->get_uri('elements');
        $idg_id = $this->get_idg_id();

        $view->stream_append('css', "body { padding-top: 130px; }\n"
            . "div#$idg_id { overflow: hidden; background-color: #3b456d;"
            . " position: fixed; top: 0; left: 0; width: 100%; font-size: 180%; }\n"
            . "div#$idg_id #myLinks { display: none; }\n"
            . "div#$idg_id #myLinks a { padding: 20px; }\n"
            . "div#$idg_id a { color: white; padding: 14px 16px 0 16px;"
            . " display: block; text-decoration: none; background-color: black;"
            . " font-size: 80%; }\n"
            . "div#$idg_id a.icon { background: #aaa; display: block;"
            . " position: absolute; right: 0; top: 0; }\n"
            . "div#$idg_id a:hover {  background-color: #ddd; color: black; }\n"
            . "div#$idg_id-title { color: white; height: 130px; font-size: 80%;"
            . " padding: 20px 0 10px 20px; margin: -5px -32px -32px 0; }\n");

        $view->render_css($this->get_declaration(), "div#$idg_id-title");
        
        $title = strtoupper($document->get_default_title());
        $title_image = $view->get_property('icon');
        
        $body = "<div id=\"$idg_id\">\n<div id=\"$idg_id-title\">";
        
        if ($title_image)
            $body .= "<img src=\"$title_image\" style=\"border: 0; height: 100px;  "
            . "float: left; margin-right: 0.3em; \" alt=\"\">";
        
        $body .= "$title</div>\n<div id=\"myLinks\">\n";

        foreach ($this->get_datasource() as $count => $node) {
            if ($path = @$node['path'])
                $uri = $view->template()->get_full_uri($path);
            else
                continue;

            $name = @$node['name'];
            $body .= "  <a href=\"$uri\">$name</a>\n";
        }

        $body .= "</div>\n<a href=\"javascript:void(0);\" class=\"icon\" "
            . 'onclick="myFunction()">'
            . "<img src=\"$elements/hamburger.png\" style=\"border: 0;"
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
