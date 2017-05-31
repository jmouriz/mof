#!/usr/bin/php -f
<?php
require '../mof.php';

restore($users);

$users['test'] = array();
$users['test']['name'] = 'Usuario de muestra';
$users['test']['password'] = password('1234');

store($users);
?>
