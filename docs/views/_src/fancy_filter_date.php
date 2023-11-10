<?php

$last_change = new DateTimeImmutable('2023-10-09 23:23:23');
$date = $last_change->format('m/d/Y');

return str_replace('[date]', $date, $text);
