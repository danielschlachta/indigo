<?php
class idg_view_html_renderer_blocks_footer extends idg_view_node_obj
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
		$style = $this->get_property('style');
		$last_change = substr($document->get_property('last-change'), 0, 10);
		$style_heading = $this->get_property('style-heading');
		$style_link = $this->get_property('style-link');
		$style_link_hover = $this->get_property('style-link-hover');
		$style_image = $this->get_property('style-image');

		$bg_col = '#b4d2b0';
		$bo_col = '#1e3723';
		$fr_col = '#b4d2b0';
		$co_col = '#b4d2b0';
		$fo_col = '#222';

		$css = "div#$idg_id-box { margin: 5px 0 0 0; background: $bg_col;"
			. " border: solid $bo_col; border-width: 1px 1px 0px 1px;"
			. " $font_blocks font-size: 70%; }\n"
			. "div#$idg_id-frame { padding: 15px 25px 0px 35px;"
			. " background: $fr_col url($elements/footer/line_red.png)"
			. " repeat-y; }\n"
			. "div#$idg_id-content { background: $co_col"
			. " url($elements/footer/metal.png);"
			. " padding: 5px; color: $fo_col; text-align: right; $style }\n"
			. "div#$idg_id-content p { margin: 0.3em 0 0 0; }\n"
			. "span#$idg_id-top { float: left; position: relative;"
			. " top: 0; left: 0; z-index: 220; }\n";

		if ($style_heading)
			$css .= "div#$idg_id-content h1 { $style_heading }\n";
		if ($style_link)
			$css .= "div#$idg_id-content a { $style_link }\n";
		if ($style_link_hover)
			$css .= "div#$idg_id-content a:hover { $style_link_hover }\n";
		if ($style_image)
			$css .= "div#$idg_id-content img { $style_image }\n";

		$url = $document->get_site()->get_site_url();

		$body = "<div id=\"$idg_id-box\">\n"
			. "<div id=\"$idg_id-frame\">\n"
			. "<div id=\"$idg_id-content\">\n"
			. "Last change: $last_change\n"
			. "<p>\n<span id=\"$idg_id-top\"><a href=\"#top\">"
			. "<img src=\"$elements/footer/link.png\""
			. " width=\"11\" height=\"10\" alt=\"\">top</a></span>\n"
			. " &copy;2023 <i>daniel@schlachta.info</i><span> |"
			. " <a href=\"https://validator.w3.org/nu/?doc=$url\">"
			. "<img src=\"$elements/footer/link.png\""
			. " width=\"11\" height=\"10\" alt=\"\">HTML5</a></span>\n"
			. "</p>\n</div>\n</div>\n</div>\n";

		$css_print = "div#$idg_id-box { margin: 0; border: 0; }\n"
			. "div#$idg_id-frame { margin: 0; border: 0; }\n"
			. "div#$idg_id-content { padding: 0; }\n"
			. "div#$idg_id-content span { display: none; }\n";

		$view->stream_append('css', $css);
		$view->stream_append('css-print', $css_print);
		$view->stream_append('html-body', $body);
	}
}

?>
