<?php

/* ========================================================================
 * Indigo/Web
 *
 * File: designs/fancy.php - defines the 'fancy' layout
 *
 * (c) 2023 Daniel Schlachta
 * ======================================================================== */

/*!
 * This layout takes one container that is then placed in a grid,
 * decorated with a navigation bar at the bottom and various other
 * frills.
 *
 * Lorem ipsum dolor sit amet adipising elit
 *
 */

require_once($idg_path . '/modules/mod_pagemap.php');

require_once('fancy/navigation.php');
require_once('fancy/caption.php');
require_once('fancy/sidebar.php');
require_once('fancy/footer.php');

class idg_view_html_template_fancy extends idg_tree_node_instance
{
	function __construct(&$parent, $parameters = null)
	{
		parent::__construct($parent);
	}

	function render(&$document, &$view)
	{
		if ($this->get_child_count() == 0)
			diag($this, 'part has no children');

		//if ($stylesheet = $view->get_option('stylesheet'))
		$stylesheet = '../designs/fancy/elements/default.css';
		$head = " <link href=\"$stylesheet\" rel=\"stylesheet\">\n";

		$stylesheet = 'elements/fancy/style.css';
		$head = " <link href=\"$stylesheet\" rel=\"stylesheet\">\n";

		$view->stream_append('html-head', $head);

		//$idg_id = $view->idg_id;

		$css =  "	body {\n"
			. "		font-family: Liberation Serif', sans-serif;\n"
			. "		font-size: 110%;\n"
			. "		margin: 0;\n"
			. "		padding: 0;\n"
			. "	}\n\n"
			. "	*, *:before, *:after {\n"
			. "		box-sizing: border-box;\n"
			. "	}\n\n"
			. "	.wrapper {\n"
			. "		max-width: 940px;\n"
			. "		width: 66%;\n"
			. "		margin: 2em;\n"
			. "		float: left;\n"
			. "		display: grid;\n"
			. "		grid-template-columns: min-content 1fr;\n"
			. "		grid-gap: 10px;\n"
			. "	}\n\n"
			. "	.wrapper > * {\n"
			. "		padding: 20px;\n"
			. "		margin-bottom: 10px;\n"
			. "		border-radius: 5px;\n"
			. "	}\n\n"
			. "	aside {\n"
			. "		float: left;\n"
			. "		width: 19.1489%;\n"
			. "		padding-top: 0;\n"
			. "	}\n\n"
			. "	article {\n"
			. "		float: right;\n"
			. "		width: 79.7872%;\n"
			. "		padding-top: 0.9em;\n"
			. "		padding-bottom: 0.5em;\n"
			. "		padding-right: 1.5em;\n"
			. "	}\n\n"
			. "	footer {\n"
			. "		float: right;\n"
			. "		width: 79.7872%;\n"
			. "	}\n\n";

		$view->stream_append('css', $css);

		$css_print = "    	nav { display: none; }\n\n";
		$view->stream_append('css-print', $css_print);

		$view->stream_append('css',
			  "	@supports (display: grid) {\n"
			. "		.wrapper > * {\n"
			. "			width: auto;\n"
			. "			margin: 0;\n"
			. "		}\n"
			. "	}\n\n");


		$view->stream_append('html-body', "<div class=\"wrapper\">\n");

		// header
		if ($header = $view->get_child_by_key('name', 'header'))  {
			$view->stream_append('html-body', "<header id=\"#hd\">\n");
			$header->_render($document, $view);
			$view->stream_append('html-body', "</header>\n");
		}

		// sidebar
		$view->stream_append('html-body', "<aside>\n");

		if ($sidebar = $view->get_child_by_key('name', 'sidebar'))
			$sidebar->_render($document, $view);

		$view->stream_append('html-body', "</aside>\n");

		// content
		$view->stream_append('html-body', "<article>\n");

		if ($content = $view->get_child_by_key('name', 'content'))
			$content->_render($document, $view);
		else
			$view->stream_append('html-body', "<code>This page intentionally left blank.</code>\n");

		$view->stream_append('html-body', "</article>\n");

		// footer
		if ($footer = $view->get_child_by_key('name', 'footer')) {
			$view->stream_append('html-body', "<footer>\n");
			$footer->_render($document, $view);
			$view->stream_append('html-body', "\n</footer>\n");
		}

		// navigation
		if ($navigation = $view->get_child_by_key('name', 'navigation')) {
			$view->stream_append('html-body', "	<nav>\n");
			$navigation->_render($document, $view);
			$view->stream_append('html-body', "	</nav>\n");
		}

		// page map
		idg_pagemap::add_to_view($view,
			'bottom: 0', 'right: 0', '25%','93%');
	}
}

?>
