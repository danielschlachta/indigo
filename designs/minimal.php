<?php

$elements_minimal = '../designs/minimal/elements';

require_once('minimal/navigation.php');
require_once('minimal/footer.php');

/*!
 * Layout for the minimal design.
 *
 * Expected structure:
 *
 * + container
 * 		+ renderer, type minimal_navigation
 * 		+ container
 * 		+ renderer, type minimal_footer
 */


class idg_view_html_template_minimal extends idg_view_node_param_obj
{
	function __construct(&$parent)
	{
		parent::__construct($parent);
	}

	function render(&$document, &$view)
	{
		global $elements_minimal;

	    $style = $this->get_property('style');
   	    $style_print = $this->get_property('style-print');

		$children = $this->get_children();

	    if (!$children
			|| ($childcount = count($children)) != 1)
			diag($this, get_class($this)
				. " needs exactly one container (found $childcount).");

		if (count($children[0]->get_children(),
			COUNT_RECURSIVE) != 3)
			diag($this->get_class($this) . " needs exactly three "
				. "elements in its container (see doc).");

		$idg_id = $this->get_idg_id();
		$fixed = $children[0];

		$head = ' <link rel="stylesheet" href="' . $elements_minimal
			. '/style.css">';
		$view->stream_append('html-head', $head);

		$body = "<div id=\"$idg_id\">\n";
		$view->stream_append('html-body', $body);

		$fixed->_render($document, $view);

		$body = "</div>\n";
		$view->stream_append('html-body', $body);

		$part = $children[0];
	}
}


?>
