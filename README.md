# MOF

MOF significa Mockup Outside Framework (Maqueta Fuera del Marco de Trabajo) y es un pequeño archivo PHP (~3Kb) para que incluyas en tus proyectos en la etapa de fabricación de la maqueta. Incluye algunas funciones básicas que serán de utilidad para:

- Cifrar y comparar contraseñas cifradas de forma más o menos segura (password).
- Escribir y leer estructuras de datos complejas en archivos (store/restore).
- Autorizar y desautorizar usuarios (login/logout).
- Verificar si un usuario está autorizado (logged).
- Proteger las páginas con acceso restringido (protect).
- Leer datos suministrados por el usuario sea por GET o POST (input).
- Contestar pedidos con datos en notación JSON (json).
- Redireccionar (redirect).

MOF contiene una serie de funciones mínimas de jugete útiles especialmente para hacer bosquejos de nuevas funcionalidades sin lidiar con bases de datos, modelos y sentencias SQL y olvidarse de las sesiones. Se puede usar dentro o fuera de un marco de trabajo aunque fuera del marco de trabajo se refiere a que **no debe ser usado en producción**. Consume malas prácticas en favor de prestar una funcionalidad sencilla al programador para la confección de bosquejos de código útiles para maquetar controladores o contestar pedidos de la interfaz frontal.

## Ejemplos

### Iniciar la sesión

```php
<?php
require 'mof.php';

$email = input('email'); // obtener el usuario
$password = input('password'); // obtener la contraseña

restore($users); // leer la estructura de datos $users

if (array_key_exists($email, $users)) {
   if (password($password, $users[$email]['password'])) { // comparar contraseñas cifradas
      login($email); // iniciar la sesión
      json(array('status' => 'authorized')); // contestar el pedido
   } else {
      json(array('status' => 'invalid-password')); // contestar el pedido
   }
} else {
   json(array('status' => 'unknown-email')); // contestar el pedido
}
?>
```

### Cerrar la sesión

```php
<?php
require 'mof.php';

logout(); // iniciar la sesión
?>
```

### Editar datos de un usuario

```php
<?php
require 'mof.php';

protect(); // esta página es privada

$email = input('email'); // obtener el usuario

restore($users); // leer la estructura de datos $users

$users[$email]['name'] = input('name'); // obtener el nombre
$users[$email]['phone'] = input('phone'); // obtener el teléfono
$users[$email]['city'] = input('city'); // obtener la ciudad

store($users); // guardar la estructura de datos $users

json(array('status' => 'ok')); // contestar el pedido
?>
```

### Cambiar la contraseña

```php
<?php
require 'mof.php';

protect(); // esta página es privada

$email = input('email'); // obtener el usuario
$current = input('current'); // obtener la contraseña actual
$password = input('new'); // obtener la contraseña nueva

restore($users); // leer la estructura de datos $users

if (password($current, $users[$email]['password'])) { // comparar contraseñas cifradas
   $users[$email]['password'] = password($password); // cifrar contraseña nueva
   store($users);  // guardar la estructura de datos $users
   json(array('status' => 'ok')); // contestar el pedido
} else {
   json(array('status' => 'invalid-password')); // contestar el pedido
}
?>
```

### CLI para crear un usuario

```php
<?php
require 'mof.php';

restore($users); // leer la estructura de datos $users

$users['jperez'] = array();
$users['jperez']['name'] = 'Juan Perez';
$users['jperez']['password'] = password('1234'); // cifrar contraseña

store($users);// guardar la estructura de datos $users
?>
```

### CLI para listar los usuarios

```php
<?php
require 'mof.php';

restore($users); // leer la estructura de datos $users

print_r($users);
?>
```

#### Salida

```
Array
(
    [jperez] => Array
        (
            [name] => Juan Perez
            [password] => $2y$07$9025d1288eec924ee57fduU.wbLcxioeQBq32BWtNVm2BLIdhVT/6
        )

)
```

## Ejemplo de completo de un micrositio con inicio de sesión

### append.php (CLI)

```php
<?php
require '../mof.php';

restore($users);

$users['test'] = array();
$users['test']['name'] = 'Usuario de muestra';
$users['test']['password'] = password('1234');

store($users);
?>
```

### login.php

```php
<?php
require 'mof.php';

$username = input('username');
$password = input('password');

restore($users);

if (array_key_exists($username, $users)) {
   if (password($password, $users[$username]['password'])) {
      login($username);
      redirect('welcome.php');
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
      <form method="post" action="login.php">
         <input name="username" placeholder="Correo electrónico">
         <br>
         <input name="password" placeholder="Contraseña" type="password">
         <br>
         <button type="reset">Restablecer</button>
         <button type="submit">Enviar</button>
      </form>
   </body>
</html>
```

### logout.php

```php
<?php
require '../mof.php';

logout('login.php');
?>
```

### welcome.php

```php
<?php
require 'mof.php';

protect('forbidden.php');

restore($users);
?>
<!doctype html>
<html lang="es">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="initial-scale=1">
      <title>Bienvenido</title>
   </head>
   <body>
      <h1>Bienvenido <?php echo $users[logged()]['name']; ?></h1>
   </body>
</html>
```

### forbidden.php

```php
<!doctype html>
<html lang="es">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="initial-scale=1">
      <title>No autorizado</title>
   </head>
   <body>
      <h1 style="color:red">No autorizado</h1>
      <a href="login.php">Iniciar sesión</a>
   </body>
</html>
```

## Tareas pendientes (que postergaré eternamente)

- Documentar: MOF está pensada para el usuario desarrollador y el código es autosuficiente, muy corto y extremadamente sencillo. No requiere documentación.
