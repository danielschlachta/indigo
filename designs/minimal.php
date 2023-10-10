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


class idg_view_html_part_minimal extends idg_view_node_param_obj
{
	function __construct(&$parent)
	{
		parent::__construct($parent);
	}

	function render(&$document, &$view)
	{
		global $elements_minimal;

	    $style = $this->parent->get_property('style');
   	    $style_print = $this->parent->get_property('style-print');

	    if ($this->parent->children == null
			|| ($childcount = count($this->parent->children)) != 1)
			diag($this, get_class($this)
				. " needs exactly one container (found $childcount).");

		if (count($this->parent->children[0]->children,
			COUNT_RECURSIVE) != 3)
			diag($this->get_class($this) . " needs exactly three "
				. "elements in its container (see doc).");

		$idg_id = $this->parent->get_idg_id();
		$fixed = $this->parent->children[0];

		$head = ' <link rel="stylesheet" href="' . $elements_minimal
			. '/style.css">';
		$view->stream_append('html-head', $head);

		$body = "<div id=\"$idg_id\">\n";
		$view->stream_append('html-body', $body);

		$fixed->_render($document, $view);

		$body = "</div>\n";
		$view->stream_append('html-body', $body);

		$part = $this->parent->children[0];
	}
}


?>
