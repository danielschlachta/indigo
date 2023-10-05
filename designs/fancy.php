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

$body_font = @is_null($body_font) ? 'Enriqueta' : $body_font;
$body_bg_color = @is_null($body_bg_color) ? 'fff6e8' : $body_bg_color;

$nav_font = @is_null($nav_font) ? 'Merriweather' : $nav_font;
$nav_bg_color = @is_null($nav_bg_color) ? 'ffe7d6' : $nav_bg_color;
 
require_once('fancy/navigation.php');

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
		global $body_font;
		global $body_bg_color;
		
	    $style = $this->parent->get_property('style');
   	    $style_print = $this->parent->get_property('style-print');
	
		if ($this->parent->children == null 
			|| ($childcount = count($this->parent->children)) < 1)
			diag($this, get_class($this) . ' must have at least two children');
			
		
		$head = ' <link rel="preconnect" href="https://fonts.gstatic.com">';
		
		$nav_font_url = str_replace(' ', '+', $nav_font);
		$head .= " <link href=\"https://fonts.googleapis.com/css2?family=$nav_font_url&display=swap\" " 
			. "rel=\"stylesheet\">\n";
		
		$body_font_url = str_replace(' ', '+', $body_font);
		$head .= " <link href=\"https://fonts.googleapis.com/css2?family=$body_font_url&display=swap\" " 
			. "rel=\"stylesheet\">\n";
		
		$view->stream_append('html-head', $head);
			
		$fixed = $this->parent->children[0];
		
		$css_body_font = "'$body_font)'";
		
		$idg_id = $this->parent->idg_id;
		
		$css =  "	body {\n"
			  . "		font-family: '$body_font', serif;\n" 
			  . "		font-size: 110%;\n"
			  . "		background: #$body_bg_color;\n"
			  . "	}\n\n"
			  . " .$idg_id { position: absolute; top: 3em; left: 2em; width: 66%; }\n\n"
			  . " .header { color: #E2CDA5; font-size: 150%; height: 3.5em; margin-left: -0.1em; padding-top: 0.1em; padding-left: 0.8em; background: url(elements/fancy/images/banner-left.png) top left no-repeat; }\n\n"
			  . " .main { padding: 0.1em 1em 0 1em; background-color: #BDD5C4; }\n\n";
			  
		$view->stream_append('css', $css);
	
		$css_print = "    	#navigation { display: none; }\n";
		$view->stream_append('css-print', $css_print);
		
		$view->stream_append('html-body', "  <div class=\"$idg_id\">\n");
	
		$view->stream_append('html-body', '  <h1 class="header">Indigo&mdash;the tutorial</h1><div class="main">');
	
	
		for ($i = 1; $i < $childcount; $i++) {
			$part = $this->parent->children[$i];
			$part->_render($document, $view);
		}
		
		$view->stream_append('html-body', "  </div></div>\n");
		$fixed->_render($document, $view);
	}
}

?>
