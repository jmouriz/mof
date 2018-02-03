#!/usr/bin/php -f
<?php
require '../mof.php';

$path = __DIR__;

mof\storage("$path/database");

mof\restore($table);

print_r($table);
?>
