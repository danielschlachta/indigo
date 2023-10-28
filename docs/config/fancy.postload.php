<?php

require_once($idg_path . '/../designs/fancy.php');

function fancy_hook_get_footer_tag() {
	global $document;

	$date = new DateTimeImmutable($document->get_property('last-change'));

	return 'This page was last updated on '
		. $date->format('m/d/Y')
		. '. Copyright (c) 2023 '
		. '<a href="mailto:Daniel Schlachta <daniel@schlachta.info>">Daniel Schlachta</a>';
}


$footer = $view->get_child_by_key('name', 'footer');
$footer->set_property_hook('tag', 'fancy_hook_get_footer_tag');

?>
