<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

$lipsum = <<<ENDLIPSUM
<div id="lipsum">
    <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut et 
tempus leo. Vivamus tellus ligula, consectetur sed condimentum non, 
laoreet et lectus. Curabitur quis magna lobortis, interdum libero sed, 
tincidunt tellus. Proin fringilla lectus sit amet dolor scelerisque 
interdum. In auctor, quam a faucibus malesuada, sem ex aliquet nisi, ac 
efficitur enim libero eu sem. Phasellus in suscipit quam. Quisque 
imperdiet ullamcorper tincidunt. Aliquam consequat magna eu libero 
tempor, sed condimentum ligula sodales. Sed nunc justo, condimentum quis 
libero eu, volutpat ullamcorper mi. Cras ut suscipit felis. Proin 
vehicula, ligula pretium pharetra interdum, massa ex consectetur odio, 
sed euismod odio massa sed nulla. </p> <p> Curabitur dapibus dolor 
tempus ipsum finibus, sit amet condimentum mauris ornare. Suspendisse 
semper blandit est, maximus hendrerit eros laoreet eu. Donec egestas leo 
orci, a rhoncus libero interdum id. Etiam cursus sed purus non aliquam. 
Nam ex lacus, placerat eu sollicitudin et, volutpat vel orci. Ut semper 
diam vitae urna tempus condimentum. Curabitur urna nisi, luctus id 
ullamcorper non, maximus sit amet felis. Mauris in rhoncus urna, at 
porttitor turpis. In hac habitasse platea dictumst. Integer ac massa nec 
justo condimentum vulputate at et justo. In consectetur ac metus posuere 
scelerisque. Curabitur odio lorem, fermentum sit amet dictum at, tempus 
tristique justo. Cras sodales turpis vel erat semper, id tristique 
tortor dignissim. Pellentesque elit turpis, tincidunt in vehicula quis, 
posuere vel lacus. Pellentesque ut purus augue. </p> <p> Nunc quis augue 
enim. Maecenas vitae mi nec urna tincidunt blandit. Cras pellentesque 
est vitae ex varius, sit amet commodo odio posuere. Sed scelerisque leo 
lorem, et congue lectus facilisis eget. Cras hendrerit ante vitae 
elementum pellentesque. Nunc fringilla odio tortor, sollicitudin posuere 
ligula interdum nec. Aliquam scelerisque aliquam mattis. Morbi cursus, 
turpis nec porttitor posuere, enim erat tristique ligula, consectetur 
interdum quam tortor in urna. Mauris sit amet ipsum ac erat suscipit 
venenatis. Fusce ac ligula et dui tristique auctor quis in massa. Sed 
non mi nec leo egestas elementum eu eget lacus. Integer aliquet 
convallis enim vitae vehicula. In condimentum nec nulla sit amet 
viverra. Aenean egestas tincidunt mi, ornare pharetra tortor convallis 
vitae. </p> <p> Vestibulum tristique, tortor eu fermentum pellentesque, 
nibh massa vestibulum nibh, iaculis varius mauris neque vel tellus. 
Praesent accumsan mi sem, eu blandit erat sagittis ultrices. Donec odio 
sem, tempus at facilisis ac, viverra ut sapien. Aliquam tincidunt 
fermentum ex, ac pulvinar sem bibendum in. Interdum et malesuada fames 
ac ante ipsum primis in faucibus. Mauris viverra laoreet tincidunt. 
Nullam varius dolor est, sed scelerisque mauris aliquam vulputate. 
Curabitur quis tortor sapien. Etiam ultricies venenatis ligula, eu 
semper sapien tincidunt sit amet. Sed a massa eu mi blandit rhoncus. 
Praesent sed luctus justo. Mauris venenatis velit ac ullamcorper 
sodales. Praesent faucibus auctor justo sed interdum. Sed diam purus, 
ullamcorper at nisi eget, egestas volutpat magna. </p> <p> Fusce 
scelerisque, sapien non ultrices consequat, tellus ante viverra nunc, 
non sagittis lorem enim et mi. Donec blandit tristique sollicitudin. 
Curabitur eu urna eget dolor tempor accumsan. Fusce nec cursus turpis. 
Morbi volutpat, augue quis dictum condimentum, mauris mi ullamcorper 
purus, sed laoreet odio massa eu nisl. Donec aliquet massa non lectus 
tempor, ac cursus mauris pellentesque. Ut ullamcorper turpis est, et 
blandit lacus gravida ut. Vestibulum ante ipsum primis in faucibus orci 
luctus et ultrices posuere cubilia curae; Pellentesque habitant morbi 
tristique senectus et netus et malesuada fames ac turpis egestas. 
Suspendisse ac arcu vel augue aliquam tempus. Integer dui tellus, 
blandit a volutpat vulputate, condimentum ac quam. Vestibulum ante ipsum 
primis in faucibus orci luctus et ultrices posuere cubilia curae; In 
tincidunt, massa in lobortis varius, elit metus interdum lacus, nec 
mollis quam augue a lorem. Donec quis volutpat nunc, et consectetur 
neque. Sed ullamcorper posuere arcu non maximus. Etiam blandit, felis et 
interdum laoreet, nibh enim euismod sem, eu eleifend elit magna eget 
metus. </p>
</div>

ENDLIPSUM;

$form = <<<ENDSTART
    <div style="font-size: 200%; font-family: sans-serif; padding-right: 0.5em; float: left;">
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

    if (in_array($name, array('.', '..')) || strpos($name, '.php') > 0)
        continue;

    $select = $name == $design ? ' selected="yes"' : '';

    $form .= "<option value=\"$name\"$select>$name</option>\n";
}

$form .= <<<ENDEND
		</select>
	</form>
    
ENDEND;

require_once '../indigo/startup.php';

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

require_once "$design.php";

$view = new idg_view;
$view->set_properties(['class' => $design]);

$text = new idg_fragment;
$text->set_properties(array(
    'class' => 'text',
    'name' => 'content' // fancy needs this!
));
$text->set_text($form . $lipsum);

$func = "idg_view_${design}_make_testcard";
$func($document, $view, $text);

$view->check();
$view->render($document);
$view->print();
?>
