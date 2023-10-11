<?php

class idg_view_html_item_toolbox extends idg_tree_node_implementation
{
	function __construct(&$parent)
	{
		parent::__construct($parent);
	}

	function qr_link(&$document)
	{
		$link = urlencode($document->get_site()->get_site_url());

		return "http://chart.googleapis.com/chart?chs=300x300&cht=qr&amp;chl=$link&amp;choe=UTF-8";
    }

	function render(&$document, &$view)
	{
		global $font_blocks;
		global $elements;

		$idg_id = $this->get_idg_id();

		$body = "<div id=\"$idg_id\">\n<div id=\"$idg_id-body\">\n"
			. "<ul>\n"
			. " <li><a href=\"mailto:Daniel Schlachta <daniel@schlachta.info>\">Contact</a></li>\n"
			. " <li><a href=\"javascript:void(0);\" onClick=\"document.getElementById('framedimg').src='"
			. $this->qr_link($document) . "'\">QR Code</a></li>\n"
			. "</ul>\n</div>\n</div>\n";

		$bo_col = '#9095c6';
		$fg_col = '#23314f';
		$bg_col = '#6b6bb2';
		$li_col = '#aaa';

		$css = "div#$idg_id { position: fixed;"
			. " $font_blocks bottom: 13px; left: 30px;"
			. " z-index: 210; }\n"
			. "div#$idg_id-body { border: solid $bo_col;"
			. " border-width: 1px 1px 1px 0px; padding: 5px 0 5px 2px;"
			. " font-size: 90%; width: 80px; }\n"
			. "div#$idg_id-body ul { margin: 0 0 0 -2px; padding: 0;"
			. " list-style: none; font-size: 80%; }\n"
			. "div#$idg_id-body li { padding: 0; margin: 0; }\n"
			. "div#$idg_id-body a { padding: 1px 0 1px 15px;"
			. " display: block; width: 67px; height: 100%; color: $li_col;"
			. " text-decoration: none;"
			. " background: url($elements/toolbox/link.png)"
			. " no-repeat; background-position: left center; }\n"
			. "div#$idg_id-body a:hover { color: $fg_col; background: $bg_col"
			. " url($elements/toolbox/link.png) no-repeat; "
			. "background-position: left center; }\n";

		$css_ie = "div#$idg_id { position: absolute; }\n"
			. "div#$idg_id-body a { width: 80px }\n";

		$css_print = "div#$idg_id { display: none }\n";

		$view->stream_append('css', $css);
		$view->stream_append('css-print', $css_print);
		$view->stream_append('html-body', $body);
	}
}


?>
