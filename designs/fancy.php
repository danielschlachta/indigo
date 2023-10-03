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

class idg_view_html_part_grid_bottomnav extends idg_view_html_param_obj
{
	
	function __construct(&$parent)
	{
		parent::__construct($parent);
	}
	
	function render(&$document, &$view)
	{
	    $style = $this->parent->get_property('style');
   	    $style_print = $this->parent->get_property('style-print');
	
		if ($this->parent->children == null 
			|| ($childcount = count($this->parent->children)) < 1)
			die(get_class($this) . ' must have at least two children');
			
		
		
		$idg_id = $this->parent->idg_id;
		$fixed = $this->parent->children[0];
		
		$css =  "	body {\n"
			  . "  		display: grid;\n"
			  . "  		grid-template-rows: 1fr min-content;\n"
			  . "  		grid-template-columns: 1fr 120px;\n"
			  . "	}\n\n"
			  . "	#main-left {\n"
			  . "		grid-row: 1;\n"
			  . "		grid-column: 1;\n"
			  . "	}\n\n"
			  . "	#main-right {\n"
			  . "		grid-row: 1;\n"
			  . "		grid-column: 1;\n"
			  . "	}\n\n"
			  . "	#navigation {\n"
			  . "		grid-row: 2;\n"
			  . "		grid-column: 1 / span 2;\n"
			  . "	}\n\n";
	
		$view->stream_append('css', $css);
	
		$css_print = "    	#navigation { display: none; }\n";
		$view->stream_append('css-print', $css_print);
		
		$fixed->_render($document, $view);
		
		$body = "	<nav id=\"navigation\">\n";
		$view->stream_append('html-body', $body);
		
		$part = $this->parent->children[0];
			$part->_render($document, $view);
		
		$body = "	</nav>\n";
		$view->stream_append('html-body', $body);
		
		
/*		for ($i = 1; $i < $childcount; $i++) {
			$part = $this->parent->children[$i];
			$part->_render($document, $view);
		}*/
	}
}

?>
