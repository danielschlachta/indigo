<?php

/* ========================================================================
 * Indigo/Web
 * 
 * File: designs/fancy/caption.php - header for the 'fancy' layout
 * 
 * (c) 2023 Daniel Schlachta
 * ======================================================================== */


$caption_preg = '|^<h1>.*</h1>[ \n\r]*|';
$caption_eaten = false;

function fancy_filter_eat_caption(&$text)
{
	global $caption_preg;
	global $caption_eaten;
	
	if (!$caption_eaten && preg_match($caption_preg, $text)) {
		$output = preg_replace($caption_preg, '', $text);
		$caption_eaten = true;
		return $output;
	}
}

class idg_renderer_fancy_caption extends idg_view_node_obj
{
	function __construct(&$parent)
	{
		parent::__construct($parent);
	}
	
	function render(&$document, &$view)
	{
		global $caption_preg;
		global $caption_font;
		
		$this->datasource->rewind();
		
		$view->stream_append('css', "	#caption {\n" 
			. "		font-family: '$caption_font', 'Caladea', 'LinLibertine', serif;\n" 			  
			. "		font-size: 100%;\n"
			. "		font-weight: normal;\n"
			. "		grid-column: 1 / -1;\n"
			. "		clear: both;\n"
			. "	}\n\n");
		
		while ($token = $this->datasource->get_token()) {
			if (preg_match($caption_preg, $token->data, $match)) {
				$caption = preg_replace('|[ \r\n]*$|', '', $match[0]);
				$view->stream_append('html-body', 
					"<div id=\"caption\">$caption</div>\n");
			}
		}
	}
}

?>
