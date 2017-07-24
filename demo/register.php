<?php
require '../mof.php';

$username = mof\input('username');
$password = mof\input('password');
$exists = false;

mof\restore($users);

if ($username && $password) {
   if (array_key_exists($username, $users)) {
      $exists = true;
   } else {
      $users[$username] = array();
      $users[$username]['password'] = mof\password($password);
      $users[$username]['firstname'] = mof\input('firstname');
      $users[$username]['lastname'] = mof\input('lastname');
      $users[$username]['phone'] = mof\input('phone');
      mof\store($users);
      mof\redirect('login.php?registered=1');
   }
}
?>
<!doctype html>
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="initial-scale=1">
      <title>Registro</title>
   </head>
   <body>
      <h1>Registro</h1>
      <?php if ($username || $password): ?>
      <p style="color:red">Faltan datos</p>
      <?php endif ?>
      <?php if ($exists): ?>
      <p style="color:red">El usuario ya existe, por favor, elige otro nombre</p>
      <?php endif ?>
      <form method="post" action="register.php">
         <input name="username" placeholder="Usuario">
         <br>
         <input name="password" placeholder="Contraseña" type="password">
         <br>
         <input name="firstname" placeholder="Nombre">
         <br>
         <input name="lastname" placeholder="Apellido">
         <br>
         <input name="phone" placeholder="Teléfono">
         <br>
         <button type="reset">Restablecer</button>
         <button type="submit">Enviar</button>
      </form>
   </body>
</html>
