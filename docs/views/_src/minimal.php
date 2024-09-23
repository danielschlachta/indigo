<?php

function configure_view(idg_view $view): void {
    $view->set_properties([
        'class' => $view->template()->qualify('view'),
        'icon' => 'favicon.png',
        'tag' => "views/minimal/elements/style.css"
    ]);

    $body = new idg_container;
    $body->set_properties([
        'name' => 'body'
    ]);
    $view->add_child($body);

    $navigation = new idg_fragment;
    $navigation->set_properties([
        'class' => $view->template()->qualify('navigation'),
        'source' => '_site'
    ]);
    $attribute = $navigation->create_attribute('minimal::navigation');
    $attribute->set_parameter('image', 'views/minimal/elements/logo.png');
    $attribute->set_parameter('title', 'indigo');    
    $attribute->set_parameter('subtitle', "Copyright &copy;2023 " 
        . "<a href=\"mailto:Daniel Schlachta<daniel@schlachta.info>\">" 
        . " Daniel Schlachta</a>");    
    
    $body->add_child($navigation);

    $text = new idg_container;
    $text->set_properties([
        'name' => 'text'
    ]);
    $body->add_child($text);

     $main_text = new idg_slot;
    $main_text->set_properties([
        'name' => 'main-text',
    ]);
    $text->add_child($main_text);
    
    $footer = new idg_fragment;
    $footer->set_properties([
        'class' => $view->template()->qualify('footer'),
    ]);
    $body->add_child($footer);
}

if (!@$_SERVER['HTTP_USER_AGENT']) {
    require '../../../indigo/startup.php';
    $view = new idg_view;
    $view->template()->set_namespace('Indigo\Design\Minimal');
    configure_view($view);
    $view->write_xml('minimal.xml');
}
