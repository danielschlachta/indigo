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

$body_font = @is_null($body_font) ? 'GentiumAlt' : $body_font;
$body_bg_color = @is_null($body_bg_color) ? '#e0e0e0' : $body_bg_color;

$caption_font = @is_null($caption_font) ? 'Noto Serif' : $caption_font;

$nav_font = @is_null($nav_font) ? 'Noto Sans' : $nav_font;
$nav_bg_color = @is_null($nav_bg_color) ? 'c0c0c0' : $nav_bg_color;
 
require_once($idg_path . '/modules/mod_pagemap.php');
 
require_once('fancy/navigation.php');
require_once('fancy/caption.php');

function fancy_filter_typo(&$text)
{
	$output = str_replace(' - ', '&mdash;', $text);
	$output = str_replace('&ldquo;', '&laquo;', $output);
	$output = str_replace('&rdquo;', '&raquo;', $output);
	
	return $output;
}

class idg_view_html_part_fancy extends idg_view_node_param_obj
{
	
	function __construct(&$parent)
	{
		parent::__construct($parent);
	}
	
	function render(&$document, &$view)
	{
		global $nav_font;
		global $caption_font;
		global $body_font;
		global $body_bg_color;	
		
	    $style = $this->parent->get_property('style');
   	    $style_print = $this->parent->get_property('style-print');
	
		if ($this->parent->children == null)
			diag($this, get_class($this) . ' has no children');
		
		$head = ' <link rel="preconnect" href="https://fonts.gstatic.com">';
		
		$nav_font_url = str_replace(' ', '+', $nav_font);
		$head .= " <link href=\"https://fonts.googleapis.com/css2?family=$nav_font_url&display=swap\" " 
			. "rel=\"stylesheet\">\n";
		
		$caption_font_url = str_replace(' ', '+', $caption_font);
		$head .= " <link href=\"https://fonts.googleapis.com/css2?family=$caption_font_url&display=swap\" " 
			. "rel=\"stylesheet\">\n";
				
		$body_font_url = str_replace(' ', '+', $body_font);
		$head .= " <link href=\"https://fonts.googleapis.com/css2?family=$body_font_url&display=swap\" " 
			. "rel=\"stylesheet\">\n";
		
		$view->stream_append('html-head', $head);
			
		//$idg_id = $this->parent->idg_id;
		
		$css =  "	body {\n"
			. "		font-family: '$body_font', 'Liberation Serif', sans-serif;\n" 
			. "		font-size: 110%;\n"
			. "		margin: 0;\n"
			. "		background: $body_bg_color;\n"
			. "	}\n\n"
			. "	*, *:before, *:after {\n"
			. "		box-sizing: border-box;\n"
			. "	}\n\n"
			. "	.wrapper {\n"
			. "		max-width: 940px;\n"
			. "		width: 66%;\n"
			. "		margin: 2em;\n"
			. "		padding-top: 1em;\n"
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
			. "	.sidebar {\n"
			. "		float: left;\n"
			. "		width: 19.1489%;\n"
			// . "		background: #B1B390;\n" // khaki !!!
			. "		background: #bdd5c4;\n"
			. "	}\n\n"
			. "	.content {\n"
			. "		float: right;\n"
			. "		width: 79.7872%;\n"
			. "		background: #ebd8b9;\n"
			. "	}\n\n"
			. "	.footer {\n"
			. "		float: right;\n"
			. "		width: 79.7872%;\n"
			//. "		background: #8ca6cd;\n" // darker tint
			. "		background: #a2acbd;\n"
			. "	}\n\n"
			. "	.header, .footer {\n"
			. "		grid-column: 1 / -1;\n"
			. "		clear: both;\n"
			. "	}\n\n"
			. "	@supports (display: grid) {\n"
			. "		.wrapper > * {\n"
			. "			width: auto;\n"
			. "			margin: 0;\n"
			. "		}\n"
			. "	}\n\n"
			. "	#map {\n"
			. "		position: fixed;\n"
			. "		bottom: 0;\n"
			. "		right: 0;\n"
			. "		width: 25%;\n"
			. "		height: 90%;\n"
			. "		z-index: 200;\n"
			.	"	}\n\n";
			  
		$view->stream_append('css', $css);
	
		$css_print = "    	#navigation { display: none; }\n\n";
		$view->stream_append('css-print', $css_print);
		
		$view->stream_append('html-body', "<div class=\"wrapper\">\n");
		
//		var_dump($this->parent->children[1]);
//		die('');
		
		$parent =& $this->parent;
		
		// header
		if ($header = $parent->get_child_by_key('name', 'header'))  {
			$view->stream_append('html-body', "<header class=\"header\">\n");
			$header->_render($document, $view);	
			$view->stream_append('html-body', "</header>\n");
		}

		// sidebar
		$view->stream_append('html-body', "<aside class=\"sidebar\"><h2>&middot; hello &middot;</h2></aside>\n");

		// content
		$view->stream_append('html-body', "<article class=\"content\">\n");
		
		if ($content = $parent->get_child_by_key('name', 'content'))  
			$content->_render($document, $view);
		else
			$view->stream_append('html-body', "<code>This page intentionally left blank.</code>\n");
		
		$view->stream_append('html-body', "</article>\n");
		
		// footer
		if ($footer = $parent->get_child_by_key('name', 'footer')) {
			$view->stream_append('html-body', "<footer class=\"footer\">\n");
			$footer->_render($document, $view);
			$view->stream_append('html-body', "\n</footer>\n");	
		}
		
		// navigation
		if ($navigation = $parent->get_child_by_key('name', 'navigation')) 
			$navigation->_render($document, $view);	
			
			
		$view->stream_append('html-body', idg_pagemap::map());
	}
}

?>
