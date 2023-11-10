<?php

function configure_view(idg_view $view): void {
    $font = 'font-family: verdana,sans-serif; font-size: 48px; line-height: 1.2;';

    $view->set_properties([
        'class' => $view->core()->qualify('view'),
        'icon' => 'favicon.png',
        'style' => $font . ' background: #b4d2b0;'
    ]);

    $navig = new idg_fragment;
    $navig->set_properties([
        'class' => $view->core()->qualify('navigation'),
        'source' => '_site'
    ]);
    $view->add_child($navig);
    
    $text_box = new idg_container;
    $text_box->set_properties([
        'style' => 'text-align: left; height: 100%;',
        'style-h1' => 'text-align:right; background: #99b6a9; font-size: 140%;'
        . 'margin-left: -20px; margin-right: -20px; padding-right: 20px;',
        'style-h2' => 'font-size: 120%; padding-top: 24px;'
    ]);
    $view->add_child($text_box);

    $main_text = new idg_slot;
    $main_text->set_properties([
        'name' => 'main-text',
        'style' => ' margin: 40px; padding: 0 20px 20px 20px; background: #afccbf;',
        'style-a' => 'text-decoration: underline; font-weight: bold; '
        . 'color: #25253d; font-style: italic;',
        'style-a-hover' => 'color: #909090;'
    ]);
    $text_box->add_child($main_text);
}

if (!@$_SERVER['HTTP_USER_AGENT']) {
    require '../../../indigo/startup.php';
    $view = new idg_view;
    $view->core()->set_namespace('Indigo\Design\Mobile');
    configure_view($view);
    $view->write_xml('mobile.xml');
}
