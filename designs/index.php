<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once '../indigo/startup.php';

$lipsum = <<<ENDLIPSUM

<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut et 
tempus leo. Vivamus tellus ligula, consectetur sed condimentum non, 
laoreet et lectus. Curabitur quis magna lobortis, interdum libero sed, 
tincidunt tellus. Proin fringilla lectus sit amet dolor scelerisque 
interdum. In auctor, quam a faucibus malesuada, sem ex aliquet nisi, ac 
efficitur enim libero eu sem. Phasellus in suscipit quam. Quisque 
imperdiet ullamcorper tincidunt. Aliquam consequat magna eu libero 
tempor, sed condimentum ligula sodales. Sed nunc justo, condimentum quis 
libero eu, volutpat ullamcorper mi. Cras ut suscipit felis. Proin 
vehicula, ligula pretium pharetra interdum, massa ex consectetur odio, 
sed euismod odio massa sed nulla.</p> 
    
ENDLIPSUM;

$form = <<<ENDSTART
    
    <div style="font-size: 200%; font-family: sans-serif;
        padding-right: 0.5em; float: left;">
        Choose a design:</div>
	<form method="get" action="" id="design">
		
		<select name="design" onchange="this.form.submit();" 
            style="font-size: 200%; display:  background: #e0e0e0;">

ENDSTART;

$design = @$_GET['design'];
$design = $design ? $design : 'blocks';

$directory = scandir('.');

for ($i = 0; $i < count($directory); $i++) {

    $name = $directory[$i];

    if (in_array($name, array('.', '..')) || !is_dir($name))
        continue;

    $select = $name == $design ? ' selected="yes"' : '';

    $form .= "              <option value=\"$name\"$select>$name</option>\n";
}

$form .= <<<ENDEND
		</select>
	</form>
        
ENDEND;

$text = new idg_fragment;
$text->set_properties(array(
    'class' => '\Indigo\Fragment\text',
    'name' => 'content' // fancy needs this!
));
$text->set_text($form . $lipsum . $lipsum . $lipsum . $lipsum);

// -----------------------------------------------------------------------

$site = new idg_site;

$document = new idg_document;
$document->set_properties([
    'id' => 'doc-1',
    'name' => "Document 1",
    'title' => "indigo test card for design '$design'"
]);

$folder1 = new idg_folder;
$folder1->set_properties(['id' => 'folder-1', 'name' => 'Folder 1']);
$folder1->add_child($document);

$pg = 2;

for ($i = 1; $i < 3; $i++) {
    $cur_pg = $pg++;
    $name = "Document $cur_pg";
    $$name = new idg_document;
    $$name->set_properties([
        'id' => "doc-$cur_pg",
        'name' => $name,
        'title' => "Document $cur_pg"
    ]);

    $folder1->add_child($$name);
}

$site->add_child($folder1);

$folder2 = new idg_folder;
$folder2->set_properties([ 'id' => 'folder-2',   'name' => 'Folder 2']);

for ($i = 1; $i < 5; $i++) {
    $cur_pg = $pg++;
    $name = "Document $cur_pg";
    $id = "doc-$cur_pg";
    $$id = new idg_document;
    $$id->set_properties([ 
        'id' => $id,
        'name' => $name, 
        'title' => "Document $cur_pg"
    ]);

    $folder2->add_child($$id);
}

$site->add_child($folder2);

/* Uncomment this to check whether navs filter correctly  
 
$folder3 = new idg_folder;
$folder3->set_properties([ 'id' => 'folder-2',   'name' => 'Folder 2']);

for ($i = 1; $i < 5; $i++) {
    $cur_pg = $pg++;
    $name = "Document $cur_pg";
    $id = "doc-$cur_pg";
    $$id = new idg_document;
    $$id->set_properties([ 
        'id' => $id,
        'name' => $name, 
        'title' => "Document $cur_pg"
    ]);

    $folder3->add_child($$id);
}

$site->add_child($folder3);
*/

$doc = 'doc-5';

for ($i = 1; $i < 6; $i++) {
    $name = "Anchor $i";
    $$name = new idg_renderer;
    $$name->set_properties([
        'name' => $name, 
        'class' => 'text',
        'source' => 'null',
        'slot' => 'null',
        'anchor' => "anchor-$i"
    ]);
    $$doc->add_child($$name);
}

$site->check();

$view = new idg_view(new idg_core($design));
    
$func = $view->core()->qualify('testcard');
$func($document, $view, $text);

$view->check();
$view->render($document);
$view->print();

