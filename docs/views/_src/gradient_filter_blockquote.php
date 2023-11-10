<?php

$elements = 'views/gradient/elements';

$output = str_replace('<blockquote>',
    "<div style=\"border: solid black; border-width: 1px;\">"
    . "<blockquote style=\"margin-left: 1em; margin-right: 1em;\">"
    . " <img src=\"$elements/hand.png\" alt=\"\" style=\"float: left; "
    . "padding-right: 0.5em; margin-top: 2px;\">", $text);

$output = str_replace('</blockquote>', '</blockquote></div>', $output);

return $output;
