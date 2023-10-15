<?php

$view = new idg_view_html;
$view->set_properties(array(
	'name' => 'minimal',
	'icon' => 'favicon.png',
	'tag' => 'elements/minimal/logo.png\indigo\&amp;copy; 2023 '
		. ' &lt;a href=\'mailto:daniel@schlachta.info\'&gt;' 
		. ' Daniel Schlachta&lt;/a&gt;'
));

$template = new idg_view_html_template;
$template->set_properties(array(
	'class' => 'minimal'
));
$view->add_child($template);

$body = new idg_view_html_container;
$body->set_properties(array(
	'name' => 'body'
));

$template->add_child($body);

$navi = new idg_view_html_renderer;
$navi->set_properties(array(
	'class' => 'minimal_navigation',
	'source' => '_site'
));
$body->add_child($navi);

$text = new idg_view_html_container;
$text->set_properties(array(
	'name' => 'text'
));

$body->add_child($text);

$footer = new idg_view_html_renderer;
$footer->set_properties(array(
	'class' => 'minimal_footer',
	'source' => '_site'
));
$body->add_child($footer);

$main_text = new idg_view_html_slot;
$main_text->set_properties(array(
	'name' => 'main-text',
));
$text->add_child($main_text);

?>
