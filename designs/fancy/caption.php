<?php

/* ========================================================================
 * Indigo/Web
 *
 * File: designs/fancy/caption.php - header for the 'fancy' layout
 *
 * (c) 2023 Daniel Schlachta
 * ======================================================================== */

/*!
 * Create a caption potentially with an image
 */

class idg_view_html_renderer_fancy_caption
	extends idg_treenode {
	function __construct(&$parent)
	{
		parent::__construct($parent);
	}

	function render(&$document, &$view)
	{
		$image = 'elements/fancy/caption.png';

		$idg_id = $this->get_idg_id();

		$css = "	header {\n"
			. "		grid-column: 1 / -1;\n"
			. "		clear: both;\n"
			. "		float: right;\n"
			. "		width: 79.7872%;\n"
			. "		margin: 0;\n"
			. "	}\n\n"
			.	"	#caption {\n"
			. "		margin: 0;\n"
			. "	}\n\n";

			if (@$image)
			$css .= "	#$idg_id {\n"
				. "		background-image: url($image);\n"
				. "		background-repeat: no-repeat;\n"
				. "		background-position: right center;\n"
				.  "	}\n\n";

		$view->stream_append('css', $css);

		$caption = $document->get_property('description');
		$view->stream_append('html-body', "<div id=\"$idg_id\">"
			. "<h1 id=\"caption\">$caption</h1></div>");
	}
}

?>
