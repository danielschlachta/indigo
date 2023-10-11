<?php

/* ========================================================================
 * Indigo/Web
 *
 * File: designs/fancy/sidebar.php - sidebar for the 'fancy' layout
 *
 * (c) 2023 Daniel Schlachta
 * ======================================================================== */


/*!
 * Create the sidebar. Create with datasource \c _site.
 *
 * The renderer will produce links for the named anchors
 * provided that the current page has more than one.
 *
 * */

class idg_view_html_renderer_fancy_sidebar extends idg_view_node_obj
{
	function __construct(&$parent)
	{
		parent::__construct($parent);
	}


	function render(&$document, &$view)
	{
		if (!$this->datasource)
			return;

		$this->datasource->rewind();

		$doc_path = $document->get_path();
		$url = false;

		$body = "<ul>\n";

		while ($node = $this->datasource->get_token()) {
			if (!$url) {
				if (@($node->properties['path'] != $doc_path))
					continue;
				else {
					$url = $node->properties['url'];
					$depth = $node->get_data();
				}
			} else {
				if ($node->get_data() < $depth)
					break;

				if ($node->properties['type'] != 'anchor')
					continue;

				$name = $node->properties['name'];
				$anchor = $node->properties['anchor'];

				$body .=
					"  <li><div><a href=\"$url#$anchor\">$name</a></div>\n";
			}

		}

		$body .= "</ul>\n";

		if ($body != "<ul>\n</ul>\n")
			$view->stream_append('html-body', "$body\n");
	}
}

?>
