<?php

$font = 'font-family: georgia,serif; font-size: 100%; line-height: 1.2;';

$view = new idg_view_html;
$view->set_properties(array('name' => 'gradient', 'icon' => 'favicon.png'));

$part = new idg_view_html_part;
$part->set_properties(array('class' => 'fixedbar',
    'style' => $font . 'background-color: #46467d'
    ));

$fixed_pos = 'left';
$part->set_text("fixed-width: 100px; fixed-position: $fixed_pos;");

$view->add_child($part);

$fixed = new idg_view_html_container;

$fixed->set_properties(array('name' => 'lighthouse', 
    'style' => 'background: #53538a url(elements/gradient/lighthouse.jpg)'
	    ." bottom $fixed_pos fixed no-repeat;"));

$navig = new idg_view_html_renderer;
$navig->set_properties(array('class' => 'gradient_navigation',
    'source' => '_site'));
$fixed->add_child($navig);

$logo = new idg_view_html_container;
$logo->set_properties(array(
	'name' => 'logo',
	'style' => 'position: fixed; text-align: right; top: 20px;' . ' right: 20px; z-index: 120;',
	'style-print' => 'position: absolute; top: 0; right: 0;'
));

$logo_text = new idg_view_html_item;
$logo_text->set_properties(array(
	'class' => 'text',
	'style' => 'padding-right: 8px; ' 
	  . 'font-size: 120%; font-family: verdana, sans-serif; color: #394a71; font-style: italic;'
));
$logo_text->set_text('&lt;b&gt;indigo&lt;/b&gt; / web');

$logo->add_child($logo_text);

$logo_image = new idg_view_html_item;
$logo_image->set_properties(array(
	'class' => 'image'
));
$logo_image->set_text('source: elements/gradient/indigo.png; width: 90; height: 70;');

$logo->add_child($logo_image);

$main = new idg_view_html_container;
$main->set_properties(array('name' => 'content',
	'style' =>  'width: 100%; height: 100%; margin-left: auto; ' 
	    . 'margin-right: auto;' 
	    . ' background: #53538a url(../designs/gradient/elements/bg_grad.png)' 
	    . ' top left fixed repeat-x;'));


$headline = new idg_view_html_item;
$headline->set_properties(array('class' => 'text',
	'style' =>  'width: 94%; position: relative; left: 140px; top: 15px;'
	    . 'font-size: 130%; font-weight: bold; color: #3b456d'));
$headline->set_text('{description}');

$main->add_child($headline);

$text_box = new idg_view_html_container;
$text_box->set_properties(array('name' => 'background', 
    'style' => 'text-align: center; height: 100%;'));

$fill_box = new idg_view_html_container;
$fill_box->set_properties(array('name' => 'fillbox', 
    'style' => 'width: 56%; height: 100%;' 
        . 'margin-left: 160px; padding: 0 20px 0 20px;' 
        . 'margin-top: 40px; text-align: justify; font-size: 118%; '
        . 'border-top: 1px solid #25253d; border-left: 1px solid #25253d; '
	    . 'border-right: 1px solid #25253d; background: #ffffff '
        . 'url(elements/gradient/bg_grad_2.png) bottom left ' 
        . 'fixed repeat-x;',
     'style-head' => 'font-size: 130%;',
     'style-subhead' => 'font-size: 100%;'));

$main_text = new idg_view_html_slot;
$main_text->set_properties(array(
	'name' => 'main-text',
    'style-link' => 'text-decoration: underline; font-weight: bold; '
	        . 'color: #25253d; font-style: italic;',
	'style-link-hover' => 'color: #c2c2c2;',
	'filter' => 'gradient_filter_blockquote'));

$fill_box->add_child($main_text);

$footer = new idg_view_html_renderer;
$footer->set_properties(array(
	'class' => 'footer',
	'source' => '_site'));

$fill_box->add_child($footer);

$text_box->add_child($fill_box);

$bottom = new idg_view_html_container;
$bottom->set_properties(array('name' => 'bottom',
	'style' =>  'height: 70px;'));

$text_box->add_child($bottom);

$main->add_child($text_box);

$part->add_child($fixed);
$part->add_child($main);
$part->add_child($logo);

?>
