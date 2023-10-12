<?php

class idg_view_html_renderer_blocks_imageframe
	extends idg_tree_node_implementation {

	function __construct(&$parent)
	{
		parent::__construct($parent);
	}

	function render(&$document, &$view)
	{
		$idg_id = $this->get_idg_id();


		if (!$attr = $document->get_attribute('image'))
			diag($this, 'no image attribute');

		if (!$img = $attr->get_option('src'))
			diag($this, "no 'src' option given");

		$width = 90;//$parameters['width'];
		$height = 90; // $parameters['height'];
		$top = round((110 - $height) / 2) + 36;
		$left = round((110 - $width) / 2) + 36;
		$top_ie = $top - 1;
		$left_ie = $left - 1;

		$bg_col = '#8a94b6';
		$bo_col = '#23314f';

		$body = "<div id=\"$idg_id-frame\"></div>\n"
			. "<div id=\"$idg_id-image\">"
			. "<img src=\"$img\" id=\"framedimage\""
			. " width=\"$width\" height=\"$height\""
			. " alt=\"\"></div>\n";

		$view->stream_append('html-body', $body);

		$css = "div#$idg_id-frame { position: fixed; top: 30px; left: 30px;"
			. " background: $bg_col; border: 1px solid $bo_col; width: 120px;"
			. " height: 120px; opacity:0.5; z-index: 180; }\n"
			. "div#$idg_id-image { position: fixed;"
			. " top: ${top}px; left: ${left}px; z-index: 181; }\n"
			. "div#$idg_id-image img { opacity:0.8; }\n";

		$view->stream_append('css', $css);

		$css_print = "div#$idg_id-frame { display: none; }\n"
			. "div#$idg_id-image { display: none; }\n";

		$view->stream_append('css-print', $css_print);
	}
}


?>
