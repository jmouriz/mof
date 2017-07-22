#!/usr/bin/php -f
<?php
require '../mof.php';

mof\restore($users);

$users['test'] = array();
$users['test']['name'] = 'Usuario de muestra';
$users['test']['password'] = mof\password('1234');

mof\store($users);
?>
