<?php

class idg_datasource_blocks_framedimg extends idg_datasource
{
	function __construct(&$parameters)
	{
		parent::__construct($parameters);
		$tok = new idg_token($parameters);
		$this->tokens[] = $tok;
	}
}

class idg_view_html_renderer_blocks_framedimg extends idg_view_node_obj
{

	function __construct(&$parent)
	{
		parent::__construct($parent);
	}

	function render(&$document, &$view)
	{
		$idg_id = $this->get_idg_id();
//		$this->datasource->rewind();
//		$token = $this->datasource->get_token();
//		$parameters = $token->get_data();

		$img = $document->get_path() . '/images/' . @$parameters['file'];
		$width = @$parameters['width'];
		$height = @$parameters['height'];
		$top = round((110 - $height) / 2) + 36;
		$left = round((110 - $width) / 2) + 36;
		$top_ie = $top - 1;
		$left_ie = $left - 1;

		$bg_col = '#8a94b6';
		$bo_col = '#23314f';

		$body = "<div id=\"$idg_id-frame\"></div>\n"
			. "<div id=\"$idg_id-image\">"
			. "<img src=\"$img\" id=\"framedimg\""
			. " width=\"$width\" height=\"$height\""
			. " alt=\"\"></div>\n";

		$css = "div#$idg_id-frame { position: fixed; top: 30px; left: 30px;"
			. " background: $bg_col; border: 1px solid $bo_col; width: 120px;"
			. " height: 120px; opacity:0.5; z-index: 180; }\n"
			. "div#$idg_id-image { position: fixed;"
			. " top: ${top}px; left: ${left}px; z-index: 181; }\n"
			. "div#$idg_id-image img { opacity:0.8; }\n";

		$css_ie = "div#$idg_id-frame { position: absolute;"
			. " margin-right: 22px; }\n"
			. "div#$idg_id-image { position: absolute;"
			. " top: ${top_ie}px; left: ${left_ie}px; }\n";

		$css_print = "div#$idg_id-frame { display: none; }\n"
			. "div#$idg_id-image { display: none; }\n";

		$view->stream_append('html-body', $body);
		$view->stream_append('css', $css);
		$view->stream_append('css-print', $css_print);
	}
}


?>
