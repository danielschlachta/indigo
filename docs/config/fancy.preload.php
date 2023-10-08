<?php

function fancy_filter_typo(&$text)
{
	$output = str_replace(' - ', '&mdash;', $text);
	$output = str_replace('&ldquo;', '&laquo;', $output);
	$output = str_replace('&rdquo;', '&raquo;', $output);
	$output = str_replace('...', '&hellip;', $output);
	$output = str_replace('\'', '&rsquo;', $output);
	
	$output = str_replace('<blockquote>',
		'<blockquote><img src="elements/fancy/blockquote.png" alt="">', $output);
	
	return $output;
}

function fancy_filter_eat_caption(&$text) {
	 return idg_renderer_fancy_caption::filter_eat_caption($text);
}


?>
