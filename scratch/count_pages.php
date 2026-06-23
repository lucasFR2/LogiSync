<?php
$c = file_get_contents(__DIR__ . '/invoice_test.pdf');
echo 'Pages: ' . preg_match_all('/\/Type\s*\/Page\b/', $c, $m) . PHP_EOL;
