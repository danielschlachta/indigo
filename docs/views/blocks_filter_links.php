<?php

$elements = 'views/blocks/elements';

$image = "<img src=\"$elements/link.png\" width=\"11\" height=\"10\" alt=\"\">";
$ext_image = "<img src=\"$elements/link_ext.png\" width=\"11\" height=\"10\" alt=\"\">";

$output = $text;

$output = preg_replace('/(<a[^hH]+href=["\'][^h][^t][^t][^p][^>]*)>/', "\\1>$image",
    $output);
$output = preg_replace('/(<a[^hH]+href=["\']#[^>]*)>/', "\\1>$image",
    $output);
$output = preg_replace('/(<a[^hH]+href=["\']http[^>]*)>/', "\\1>$ext_image",
    $output);

return $output;