<?php

class idg_view_html_renderer_gradient_footer extends idg_view_node_obj
{
	function __construct(&$parent)
	{
		parent::__construct($parent);
	}

	function render(&$document, &$view)
	{
    	$idg_id = $this->parent->get_idg_id();

	    $css = "p#$idg_id { padding-bottom: 1em; }\n"
			. "p#$idg_id a { text-decoration: none; color: #25253d; }\n"
	        . "p#$idg_id a:hover { text-decoration: underline; color: #50503d; }\n"
	        . "p#$idg_id span { font-size: 90%; font-style: italic; font-weight: bold; }\n";

		$css_print = "div#$idg_id { margin: 0; border: 0; }\n"
			. "div#$idg_id span { display: none; }\n";

		$body = "<p id=\"$idg_id\"><span><a href=\"#top\">"
            . '<img src="elements/gradient/hand.png"'
            . ' width="37" height="18" style="float: left; margin-top: 2px; padding-right: 10px;" alt="">'
            . "back to top</a></span></p>\n";

		$view->stream_append('css', $css);
		$view->stream_append('css-print', $css_print);
		$view->stream_append('html-body', $body);
	}
}

?>
