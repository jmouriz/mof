<?php
require '../mof.php';

$username = mof\input('username');
$password = mof\input('password');
$remember = mof\input('remember');
$registered = mof\input('registered');

mof\restore($users);

if (array_key_exists($username, $users)) {
   if (mof\password($password, $users[$username]['password'])) {
      mof\login($username, $remember);
      mof\redirect('index.php');
   }
}
?>
<!doctype html>
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="initial-scale=1">
      <title>Ingresar</title>
   </head>
   <body>
      <h1>Ingresar</h1>
      <?php if ($username): ?>
      <p style="color:red">No autorizado</p>
      <?php endif ?>
      <?php if ($registered): ?>
      <p>Creaste una cuenta, ya puedes utilizarla para ingresar</p>
      <?php else: ?>
      <p>¿Aún no tienes cuenta? <a href="register.php">Crea una nueva</a></p>
      <?php endif ?>
      <form method="post" action="login.php">
         <input name="username" placeholder="Usuario">
         <br>
         <input name="password" placeholder="Contraseña" type="password">
         <br>
         <input name="remember" type="checkbox"> Recordar sesión
         <br>
         <button type="reset">Restablecer</button>
         <button type="submit">Enviar</button>
      </form>
   </body>
</html>
