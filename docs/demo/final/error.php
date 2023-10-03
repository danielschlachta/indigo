<?php

$page = new idg_document;

$page->parent =& $site;

$page->set_properties(array(
    'id' => 'error',
    'name' => 'Page not found',
    'content-language' => 'en'
));
   
$text1_src = new idg_datasource_declaration;
$text1_src->set_properties(array(
    'name' => 'text1_src',
    'class' => 'idg_datasource_phpscript'
));

$text1_src->set_text('script: display_error.php;'  // this is where
    . ' class: idg_view_html_renderer_errormsg;'); // the magic happens

$page->add_child($text1_src);

$text1_rend = new idg_renderer_declaration;
$text1_rend->set_properties(array(
    'slot' => 'main-text',
    'class' => 'idg_view_html_renderer_phpscript',
    'source' => 'text1_src'
));

$page->add_child($text1_rend);

?>
