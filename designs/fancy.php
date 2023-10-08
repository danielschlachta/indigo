<?php

/* ========================================================================
 * Indigo/Web
 * 
 * File: designs/fancy.php - defines the 'fancy' layout
 * 
 * (c) 2023 Daniel Schlachta
 * ======================================================================== */


require_once($idg_path . '/lib/gfontapi.php');

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
require_once('fancy/footer.php');

class fancy_options
{
	static function get_font($obj,
		$fallback_font = 'Noto Sans') {
		$font = $obj->get_option('font-family');
				
		return $font ? $font : $fallback_font;
	}
	
	static function get_background_color($obj,
		$fallback_color = '#e0e0e0') {
		$color = $obj->get_option('background-color');
				
		return $color ? $color : $fallback_color;
	}
}

class idg_view_html_part_fancy extends idg_view_node_param_obj
{
	var $google_font_api;
	
	function __construct(&$parent, $parameters = null)
	{
		parent::__construct($parent);
	}
		
	function _find_fonts($obj) {
		if (!$obj->children)
			return;
		
		foreach ($obj->children as $child) {
			if (@($opt = $child->properties['options'])) {
				$font = $child->get_option('font-family');
				$this->google_font_api->register_font($font);
			}
			
			$this->_find_fonts($child);
		}
	}
		
	function render(&$document, &$view)
	{
		// FIXME: currently ignored
	    $style = $this->parent->get_property('style');
   	    $style_print = $this->parent->get_property('style-print');
	
		$p = $this->parent;
	
		if ($p->children == null)
			diag($this, get_class($this) . ' has no children');
		
		$font = fancy_options::get_font($p);
		$bg_color = fancy_options::get_background_color($p, '#c0c0c0');
			
		$this->google_font_api = new google_font_api;
		$this->google_font_api->register_font($font);
		$this->_find_fonts($p);
		
		$head = $this->google_font_api->get_header_lines();
	
		if ($stylesheet = $p->get_option('stylesheet'))
			$head .= " <link href=\"$stylesheet\" rel=\"stylesheet\">\n";
		
		$view->stream_append('html-head', $head);
	
		//$idg_id = $p->idg_id;
			
		$css =  "	body {\n"
			. "		font-family: '$font', 'Liberation Serif', sans-serif;\n" 
			. "		font-size: 110%;\n"
			. "		margin: 0;\n"
			. "		padding: 0;\n"
			. "		background: $bg_color;\n"
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
			. "	.sidebar {\n"
			. "		float: left;\n"
			. "		width: 19.1489%;\n"
			// . "		background: #B1B390;\n" // khaki !!!
			. "		background: #bdd5c4;\n"
			. "		padding-top: 0;\n"
			. "	}\n\n"
			. "	.content {\n"
			. "		float: right;\n"
			. "		width: 79.7872%;\n"
			. "		background: #ebd8b9;\n"
			. "		padding-top: 0.9em;\n"
			. "		padding-bottom: 0.5em;\n"
			. "		padding-right: 1.5em;\n"
			. "	}\n\n"
			. "	.footer {\n"
			. "		float: right;\n"
			. "		width: 79.7872%;\n"
			. "	}\n\n";
			  
		$view->stream_append('css', $css);
	
		$css_print = "    	#navigation { display: none; }\n\n";
		$view->stream_append('css-print', $css_print);
		
		$view->stream_append('html-body', "<div class=\"wrapper\">\n");
		
		$parent =& $this->parent;
		
		// header
		if ($header = $parent->get_child_by_key('name', 'header'))  {
			$header_bg_color = fancy_options::get_background_color($header);
			$css =	"	.header {\n"
			. "		float: right;\n"
			. "		width: 79.7872%;\n"
			. "		background: $header_bg_color;\n"
			. "	}\n\n";
			$view->stream_append('css', $css);
		
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
			
		$view->stream_append('css', "	.header, .footer {\n"
			. "		grid-column: 1 / -1;\n"
			. "		clear: both;\n"
			. "	}\n\n"
			. "	@supports (display: grid) {\n"
			. "		.wrapper > * {\n"
			. "			width: auto;\n"
			. "			margin: 0;\n"
			. "		}\n"
			. "	}\n\n");
			
			
		// page map
		idg_pagemap::add_to_view($view, 'bottom');
	}
}

?>
