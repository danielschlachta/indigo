<?php

$elements = 'views/blocks/elements';

$output = str_replace('<blockquote>',
    "<div style=\"background: url($elements/bg_div.png); "
    . "width: 100%; height: 100%; border: solid black; border-width: 1px;\">"
    . "<blockquote style=\"margin-left: 1em;\">", $text);

$output = str_replace('</blockquote>', "</blockquote>"
    . "<img src=\"$elements/lightbulb.png\" alt=\"\" style=\"float: right; "
    . "position: relative; bottom: 40px; right: 2px;\"></div>", $output);

return $output;
