<?php

$output = str_replace(' - ', '&mdash;', $text);
$output = str_replace('&ldquo;', '&laquo;', $output);
$output = str_replace('&rdquo;', '&raquo;', $output);
$output = str_replace('...', '&hellip;', $output);
$output = str_replace('\'', '&rsquo;', $output);

$output = str_replace('<blockquote>',
    '<blockquote><img src="views/fancy/elements/blockquote.png" alt="">', $output);

return $output;
