#!/usr/bin/php -f
<?php
require '../mof.php';

$path = __DIR__;

mof\storage("$path/database");

$table = array();

$i = 0;
while (++$i <= 1000) {
   $table[] = mof\id(10);
}

mof\store($table);
?>
