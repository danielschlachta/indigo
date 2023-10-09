<?php

/* ========================================================================
 * Indigo/Web
 *
 * File: designs/fancy/footer.php - footer for the 'fancy' layout
 *
 * (c) 2023 Daniel Schlachta
 * ======================================================================== */


/*!
 * This class can be used to create a footer
 *
 * Ignores the \c source property and gets its text from \c tag instead.
 *
 * Takes the following options (set with \c set_option or \c set_options):
 *  + \c font-family
 *  + \c background-color
 *  + \c image
 *
 * The image is left-aligned, padding for the text is added
 * as necessary by inspecting the image file.
 *
 */

class idg_view_html_renderer_fancy_footer extends idg_view_node_obj
{
	function __construct(&$parent)
	{
		parent::__construct($parent);
	}

	function render(&$document, &$view)
	{
		$decl = $view->get_child_by_key('name', 'footer');
		$font = fancy_options::get_font($decl);

		$bg_color = fancy_options::get_background_color($decl);

		$image = $decl->get_option('image');

		$css = "	.footer {\n"
			. "		font-family: '$font';\n"
			. "		font-size: 100%;\n"
			. "		font-weight: normal;\n"
			. "		padding-left: 1em;\n"
			. "		margin: -1em 0 -1em 0;\n"
			. "		margin-right: 10px;\n";

		if ($image) {
			$size = getimagesize($image);    // doing it always, not
			$padding = $size[0] + 20 . "px"; // much overhead

			$css .= "		background: $bg_color url($image) no-repeat"
				. " left center;\n"
				. "		padding-left: $padding;\n";
		} else
			$css .= "		background-color: $bg_color;\n";

		$css .= "	}\n\n";

		$view->stream_append('css', $css);

		$tag = @$this->parent->get_property('tag');

		if ($tag)
			$view->stream_append('html-body',
				"<div id=\"footer-text\">$tag</div>");

	}
}

?>
