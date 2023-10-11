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
		$image = 'elements/fancy/postmark.png';

		$css = "	footer {\n"
			. "		grid-column: 1 / -1;\n"
			. "		clear: both;\n"
			. "		margin: -1em 0 -1em 10px;\n";

		if (@$image) {
			$css .= "		background-image: url($image);"
				. "		background-position: left center;\n"
				. "		background-repeat: no-repeat;\n"
				. "		text-align: right;\n";
		} else
			$css .= "		padding-left: 1em;\n";

		$css .= "	}\n\n";

		$view->stream_append('css', $css);

		$tag = $this->get_property('tag');

		if ($tag)
			$view->stream_append('html-body', $tag);
	}
}

?>
