<?php

$font = 'font-family: georgia,serif; font-size: 120%; line-height: 1.2;';

$view = new idg_view_html;
$view->set_properties(array(
	'name' => 'blocks',
	'icon' => 'favicon.png',
	'style' => $font . ' background: #53538a ' . 'url(elements/blocks/bg_grad.png) ' . 'fixed repeat-x;'
));

$part = new idg_view_html_part;
$part->set_properties(array(
	'class' => 'fixedcontent'
));
$view->add_child($part);

$centercol = new idg_view_html_container;
$centercol->set_properties(array(
	'name' => 'center',
	'style' => 'position: relative; top: 0; left: 0; margin-left: 130px; margin-right: 330px; z-index: 201;',
	'style-print' => 'margin: 0;'
));

$part->add_child($centercol);

$tabs = new idg_view_html_renderer;
$tabs->set_properties(array(
	'class' => 'tabs',
	'source' => '_site'
));
$centercol->add_child($tabs);

$style_link = 'text-decoration: none; color: black; background: url(elements/blocks/underline.png) bottom left repeat-x; padding-bottom: 1px; padding-right: 1px; margin-right: -1px; white-space: nowrap;';
$style_link_hover = 'background: #729e8d url(elements/blocks/underline.png) bottom left repeat-x;';

$centercol_body = new idg_view_html_container;
$centercol_body->set_properties(array(
	'name' => 'center_body',
	'style' => 'height: 100%; border: solid #1e3723; border-width: 0px 1px 1px 1px; padding: 0 30px 30px 23px; background: #b4d2b0 url(elements/blocks//line_red.png) repeat-y; text-align: justify;',
	'style-head' => 'padding: 0.35em 0.5em 0.35em 0; border: solid #729e8d; border-width: 2px 0 2px 0; background: url(elements/blocks/randomsymbols_bg.jpg); margin: 0 0 0.5em 0; text-align: right; font-weight: normal; font-style: italic; font-size: 150%;',
	'style-subhead' => 'padding: 0.1em 1em 0.3em 0; border: solid #729e8d; border-width: 1px 0 1px 0; background: url(elements/blocks/randomsymbols_bg.jpg); margin: 0 0 0.5em 0; text-align: right; font-weight: bold; font-size: 100%;',
	'style-image' => 'border: 0;',
	'style-print' => 'border: 0; padding: 1em; margin: 100px 0 0 0;',
	'filter' => 'blocks_filter_linkimg'
));

$centercol->add_child($centercol_body);

$main_text = new idg_view_html_slot;
$main_text->set_properties(array(
	'name' => 'main-text',
	'style' => 'padding: 0 0 0 17px; background: url(elements/blocks/line_blue.png);',
	'style-link' => $style_link,
	'style-link-hover' => $style_link_hover,
	'list-style-image' => 'elements/blocks/bullet.png',
	'filter' => 'blocks_filter_blockquote'
));
$centercol_body->add_child($main_text);

$footer = new idg_view_html_renderer;
$footer->set_properties(array(
	'class' => 'blocks_footer',
	'source' => '_site',
	'style-link' => $style_link . ' height: 1.2em;',
	'style-image' => 'margin-bottom: -1px; border: 0;',
	'style-link-hover' => $style_link_hover
));
$centercol->add_child($footer);

$logo = new idg_view_html_container;
$logo->set_properties(array(
	'name' => 'logo',
	'style' => 'position: fixed; text-align: right; top: 20px;' . ' right: 20px; z-index: 120;',
	'style-print' => 'position: absolute; top: 0; right: 0;'
));

$logo_text = new idg_view_html_item;
$logo_text->set_properties(array(
	'class' => 'text',
	'style' => 'padding-right: 8px; font-family: verdana, sans-serif; color: #394a71; font-style: italic;'
));
$logo_text->set_text('&lt;b&gt;indigo&lt;/b&gt;&amp;nbsp;/ web');

$logo->add_child($logo_text);

$logo_image = new idg_view_html_item;
$logo_image->set_properties(array(
	'class' => 'image'
));
$logo_image->set_text('source: elements/blocks/indigo.png; width: 90; height: 70;');

$logo->add_child($logo_image);

$part->add_child($logo);

$framedimg = new idg_view_html_renderer;
$framedimg->set_properties(array(
	'class' => 'blocks_framedimg',
	'source' => 'framedimg'
));

$part->add_child($framedimg);


$infobox = new idg_view_html_renderer;
$infobox->set_properties(array(
	'class' => 'blocks_infobox',
	'source' => 'infobox'
));
$part->add_child($infobox);

$navig = new idg_view_html_renderer;
$navig->set_properties(array(
	'class' => 'blocks_navigation',
	'source' => '_site',
	'tag' => 'resources'
));
$part->add_child($navig);

$searchbox = new idg_view_html_item;
$searchbox->set_properties(array(
	'class' => 'searchbox'
));

$part->add_child($searchbox);

$toolbox = new idg_view_html_item;
$toolbox->set_properties(array(
	'class' => 'toolbox'
));

$part->add_child($toolbox);

$background_img = new idg_view_html_container;
$background_img->set_properties(array(
	'name' => 'image-bg'
));
$part->add_child($background_img);

$bg_sky_container = new idg_view_html_container;
$bg_sky_container->set_properties(array(
	'name' => 'image-sky',
	'style' => 'position: fixed; top: 49px; right: 212px; width: 629px; height: 80px; background: url(elements/blocks/bg_sky.png) no-repeat; z-index: 101;'
));

$background_img->add_child($bg_sky_container);

$bg_left = new idg_view_html_container;
$bg_left->set_properties(array(
	'name' => 'image-graph-l',
	'style' => 'position: fixed; top: 352px; left: 0px; ' . 'width: 130px; height: 75px; ' . 'background: url(elements/blocks/bg_graph_l.png) no-repeat; z-index: 101;'
));

$background_img->add_child($bg_left);

$bg_right = new idg_view_html_container;
$bg_right->set_properties(array(
	'name' => 'image-graph-r',
	'style' => 'position: fixed; top: 352px; right: 0px; ' . 'width: 330px; height: 130px; ' . 'background: url(elements/blocks/bg_graph_r.png) no-repeat; z-index: 101;'
));

$background_img->add_child($bg_right);

?>
