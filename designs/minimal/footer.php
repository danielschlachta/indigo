<?php

class idg_view_html_renderer_minimal_footer extends idg_treenode
{	
	function __construct(&$parent)
	{
		parent::__construct($parent);
	}
	
	function render(&$document, &$view)
	{
		$view->stream_append('html-body', 
			"<div id=\"spacer\"></div>\n"
			. "<div id=\"footer\"><a href=\"#top\">" 
			. "&middot; back to top</a></div>\n</div>\n");
	}
}

?>
