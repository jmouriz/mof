<?php
require '../mof.php';

mof\protect('login.php');

mof\restore($users);
?>
<!doctype html>
<html lang="es">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="initial-scale=1">
      <title>Bienvenido</title>
   </head>
   <body>
      <h1>Bienvenido <?php echo $users[mof\logged()]['name']; ?></h1>
      <a href="logout.php">Cerrar sesión</a>
   </body>
</html>
