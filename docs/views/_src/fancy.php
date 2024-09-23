<?php

function configure_view(idg_view $view): void {
    $elements = "views/fancy/elements";

    $view->set_properties([
        'class' => $view->template()->qualify('view'),
        'icon' => 'favicon.png',
        'style' => "background-color: #fff6e8;" 
        . " font-family: @google('Liberation Serif'); font-size: 120%;",
        'style-h1' => "font-size: 180%;",
        'style-h2' => "font-size: 140%;"
    ]);

    $filter_typo = new idg_filter;
    $filter_typo->set_properties([
        'name' => 'filter_typo'
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
        'class' => $view->template()->qualify('nav'),
        'source' => '_site',
        'style' => "background-color: #ffe7d6; border-radius: 5px;"
    ]);
    $view->add_child($nav);

    $header = new idg_fragment;
    $header->set_properties([
        'name' => 'header',
        'class' => $view->template()->qualify('header'),
        'style' => "background-color: #b1b390; border-radius: 5px;"  
        . " font-family: @google('Quintessential');",
        'style-h1' => 'font-style: italic;'
    ]);
    $view->add_child($header);

    $aside = new idg_fragment;

    $aside->set_properties([
        'name' => 'aside',
        'class' => $view->template()->qualify('aside'),
        'source' => '_site',
        'style' => "background-color: #bdd5c4; border-radius: 5px;"
    ]);
    $view->add_child($aside);

    $article = new idg_container;
    $article->set_properties([
        'name' => 'article',
        'style' => "background-color: #ebd8b9; border-radius: 5px;" 
        . " padding: 0.2em 1em 1em 1em;",
        'style-h1' => "margin: 0.2em 0 0.2em 0",
        'style-a' => "text-decoration: underline; color: inherit; transition:.2s;",
        'style-a-hover' => "background-color: #e1c0bc;",
        'style-list-image' => "$elements/list-image.png"
    ]);
    $view->add_child($article);

    $filter_typo = new idg_filter;
    $filter_typo->set_properties([
        'name' => 'filter_typo'
    ]);
    $article->add_child($filter_typo);

    $main_text = new idg_slot;
    $main_text->set_properties([
        'name' => 'main-text',
    ]);
    $article->add_child($main_text);

    $footer = new idg_fragment;
    $footer->set_properties([
        'name' => 'footer',
        'class' => $view->template()->qualify('footer'),
        'style' => "background-color: #a2acbd; border-radius: 5px;" 
        . " font-family: @google('Special Elite'); font-size: 83%;",
        'style-a' => "font-family: inherit;	color: inherit;	text-decoration: none; "
        . "border-bottom: 1px dotted black;",
        'style-a-hover' => "border-bottom: 1px solid black;"
    ]);
    $footer->set_text("This page was last updated on [date]. Copyright (c) 2023 "
        . "<a href=\"mailto:Daniel Schlachta <daniel@schlachta.info>\">"
        . "Daniel Schlachta</a>");
    $view->add_child($footer);
}

if (!@$_SERVER['HTTP_USER_AGENT']) {
    require '../../../indigo/startup.php';
    $view = new idg_view;
    $view->template()->set_namespace('Indigo\Design\Fancy');
    configure_view($view);
    $view->write_xml('fancy.xml');
}
