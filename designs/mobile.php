<?php

require_once('mobile/navigation.php');

$elements = $idg_path . '/../designs/mobile/elements';

function mobile_filter_preformat(&$text)
{
	$output = str_replace('<pre style="', '<pre style="font-size: 60%; ', $text);
	
	return $output;
}

?>
