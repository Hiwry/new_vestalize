<?php
$c = file_get_contents(__DIR__ . '/public/build/assets/app-DnXDNtE_.css');
preg_match_all('/.{0,80}94a3b8.{0,40}/', $c, $m);
foreach ($m[0] as $h) echo $h . "\n---\n";
