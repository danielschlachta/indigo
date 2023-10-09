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

/*!
 * Create a caption from a datasource.
 * 
 * Renderer that scans its datasource
 * for occurences of \c &lt;h1> and emits only the
 * first one and its contents, decorated in a way
 * that is suitable for use as a headline with
 * idg_view_html_part_fancy.
 * 
 * Takes the following options (set with \c set_option or \c set_options):
 *  + \c font-family
 *  + \c background-color
 *  + \c image
 * 
 * Use \c filter_eat_caption to eliminate the
 * same content from a different datasource.
 * */

class idg_renderer_fancy_caption extends idg_view_node_obj
{
	function __construct(&$parent)
	{
		parent::__construct($parent);
	}

	/*! 
	 * Consumes the first \c &lt;h1> tag and its contents.
	 * 
	 */

	static function filter_eat_caption(&$text)
	{
		global $caption_preg;
		global $caption_eaten;
		
		if (!$caption_eaten && preg_match($caption_preg, $text)) {
			$output = preg_replace($caption_preg, '', $text);
			$caption_eaten = true;
			return $output;
		}
	}

	function render(&$document, &$view)
	{
		global $caption_preg;

		$decl = $view->get_child_by_key('name', 'caption');
		$font = fancy_options::get_font($decl);
		$image = $decl->get_option('image');
		
		$this->datasource->rewind();
		
		$css = "	.header {\n" 
			. "		padding: 25px 5px 25px 0;\n"
			. "		margin: 0;\n"
			. "	}\n\n"
			. "	.caption {\n" 
			. "		font-family: '$font';\n" 			  
			. "		font-size: 100%;\n"
			. "		font-weight: normal;\n"
			. "		padding-left: 1em;\n"
			. "		margin: -1em 0 -1em 0;\n";
			
		if ($image)
			$css .= "		background: url($image) no-repeat;\n" 
			. "		background-position: right center;\n";
			
		$css .= "		margin-right: 10px;\n"
			. "	}\n\n";
		
		$view->stream_append('css', $css);
		
		while ($token = $this->datasource->get_token()) {
			if (preg_match($caption_preg, $token->get_data(), $match)) {
				$caption = preg_replace('|[ \r\n]*$|', '', $match[0]);
				
				$view->stream_append('html-body', 
					"<div class=\"caption\">$caption</div>\n");
			}
		} 
	}
}

?>
