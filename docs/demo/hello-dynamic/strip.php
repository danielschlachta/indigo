<?php

$font = 'font-family: georgia, serif; font-size: 120%;';

$view->set_properties(array(
	'name' => 'strip',
	'icon' => 'favicon.png',
	'style' => $font . ' background: #53538a;'
));

$template = new idg_view_html_part;
$template->set_properties(array(
	'class' => 'fixedcontent'
));

$view->add_child($template);

$body = new idg_view_html_container;
$body->set_properties(array(
	'name' => 'body',
	'style' => 'height: 80%; ' 
	    . 'border: solid #1e3723; background: #b4d2b0;'  
	    . 'border-width: 0px 1px 1px 1px; padding: 30px;'
));

$template->add_child($body);

$main_text = new idg_view_html_slot;
$main_text->set_properties(array(
	'name' => 'main-text',
	'style' => 'padding: 0 0 0 17px;'
));

$body->add_child($main_text);

?>
