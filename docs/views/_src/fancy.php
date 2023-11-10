<?php

function configure_view(idg_view $view): void {
    $elements = "view/fancy/elements";
    
    $view->set_properties([
        'class' => $view->core()->qualify('view'),
        'icon' => 'favicon.png',
        'style' => "background-color: #fff6e8;",
        'style-h1' => "font-size: 210%;",
        'style-h2' => "font-size: 140%;"
    ]);

    $filter_typo = new idg_filter;
    $filter_typo->set_properties([
        'name' => 'filter_typo',
        'apply' => 'yes'
    ]);
    $filter_typo->load('fancy_filter_typo.php');
    $view->add_child($filter_typo);

    $filter_date = new idg_filter;
    $filter_date->set_properties([
        'name' => 'filter_date',
        'apply' => 'yes'
    ]);
    $filter_date->load('fancy_filter_date.php');
    $view->add_child($filter_date);

    /*
     *  Add the navigation first as it normally appears like that on
     *  the screen. The order does not really matter here,
     *  the grid view will take care of that.
     */
    $nav = new idg_fragment;
    $nav->set_properties([
        'name' => 'nav',
        'class' => $view->core()->qualify('nav'),        
        'source' => '_site',
        'style' => "background-color: #ffe7d6;"
    ]);
    $view->add_child($nav);

    $header = new idg_fragment;
    $header->set_properties(array(
        'name' => 'header',
        'class' => $view->core()->qualify('header'),
        'style' => "background-color: #b1b390;"
   
    ));
    
//    $header_opts = array(
//	    'image' => 'elements/fancy/caption.png',
        //'font-family' => 'Quintessential',
        //);
        //$caption->set_options($caption_opts);
    $view->add_child($header);

    $aside = new idg_fragment;

    $aside->set_properties(array(
        'name' => 'aside',
        'class' => $view->core()->qualify('aside'),
        'source' => '_site',
        'style' => "background-color: #bdd5c4;"
    ));
    $view->add_child($aside);

    $article = new idg_container;
    $article->set_properties(array(
        'name' => 'article',
        'style' => "background-color: #ebd8b9; padding: 0.1em 1em 1em 1em;"
    ));
    $view->add_child($article);

    //$content->set_option('font-family', 'Lekton'); 
    //// As mentioned in style.css ... UGLY HACK
    // Insert the actual page text into the container

    $main_text = new idg_slot;
    $main_text->set_properties(array(
        'name' => 'main-text',
        'style-h1' => "margin: 0.2em 0 0.2em 0",
        'style-a' => "text-decoration: underline; color: inherit; transition:.2s;",
        'style-a-hover' => "background-color: #e1c0bc;",
        'style-list-image' => "$elements/list-image.png"
    ));
    $article->add_child($main_text);

    // Create a footer programmatically

    $footer = new idg_fragment;
    $footer->set_properties(array(
        'name' => 'footer',
        'class' => $view->core()->qualify('footer'),
        'style' => "background-color: #a2acbd;",
        'style-a' => "font-family: inherit;	color: inherit;	text-decoration: none;"
        . "border-bottom: 1px dotted black;",
        'style-a-hover' => "border-bottom: 1px solid black;"
    ));
    $footer->set_text("This page was last updated on [date]. Copyright (c) 2023 "
        . "<a href=\"mailto:Daniel Schlachta <daniel@schlachta.info>\">"
        . "Daniel Schlachta</a>");
    
    $view->add_child($footer);
    
}

if (!@$_SERVER['HTTP_USER_AGENT']) {
    require '../../../indigo/startup.php';
    $view = new idg_view;
    $view->core()->set_namespace('Indigo\Design\Fancy');
    configure_view($view);
    $view->write_xml('fancy.xml');
}
