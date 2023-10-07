$image = new idg_view_html_item;
$image->set_properties(array(
	'class' => 'image',
	'style' => 'padding-right: 8px;'
));

/*
 * The image will have its border set to 0.
 * The source file name will have 'images/' prepended to it.
 */

$image->set_text('source: image.png; width: 220; height: 90; ' . 
    . 'alt-text: an image');
    
$text = new idg_view_html_item;
$text->set_properties(array(
    'class' => 'text',
    'style' => 'font-size: 130%; font-style: italic;'
));

/* 
 * Since this will be embedded in XML
 * you need to quote HTML tags:
 *
 * <br> becomes &lt;br&gt;
 *
 * Indigo then converts them back. This also works with html entities
 * e.g. &amp;nbsp; - if your'e missing a space after your tags because
 * the conversion has gobbled it up, use this.
 */

$text->set_text('You can use document properties: {description}');

