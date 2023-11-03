<?php

function configure_view(idg_view $view): void {
    $font_sans = "font-family: 'DejaVu Sans', sans-serif";
    $elements = 'views/blocks/elements';

    $view->set_properties([
        'icon' => "favicon.png",
        'style' => "font-family: 'DejaVu Serif', georgia, serif; font-size: 120%; " 
        . "line-height: 1.2; " 
        . "background: #53538a url($elements/bg_grad.png) fixed repeat-x;"
    ]);

    $filter_links = new idg_filter;
    $filter_links->set_properties([
        'name' => 'filter_links'
    ]);
    $filter_links->load('blocks_filter_links.php');
    $view->add_child($filter_links);
    
    $centercol = new idg_container;
    $centercol->set_properties([
        'style' => "position: relative; top: 0; left: 0; " 
        . "margin-left: 130px; margin-right: 330px;",
        'style-a' => "text-decoration: none; color: black; " 
        . "background: url($elements/underline.png) bottom left repeat-x; " 
        . "padding-bottom: 1px; padding-right: 1px; margin-right: -1px; " 
        . "white-space: nowrap;",
        'style-a-hover' => "background: #729e8d " 
        . "url($elements/underline.png) bottom left repeat-x;",
        'style-print' => "margin: 0;",
    ]);
    $view->add_child($centercol);
    
    $tabs = new idg_fragment;
    $tabs->set_properties([
        'class' => $view->qualify('tabs'),
        'source' => '_site',
        'tag' => 'main',
        'style' => "$font_sans; font-size: 120%;"
    ]);
    $centercol->add_child($tabs);

    $centercol_body = new idg_container;
    $centercol_body->set_properties([
        'name' => 'center_body',
        'style' => "height: 100%; border: solid #1e3723; border-width: 0px 1px 1px 1px; "
        . "padding: 0 30px 30px 23px; " 
        . "background: #b4d2b0 url($elements/line_red.png) repeat-y; " 
        . "text-align: justify;",
        'style-h1' => "padding: 0.35em 0.5em 0.35em 0; " 
        . "border: solid #729e8d; border-width: 2px 0 2px 0; " 
        . "background: url($elements/randomsymbols_bg.jpg); " 
        . "margin: 0; padding: 0.5em 15px 0.4em 0; text-align: right; "
        . "font-family: 'DejaVu Serif', 'Noto Serif', serif; " 
        . "font-style: italic; font-weight: bold; font-size: 130%;",
        'style-h2' => "padding: 0 1em 0.1em 0; " 
        . "border: solid #729e8d; border-width: 1px 0 1px 0; " 
        . "background: url($elements/randomsymbols_bg.jpg); " 
        . "margin: 0; text-align: right; " 
        . "font-family: 'Noto Serif', serif; " 
        . "font-style: italic; font-weight: bold; font-size: 110%;",
        'style-print' => "border: 0; padding: 1em; margin: 100px 0 0 0;"
    ]);
    $centercol->add_child($centercol_body);

    $main_text = new idg_slot;
    $main_text->set_properties([
        'name' => 'main-text',
        'style' => "padding: 0 0 0 17px; " 
        . "background: url($elements/line_blue.png);",
        'style-list-image' => "$elements/bullet.png",
    ]);
    $centercol_body->add_child($main_text);

    $filter_blockquote = new idg_filter;
    $filter_blockquote->set_properties([
        'name' => 'filter_blockquote',
        'apply' => 'yes'
    ]);
    $filter_blockquote->load('blocks_filter_blockquote.php');
    $centercol_body->add_child($filter_blockquote);

    $filter_links_body = new idg_filter;
    $filter_links_body->set_properties([
        'name' => 'filter_links'
    ]);
    $centercol_body->add_child($filter_links_body);
   
    $footer_container = new idg_container;
    $footer_container->set_properties([
        'name' => 'footer-container',
        'style' => "background: #b4d2b0 "
        . "url($elements/line_red.png) repeat-y;"
        . "padding: 15px 25px 0 35px; margin: 6px 1px 0 1px;",
    ]);
    $centercol->add_child($footer_container);

    $footer = new idg_fragment;
    $footer->set_properties([
        'class' => $view->qualify('footer'),
        'name' => 'footer',
        'tag' => 'Copyright &copy;2023 Daniel Schlachta',
        'style' => "background-image: url($elements/metal.png); "
        . " $font_sans; font-size: 70%; padding: 5px;"
    ]);
    $footer_container->add_child($footer);
    
    $filter_links_footer = new idg_filter;
    $filter_links_footer->set_properties([
        'name' => 'filter_links'
    ]);
    $footer->add_child($filter_links_footer);
    
    $logo = new idg_container;
    $logo->set_properties([
        'name' => 'logo',
        'style' => "position: fixed; text-align: right; top: 20px; right: 20px;",
        'style-print' => "position: absolute; top: 0; right: 0;"
    ]);

    $logo_text = new idg_fragment;
    $logo_text->set_properties([
        'class' => '\Indigo\Fragment\text',
        'style' => "color: #394a71; padding-right: 8px; " 
        . "font-family: sans; font-style: italic;"
    ]);
    $logo_text->set_text('<b>indigo</b> / web');

    $logo->add_child($logo_text);

    $logo_image = new idg_fragment;
    $logo_image->set_properties([
        'class' => '\Indigo\Fragment\image'
    ]);
    $logo_image->set_parameter('src', "$elements/indigo.png");

    $logo->add_child($logo_image);

    $view->add_child($logo);

    $imageframe = new idg_fragment;
    $imageframe->set_properties([
        'class' => $view->qualify('imageframe')
    ]);
    $attr = $imageframe->create_attribute('blocks::image'); // could be just 'image'
    $attr->set_parameter('top', 30);
    $attr->set_parameter('left', 30);
    $view->add_child($imageframe);

    $infobox = new idg_fragment;
    $infobox->set_properties([
        'class' => $view->qualify('infobox'),
        'source' => 'blocks::infobox',
        'style' => "$font_sans; font-size: 110%;"
    ]);
    $attr = $infobox->create_attribute('blocks::infobox');
    $attr->set_parameter('bg-url', "$elements/arms.png");
    $view->add_child($infobox);

    $dropdown = new idg_fragment;
    $dropdown->set_properties([
        'class' => $view->qualify('dropdown'),
        'tag' => 'resources',
        'source' => '_site',
        'style' => "position: fixed; top: 160px; right: 30px; z-index: 1;" 
        . "$font_sans;"
    ]);
    $attr = $dropdown->create_attribute('dropdown');
    $attr->set_parameter('bg-url', "$elements/compass.png");
    $view->add_child($dropdown);

    /*
    $searchbox = new idg_fragment;
    $searchbox->set_properties([
        'class' => 'searchbox'
    ]);
    $view->add_child($searchbox);

    $toolbox = new idg_fragment;
    $toolbox->set_properties([
        'class' => 'toolbox'
    ]);
    $view->add_child($toolbox);
*/
    $background_img = new idg_container;
    $background_img->set_properties([
        'name' => 'image-bg'
    ]);
    $view->add_child($background_img);

    $bg_sky_container = new idg_container;
    $bg_sky_container->set_properties([
        'name' => 'image-sky',
        'style' => "position: fixed; " 
        . "top: 49px; right: 212px; width: 629px; height: 80px; " 
        . "background: url($elements/bg_sky.png) no-repeat; z-index: -1;"
    ]);
    $background_img->add_child($bg_sky_container);

    $bg_left = new idg_container;
    $bg_left->set_properties([
        'name' => 'image-graph-l',
        'style' => "position: fixed; " 
        . "top: 352px; left: 0px; width: 130px; height: 75px; " 
        . "background: url($elements/bg_graph_l.png) no-repeat;"
    ]);
    $background_img->add_child($bg_left);

    $bg_right = new idg_container;
    $bg_right->set_properties([
        'name' => 'image-graph-r',
        'style' => "position: fixed; " 
        . "top: 352px; right: 0px; width: 330px; height: 130px; " 
        . "background: url($elements/bg_graph_r.png) no-repeat;"
    ]);
    $background_img->add_child($bg_right);
}

if (!@$_SERVER['HTTP_USER_AGENT']) {
    require '../../indigo/startup.php';
    $view = new idg_view;
    $view->set_namespace('Indigo\Design\Blocks');
    configure_view($view);
    $view->write_xml('blocks/blocks.xml');
}
