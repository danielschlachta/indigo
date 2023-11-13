<?php

$output = str_replace(' - ', '&mdash;', $text);
$output = str_replace('&ldquo;', '&laquo;', $output);
$output = str_replace('&rdquo;', '&raquo;', $output);
$output = str_replace('...', '&hellip;', $output);
$output = str_replace('\'', '&rsquo;', $output);

$output = str_replace('<blockquote>',
    '<blockquote><img src="views/fancy/elements/blockquote.png" style="height: 65px;'
    . ' padding-right: 10px; margin: 10px 5px 10px -30px; float: left;" alt="">', $output);

return $output;
