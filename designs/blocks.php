<?php

require_once('blocks/footer.php');
require_once('blocks/framedimg.php');
require_once('blocks/infobox.php');
require_once('blocks/navigation.php');
require_once('blocks/searchbox.php');
require_once('blocks/tabs.php');
require_once('blocks/toolbox.php');

if (@$font_blocks == null)
	$font_blocks = 'font-family: verdana, sans-serif;';

$elements = '../designs/blocks/elements';

function blocks_filter_linkimg(&$text)
{
	$image = '<img src="elements/blocks/images/link.png" ' 
		. 'width="11" height="10" alt="">';
	$ext_image = '<img src="elements/blocks/images/link_ext.png" ' 
		. 'width="11" height="10" alt="">';
	
	if (preg_match('/(<a[^hH]+href=["\']([^"]+)["\'][^>]*)>/', $text, $match)) {
		$output = preg_replace('/(<a[^hH]+href=["\']http[^>]*)>/', "\\1>$ext_image", $text);
		$output = preg_replace('/(<a[^hH]+href=["\'][^h][^t][^t][^p][^>]*)>/', "\\1>$image", $output);
	} else
		$output = false;
	
	return $output;
}

function blocks_filter_blockquote(&$text)
{
	$output = str_replace('<blockquote>', 
		'<div style="background: url(elements/blocks/images/bg_div.png); ' 
		. 'width: 100%; height: 100%; border: solid black; border-width: 1px;">'
		. '<blockquote style="margin-left: 1em;">', $text);
			
	$output = str_replace('</blockquote>', '</blockquote>'
		. '<img src="elements/blocks/images/lightbulb.png" alt="" style="float: right; ' 
		. 'position: relative; bottom: 40px; right: 2px;"></div>', $output);
	
	return $output;
}

?>
