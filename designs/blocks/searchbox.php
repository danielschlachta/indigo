<?php

class idg_view_html_item_searchbox extends idg_tree_node_implementation
{
	function __construct(&$parent)
	{
		parent::__construct($parent);
	}

	function render(&$document, &$view)
	{
		global $font_blocks;
		global $elements;

		$idg_id = $this->get_idg_id();

		$body = "<div id=\"$idg_id\">\n<form onsubmit=\"append();\" action=\"https://google.com/search\">\n"
			. "<div id=\"$idg_id-caption\">Search</div>\n"
			. "<div><input type=\"text\" name=\"q\" id=\"querytext\">"
			. "</div>\n</form>\n</div>\n";

		$css = "div#$idg_id { position: fixed;"
			. " bottom: 60px; left: 30px; $font_blocks z-index: 210; }\n"
			. "div#$idg_id form { margin: 0 0 10px 0; padding: 0; }\n"
			. "div#$idg_id input { width: 82px; font-size: 80%;"
			. " font-family: verdana,sans-serif; color: #bbb;"
			. " background: transparent; border: solid #9095c6;"
			. " border-width: 1px 0 1px 1px; margin: 0; padding: 0; }\n"
			. "div#$idg_id-caption { color: #aaa;"
			. " background: url($elements/searchbox/magnifier.png)"
			. " no-repeat; background-position: center left;"
			. " padding-left: 15px; font-size: 80%; }\n";

		$css_ie = "div#$idg_id { position: absolute; }\n"
			. "div#$idg_id input { width: 80px; }\n";

		$css_print = "div#$idg_id { display: none }\n";

		$js = "function append() { "
			. "document.getElementById(\"querytext\").value += "
			. "\" site:danielschlachta.github.io\"; }\n";

		$view->stream_append('css', $css);
		$view->stream_append('css-print', $css_print);
		$view->stream_append('html-body', $body);
    	$view->stream_append('js', $js);
	}
}


?>
