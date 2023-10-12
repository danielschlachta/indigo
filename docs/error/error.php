<?php

function get_error_document()
{
	global $site;

    $page = new idg_document();
	$page->set_parent($site);

    $page->set_properties(array(
        'id' => 'error',
        'name' => 'Document not found',
        'description' => 'Document not found',
        'content-language' => 'en'
    ));

    $text1_src = new idg_datasource_declaration;
    $text1_src->set_properties(array(
        'name' => 'text1_src',
        'class' => 'idg_datasource_textfile'
    ));
    $text1_src->set_text('filename: error/error.html');
    $page->add_child($text1_src);

    $text1_rend = new idg_renderer_declaration;
    $text1_rend->set_properties(array(
        'slot' => 'main-text',
        'class' => 'idg_view_html_renderer_textfile',
        'source' => 'text1_src'
    ));
    $page->add_child($text1_rend);

    $framedimg1_src = new idg_datasource_declaration;
    $framedimg1_src->set_properties(array(
        'name' => 'framedimg',
        'class' => 'idg_datasource_framedimg'
    ));
    $framedimg1_src->set_text("file: machine.png; width: 90; height: 57");

    $page->add_child($framedimg1_src);

    $infobox1_src = new idg_datasource_declaration;
    $infobox1_src->set_properties(array(
        'name' => 'infobox',
        'class' => 'idg_datasource_infobox'
    ));
    $infobox1_src->set_text("caption: What's going on?; file: infobox.html");

    $page->add_child($infobox1_src);

    return $page;
}
?>
