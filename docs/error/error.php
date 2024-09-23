<?php

function get_error_document()
{
	global $site;

    $page = new idg_document();
	$page->set_parent($site);

    $page->set_properties(array(
        'id' => 'error',
        'name' => 'Document not found',
        'description' => 'Document not found'
    ));

    $text1_src = new idg_datasource;
    $text1_src->set_properties(array(
        'name' => 'text1_src',
        'class' => '\Indigo\Datasource\textfile'
    ));
    $text1_src->set_parameter('filename', 'error/error.html');
    $page->add_child($text1_src);

    $text1_rend = new idg_renderer;
    $text1_rend->set_properties(array(
        'slot' => 'main-text',
        'class' => '\Indigo\Renderer\textfile',
        'source' => 'text1_src'
    ));
    $page->add_child($text1_rend);

    $framedimg1_src = new idg_datasource;
    $framedimg1_src->set_properties(array(
        'name' => 'framedimg',
        'class' => 'idg_datasource_framedimg'
    ));
    $framedimg1_src->set_text("file: machine.png; width: 90; height: 57");

    $page->add_child($framedimg1_src);

    $infobox1_src = new idg_datasource;
    $infobox1_src->set_properties(array(
        'name' => 'blocks::infobox',
        'class' => '\Indigo\Datasource\textfile'
    ));
    $infobox1_src->set_parameter('filename', 'error/infobox.html');
    
    $attr = $page->create_attribute('blocks::infobox');
    $attr->set_parameter('caption', 'What\'s going on?');
    

    $page->add_child($infobox1_src);

    return $page;
}
?>
