<?php

function fancy_get_footer_tag() {
	global $document;
	
	$date = new DateTimeImmutable($document->get_property('last-change'));
	
	return 'This page was last updated on ' 
		. $date->format('m/d/Y')
		. '; copyright 2010&ndash;2023 ' 
		. '<a href="mailto:daniel@schlachta.info">Daniel Schlachta</a>.';
}

$footer = $view->get_child_by_key('name', 'footer');
$footer->type_obj->add_hook('tag', 'fancy_get_footer_tag');

?>
