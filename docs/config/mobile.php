<?php

$font = 'font-family: verdana,sans-serif; font-size: 48px; line-height: 1.2;';

$view = new idg_view_html;
$view->set_properties(array(
	'name' => 'mobile',
	'icon' => 'favicon.png'
));

$template = new idg_view_html_template;
$template->set_properties(array('class' => 'fixedcontent',
    'style' => $font . ' background: #b4d2b0;'));

$view->add_child($template);

$text_box = new idg_view_html_container;
$text_box->set_properties(array('name' => 'background', 
    'style' => 'text-align: left; height: 100%; padding-top: 130px;',
     'style-head' => 'text-align:right; background: #99b6a9; font-size: 140%;'
        . 'margin-left: -20px; margin-right: -20px; padding-right: 20px;',
     'style-subhead' => 'font-size: 120%; padding-top: 24px;'));
    
$navig = new idg_view_html_renderer;
$navig->set_properties(array('class' => 'mobile_navigation',
    'source' => '_site'));
$text_box->add_child($navig);

$main_text = new idg_view_html_slot;
$main_text->set_properties(array('name' => 'main-text',
    'style' => ' margin: 40px; padding: 0 20px 20px 20px; background: #afccbf;',
    'style-link' => 'text-decoration: underline; font-weight: bold; '
	        . 'color: #25253d; font-style: italic;',
    'style-link-hover' => 'color: #909090;',
    'filter' => 'mobile_filter_preformat'));
$text_box->add_child($main_text);

$template->add_child($text_box);

?>
