#!/usr/bin/php -f
<?php
require '../mof.php';

$path = __DIR__;

mof\storage("$path/database");

mof\restore($table);

$table = array();

$i = 0;
while (++$i <= 1000) {
   $table[] = mof\id(10);
}

mof\store($table);
?>
