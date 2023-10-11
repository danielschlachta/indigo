 <?php

class idg_view_html_renderer_gradient_navigation extends idg_view_node_obj
{

    function __construct(&$parent)
	{
		parent::__construct($parent);
    }

    function render(&$document, &$view)
    {
        global $elements;

        $style = $this->parent->get_property('style');
        $idg_id = $this->get_idg_id();

        $css = " div#$idg_id { padding-top: 5px; padding-left: 2px; "
            . " font-size: 90%; $style }\n"
            . " div#$idg_id a { display: block; text-align: left;"
            . " padding: 10px 5px; margin: 0 0 2px; border-width: 0;"
            . " text-decoration: none; color: #c2c2c2; width: 98%; font-weight: bold; }\n"
            . " div#$idg_id a span { display: none; }\n"
            . " div#$idg_id a:hover { background: url(../designs/gradient/elements/bg_grad.png) top left"
            . " fixed repeat-x; color: #53538a; }\n"
            . " div#$idg_id a:hover span { display: block; position: relative;"
            . " width: 100px; top: 0px; left: 0px; padding-top: 5px;"
            . " font-size: 80%; font-style: italic; font-weight: normal;"
            . " z-index: 100; }\n";

        $view->stream_append('css', $css);

        $body = "<div id=\"$idg_id\">\n";

        $this->datasource->rewind();

        while ($node = $this->datasource->get_token()) {
            $path =& $node->properties['path'];

            if ($path) {
                $name =& $node->properties['name'];

                $comment =& $node->properties['navigation-comment'];

                if ($comment == null)
					$comment = @$node->properties['description'];

                $body .= "<a href=\"?display=$path\">$name"
                    . "<span>$comment</span></a>\n";
            }
        }

        $body .= "</div>\n";

        $view->stream_append('html-body', $body);
    }
}

?>
