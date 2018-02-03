#!/usr/bin/php -f
<?php
require '../mof.php';

$path = __DIR__;

mof\storage("$path/database");
mof\logs("$path/log");

mof\restore($table);

mof\log($table);
?>
