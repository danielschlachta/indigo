<?php

$pagemap_main = $idg_path . '/modules/pagemap/dist/pagemap.min.js';

if (!file_exists($pagemap_main)) {
   	complain_module('pagemap', $pagemap_main, 
		'https://github.com/lrsjng/pagemap.git');
	exit;
}

class idg_pagemap 
{
	static function get_css($position = 'top') {
		return "	#map {\n"
			. "		position: fixed;\n"
			. "		$position: 0;\n"
			. "		right: 0;\n"
			. "		width: 25%;\n"
			. "		height: 90%;\n"
			. "		z-index: 200;\n"
			.	"	}\n\n";
	}
	
	static function get_html_body() {
		global $pagemap_main;
		
		return "<canvas id=\"map\"></canvas>\n"
			. "<script src=\"$pagemap_main\"></script>\n"
			. "<script>pagemap(document.querySelector('#map'));</script>\n";
	}
	
	static function add_to_view($view, $position = 'top') {
		$view->stream_append('css', idg_pagemap::get_css($position));
		$view->stream_append('html-body', idg_pagemap::get_html_body());
	}
}

?>
