<?php
require '../mof.php';

$username = input('username');
$password = input('password');

restore($users);

if (array_key_exists($username, $users)) {
   if (password($password, $users[$username]['password'])) {
      login($username);
      redirect('index.php');
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
      <p>Pruebe <b>test/1234</b> para iniciar sesión</p>
      <?php if ($username): ?>
      <p style="color:red">No autorizado</p>
      <?php endif ?>
      <form method="post" action="login.php">
         <input name="username" placeholder="Usuario">
         <br>
         <input name="password" placeholder="Contraseña" type="password">
         <br>
         <button type="reset">Restablecer</button>
         <button type="submit">Enviar</button>
      </form>
   </body>
</html>
