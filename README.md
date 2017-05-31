# MOF

MOF significa Mockup Outside Framework (Maqueta Fuera del Marco de Trabajo) y es un pequeño archivo PHP (~3,7Kb) para que incluyas en tus proyectos en la etapa de fabricación de la maqueta. Incluye algunas funciones básicas que serán de utilidad para:

- Cifrar y comparar contraseñas cifradas de forma más o menos segura (password).
- Escribir y leer estructuras de datos complejas en archivos (store/restore).
- Iniciar y cerrar sesión (login/logout).
- Verificar si un usuario está autorizado (logged).
- Proteger las páginas con acceso restringido (protect).
- Leer datos suministrados por el usuario sea por GET, POST o php://input (input).
- Contestar pedidos con contenido HTML (html).
- Contestar pedidos con estilos CSS (css).
- Contestar pedidos con datos en notación JSON (json).
- Contestar a los clientes cualquier tipo de contenido, por ejemplo XML (response).
- Leer archivos especificando un contenido de contingencia (read).
- Redireccionar (redirect).
- Subir archivos al servidor (upload).

MOF contiene una serie de funciones mínimas de jugete útiles especialmente para hacer bosquejos de nuevas funcionalidades sin lidiar con bases de datos, modelos y sentencias SQL y olvidarse de las sesiones. Se puede usar dentro o fuera de un marco de trabajo aunque fuera del marco de trabajo se refiere a que **no debe ser usado en producción**. Consume malas prácticas en favor de prestar una funcionalidad sencilla al programador para la confección de bosquejos de código útiles para maquetar controladores o contestar pedidos de la interfaz frontal.

## Funciones

### password($password)

Dada la contraseña plana `$password` la devuelve cifrada.

### password($password, $hash)

Dadas las contraseñas cifradas `$password` y `$hash`, las compara y devuelve `true` si son iguales o `false` en caso contrario.

### libraries()

Establece la ruta donde se encuentra `mof.php` como el lugar donde buscar las librerias.

### libraries($path)

Igual que `libraries()` excepto que establece la ruta de búsqueda a `$path`.

### filename($backtrace)

Función de uso interno usada por `store` y `restore` para determinar el nombre de archivo a partir del nombre de una variable, intente no utilizarla.

### store($variable)

Guarda la estructura definida en `$variable` en un archivo con su mismo nombre y lo comprime. Por ejemplo, `store($users)` guarda `$users` en el archivo `database/users.dbz`

### restore($variable)

Restaura la estructura de `$variable` a partir del archivo con ese nombre o devuelve un arreglo vacío si el archivo no existe. Por ejemplo, `restore($users)` lee el archivo `database/users.dbz` y lo guarda en la variable `$users`.

### read($filename)

Lee y devuelve el contenido del archivo `$filename` si existe, sino devuelve `false`.

### read($filename, $fallback)

Igual que `read($filename` excepto que si el archivo no existe devuelve el valor de contingencia `$fallback`.

### upload()

Pone un archivo recién subido en la carpeta `upload` dentro del directorio actual según los datos suministrados en `$_FILES` con el modo 0640.

### upload($path)

Pone un archivo recién subido en la carpeta `$path/upload` según los datos suministrados en `$_FILES` con el modo 0640.

### upload($path, $filename)

Pone un archivo recién subido en la carpeta `$path/upload` con el nombre `$filename` con el modo 0640.

### upload($path, $filename, $mode)

Pone un archivo recién subido en la carpeta `$path/upload` con el nombre `$filename` y el modo `$mode`.

### input($variable)

Obtiene `$variable` donde esté definida, sea GET o POST. Si no está en ninguno de los dos, devuelve `false`.

### input($variable, $default)

Igual que `input($variable)` excepto que si no está ni en GET, ni en POST, devuelve `$default`.

### session()

Devuelve el identificador de la sesión existente y si no existe crea una nueva y devuelve el identificador.

### protect()

Verifica si el usuario tiene iniciada la sesión. De no ser así, termina el flujo inmediatamente.

### protect($location)

Igual que `protect()` excepto que si no hay una sesión iniciada redirige a `$location`.

### logged()

Devuelve el usuario que inició la sesión o `false` si no inició ningún usuario.

### login($email)

Inicia una sesión para el usuario $email.

### logout()

Cierra la sesión.

### logout($location)

Igual que `logout()` excepto que también redirige a `$location`.

### response($data)

Escribe la respuesta HTML `$data` con los encabezados correspondientes y sale inmediatamente.

### response($data, $type)

Igual que `response($data)` excepto que escribe el encabezado para otros tipos diferentes a `text/html`.

### json($data)

Escribe `$data` en notación JSON con los encabezados correspondientes y sale inmediatamente.

### json($data, true)

Igual que `json($data)` excepto que formatea la salida.

### css($css)

Escribe los estilos CSS pasados en `$css` con los encabezados correspondientes.

### html($html)

Escribe el contenido HTML pasado en `$html` con los encabezados correspondientes.

### redirect($location)

Redirige a `$location`.

### debug($data)

Escribe `$data` con la forma adecuada para mostrar en el navegador.

### debug($data, true)

Igual que `debug($data)` excepto que también termina el flujo inmediatamente.

### _log($message)

Escribe `$mensaje` en el archivo `logs/mof.log`.

### _log($variable, true)

Igual que `_log($message)` excepto que en lugar de un mensaje formatea y escribe `$variable`.

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

logout('goodbye.php'); // cerrar la sesión
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
## Ejemplo de completo de un micrositio protegido con inicio de sesión

> **RECUERDA** El código que aquí se expone está en el directorio `demo` y para que funcione, los directorios `log` y `database` deben tener permisos
> de escritura para el grupo `www-data` (esto puede variar dependiendo del servidor web). Recomiendo que utilices el comando `setfacl -R mof g:www-data:rwX`
> para otorgar dichos privilegios antes de comenzar. Ejecuta primero el script `append.php` en la consola para crear el usuario de prueba `test`.

### append.php (CLI)

```php
#!/usr/bin/php -f
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
$remember = input('remember');

restore($users);

if (array_key_exists($username, $users)) {
   if (password($password, $users[$username]['password'])) {
      login($username, $remember);
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
      <?php if ($username): ?>
      <p style="color:red">No autorizado</p>
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
```

### logout.php

```php
<?php
require 'mof.php';

logout('login.php');
?>
```

### index.php

```php
<?php
require 'mof.php';

protect('login.php');

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
