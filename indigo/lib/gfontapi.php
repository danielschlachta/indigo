<?php

/* ========================================================================
 * Indigo/Web
 *
 * File: gfontapi.php - simplifies work with google fonts
 *
 * (c) 2023 Daniel Schlachta
 * ======================================================================== */

class google_font_api 
{
	var $fonts = array();
	
	function register_font($name) {
		$this->fonts[$name] = true;
	}
	
	function get_header_lines() {
		$output = '';	
		
		foreach ($this->fonts as $font => $value) {
			$font_url = str_replace(' ', '+', $font);
			$output .= " <link href=\"https://fonts.googleapis.com/css2?"
			. "family=$font_url&display=swap\" rel=\"stylesheet\">\n";
		}
	
		if ($output != '') 
			$output = "\n" .
				' <link rel="preconnect" href="https://fonts.gstatic.com">'
				. "\n$output\n";
		
		return $output;
	}
}

?>
