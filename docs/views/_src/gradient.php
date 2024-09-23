<?php

function configure_view(idg_view $view): void {
    $font = "font-family: georgia,serif; font-size: 100%; line-height: 1.2";
    $elements = "views/gradient/elements";

    $view->set_properties([
        'class' => $view->template()->qualify('view'),
        'icon' => 'favicon.png',
        'style' => "$font; background-color: #46467d"
    ]);

    $fixed = new idg_container;
    $fixed->set_properties([
        'style' => "background: #53538a url($elements/lighthouse.jpg)"
        . " bottom left fixed no-repeat;"
    ]);
    $view->add_child($fixed);
    
    $navigation = new idg_fragment;
    $navigation->set_properties([
        'class' => $view->template()->qualify('navigation'),
        'source' => '_site'
    ]);
    $fixed->add_child($navigation);
    
    $main = new idg_container;
    $main->set_properties([
        'name' => 'content',
        'style' => "width: 100%; height: 100%; margin-left: auto; "
        . "margin-right: auto;"
        . " background: #53538a url($elements/bg_grad.png)"
        . ' top left fixed repeat-x;'
    ]);
    $view->add_child($main);

    $headline = new idg_fragment;
    $headline->set_properties([
        'class' => '\Indigo\Fragment\text',
        'style' => 'width: 94%; position: relative; left: 40px; top: 15px;'
        . 'font-size: 130%; font-weight: bold; color: #3b456d'
    ]);
    $headline->set_text('{description}');
    $main->add_child($headline);

    $text_box = new idg_container;
    $text_box->set_properties([
        'name' => 'background',
        'style' => 'text-align: center; height: 100%; padding-bottom: 3em;'
    ]);
    $main->add_child($text_box);

    $fill_box = new idg_container;
    $fill_box->set_properties([
        'name' => 'fillbox',
        'style' => "width: 56%; height: 100%;"
        . " margin: 40px 0 0 60px; padding: 0 20px 0 20px;"
        . " text-align: justify; font-size: 118%;"
        . " border-top: 1px solid #25253d; border-left: 1px solid #25253d;"
        . " border-right: 1px solid #25253d; background: #ffffff"
        . " url($elements/bg_grad_2.png) bottom left fixed repeat-x;",
        'style-h1' => "font-size: 130%;",
        'style-h2' => "font-size: 100%;"
    ]);
    $text_box->add_child($fill_box);

    $main_text = new idg_slot;
    $main_text->set_properties([
        'name' => 'main-text',
        'style-a' => "text-decoration: underline; font-weight: bold;"
        . " color: #25253d; font-style: italic;",
        'style-a-hover' => "color: #c2c2c2;",
        //'filter' => 'gradient_filter_blockquote'
    ]);
    $fill_box->add_child($main_text);

    $filter_blockquote = new idg_filter;
    $filter_blockquote->set_properties([
        'name' => 'filter_blockquote',
        'apply' => 'yes'
    ]);
    $filter_blockquote->load('gradient_filter_blockquote.php');
    $fill_box->add_child($filter_blockquote);

    $footer = new idg_fragment;
    $footer->set_properties([
        'class' => '\Indigo\Fragment\text',
        'style-a' => "text-decoration: none; color: #25253d;",
        'style-a-hover' => "text-decoration: underline; color: #50503d;"
    ]);
    $footer->set_text("<div style=\"height: 2em;\"><p><span style=\"font-size: 90%;\">"
        . "<a href=\"#top\"><img src=\"$elements/hand.png\""
        . "style = \"float: left; margin-top: 2px; padding-right: 10px;\" alt=\"\">"
        . "<i><b>back to the top of the page</b></i></a></span></p></div>");

    $fill_box->add_child($footer);
    
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
        . "font-family: sans; font-style: italic; font-size: 120%;"
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
}

if (!@$_SERVER['HTTP_USER_AGENT']) {
    require '../../../indigo/startup.php';
    $view = new idg_view;
    $view->template()->set_namespace('Indigo\Design\Gradient');
    configure_view($view);
    $view->write_xml('gradient.xml');
}
