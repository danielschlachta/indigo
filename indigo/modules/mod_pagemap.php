<?php

$pagemap_main = $idg_path . '/modules/pagemap/dist/pagemap.min.js';

if (!file_exists($pagemap_main)) {
   	complain_module('pagemap', $pagemap_main, 
		'https://github.com/lrsjng/pagemap.git');
	exit;
}

class idg_pagemap 
{
	static function map() {
		global $pagemap_main;
		
		return "<canvas id=\"map\"></canvas>\n"
			. "<script src=\"$pagemap_main\"></script>\n"
			. "<script>pagemap(document.querySelector('#map'));</script>\n";
	}
}

?>
