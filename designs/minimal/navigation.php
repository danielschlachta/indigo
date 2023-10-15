<?php

/*!
 * The navigation at the top of the minimal design.
 *
 * Note that the path for the logo is hard coded at the moment.
 *
 */

class idg_view_html_renderer_minimal_navigation extends idg_tree_node_instance
{

	function __construct(&$parent)
	{
		parent::__construct($parent);
	}

	/*! @todo optionize this! */

	function render(&$document, &$view)
	{
		global $font_blocks;
		global $elements;

		$idg_id = $this->get_idg_id();

		$doc_path = $document->get_path();

		$this->datasource->rewind();

		$params = explode('\\', $view->get_property('tag'));

		$img = $params[0];
		$title = $params[1];
		$copy = $params[2];

		$doctitle = $document->get_property('name');

		$body = "<div id=\"LogoImg\"><img src=\"$img\" alt=\"\"></div>\n"
		    . "<div id=\"Logo\">\n"
		    . "<div id=\"LogoBar\"><div id=\"LogoText\"><i>$title" .
		     " &ndash; <b>$doctitle</b></i>" . "</div></div>\n"
		    . "<div id=\"LogoNav\">\n<div id=\"LogoBtn\">\n";

		while ($node = $this->datasource->get_token()) {
			if ($node->properties['type'] == 'folder')  {
			    while ($node = $this->datasource->get_token()) {
					    if ($node->properties['type'] == 'document') {
						    $name = $node->properties['name'];
						    $url = $node->properties['url'];
                			$body .= "<a href=\"$url\">&middot; $name</a>\n";
                		}
        		}
        	}
		}

		$body .= "</div>\n"
		    . "<div id=\"LogoNavTxt\">$copy</div>\n</div>\n</div>\n<div id=\"TextBody\">\n";

		$view->stream_append('html-body', $body);
	}
}

?>
