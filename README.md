# MOF

MOF significa Mockup Outside Framework (Maqueta Fuera del Marco de Trabajo) y es un pequeño archivo PHP (~7,2Kb) para que incluyas en tus proyectos en la etapa de fabricación de la maqueta. Incluye algunas funciones básicas que serán de utilidad para:

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
- Decirle al navegador que no use el caché (volatile).

MOF contiene una serie de funciones mínimas de jugete útiles especialmente para hacer bosquejos de nuevas funcionalidades sin lidiar con bases de datos, modelos y sentencias SQL y olvidarse de las sesiones. Se puede usar dentro o fuera de un marco de trabajo aunque fuera del marco de trabajo se refiere a que **no debe ser usado en producción**. Consume malas prácticas en favor de prestar una funcionalidad sencilla al programador para la confección de bosquejos de código útiles para maquetar controladores o contestar pedidos de la interfaz frontal.

## Funciones

### password($password)

Dada la contraseña plana `$password` la devuelve cifrada.

### password($password, $hash)

Dada la contraseña `$password` y su versión cifrada `$hash` (almacenada), las compara y devuelve `true` si son iguales o `false` en caso contrario.

### libraries()

Establece la ruta donde se encuentra `mof.php` como el lugar donde buscar las librerias. Intenta ser una ayuda a la característica autoload de PHP.

### libraries($path)

Igual que `libraries()` excepto que establece la ruta de búsqueda a `$path`.

### filename($backtrace)

Función de uso interno usada por `store` y `restore` para determinar el nombre de archivo a partir del nombre de una variable, intenta no utilizarla.

### store($variable)

Guarda la estructura definida en `$variable` en un archivo con su mismo nombre y lo comprime. Por ejemplo, `store($users)` guarda `$users` en el archivo `database/users.dbz`

### restore($variable)

Restaura la estructura de `$variable` a partir del archivo con ese nombre o devuelve un arreglo vacío si el archivo no existe. Por ejemplo, `restore($users)` lee el archivo `database/users.dbz` y lo guarda en la variable `$users`.

### read($filename)

Lee y devuelve el contenido del archivo `$filename` si existe, sino devuelve `false`.

### read($filename, $fallback)

Igual que `read($filename)` excepto que si el archivo no existe devuelve `$fallback`.

### upload()

Pone un archivo recién subido en la carpeta `upload` dentro del directorio actual según los datos suministrados en `$_FILES` con el modo 0640.

### upload($path)

Igual que `upload()` excepto que coloca el archivo en `$path/upload`.

### upload($path, $filename)

Igual que `upload()` excepto que coloca el archivo en `$path/upload` con el nombre `$filename`.

### upload($path, $filename, $mode)

Igual que `upload()` excepto que coloca el archivo en `$path/upload` con el nombre `$filename` y el modo `$mode`.

### volatile()

Escribe los encabezados necesarios para solicitarle al navegador que no use el caché.

### input($variable)

Obtiene `$variable` donde esté definida, sea GET, POST o php://input. Si no está en ninguna de las tres, devuelve `false`.

### input($variable, $default)

Igual que `input($variable)` excepto que si no está definida ni en GET, ni en POST, ni en php://input, devuelve `$default`.

### session()

Devuelve el identificador de la sesión existente y si no existe crea una nueva y devuelve el identificador.

### protect()

Verifica si el usuario tiene iniciada la sesión. De no ser así, termina el flujo inmediatamente.

### protect($location)

Igual que `protect()` excepto que si no hay una sesión iniciada redirige a `$location`.

### logged()

Devuelve el usuario que inició la sesión o `false` si no inició ningún usuario.

### login($username)

Inicia una sesión para el usuario $username.

### login($username, true)

Inicia una sesión para el usuario $username la guarda en una cookie para futuras ocasiones. Útil para mantener una sesión iniciada de manera persistente.

### logout()

Cierra la sesión.

### logout($location)

Igual que `logout()` excepto que también redirige a `$location`.

### response($data)

Escribe la respuesta HTML `$data` con los encabezados correspondientes y sale inmediatamente.

### response($data, $type)

Igual que `response($data)` excepto que escribe el encabezado para el tipo `$type`, por ejemplo text/xml.

### json($data)

Escribe `$data` en notación JSON con los encabezados correspondientes y sale inmediatamente.

### json($data, true)

Igual que `json($data)` excepto que formatea la salida.

### css($css)

Escribe los estilos CSS pasados en `$css` con los encabezados correspondientes.

### html($html)

Escribe el contenido HTML pasado en `$html` con los encabezados correspondientes. Igual que `response($data)`.

### redirect($location)

Redirige a `$location`.

### debug($data)

Escribe `$data` con la forma adecuada para mostrar en el navegador.

### debug($data, true)

Igual que `debug($data)` excepto que también termina el flujo inmediatamente.

### log($message)

Escribe `$message` en el archivo `logs/mof.log`. Si `$message` es una variable, escribe su contenido formateado.

## Ejemplos

### Iniciar la sesión

```php
<?php
require 'mof.php';

$email = mof\input('email'); // obtener el usuario
$password = mof\input('password'); // obtener la contraseña

mof\restore($users); // leer la estructura de datos $users

if (array_key_exists($email, $users)) {
   if (mof\password($password, $users[$email]['password'])) { // comparar contraseñas cifradas
      mof\login($email); // iniciar la sesión
      mof\json(array('status' => 'authorized')); // contestar el pedido
   } else {
      mof\json(array('status' => 'invalid-password')); // contestar el pedido
   }
} else {
   mof\json(array('status' => 'unknown-email')); // contestar el pedido
}
?>
```

### Cerrar la sesión

```php
<?php
require 'mof.php';

mof\logout('goodbye.php'); // cerrar la sesión
?>
```

### Registrar un usuario

```php
<?php
require 'mof.php';

$email = mof\input('email'); // obtener el usuario
$password = mof\input('password'); // obtener la contraseña

mof\restore($users); // leer la estructura de datos $users

if ($email && $password) {
   if (array_key_exists($email, $users)) {
      mof\json(array('status' => 'already-exists')); // contestar el pedido
   } else {
      $users[$email] = array(); // crear el usuario
      $users[$email]['password'] = mof\password($password); // cifrar contraseña nueva
      $users[$email]['firstname'] = mof\input('firstname'); // obtener el nombre
      $users[$email]['lastname'] = mof\input('lastname'); // obtener el apellido
      $users[$email]['phone'] = mof\input('phone'); // obtener el teléfono
      mof\store($users); // guardar la estructura de datos $users
      mof\json(array('status' => 'ok')); // contestar el pedido
   }
}
?>
```

### Editar datos de un usuario

```php
<?php
require 'mof.php';

mof\protect(); // esta página es privada

$email = mof\input('email'); // obtener el usuario

mof\restore($users); // leer la estructura de datos $users

$users[$email]['name'] = mof\input('name'); // obtener el nombre
$users[$email]['phone'] = mof\input('phone'); // obtener el teléfono
$users[$email]['city'] = mof\input('city'); // obtener la ciudad

mof\store($users); // guardar la estructura de datos $users

mof\json(array('status' => 'ok')); // contestar el pedido
?>
```

### Cambiar la contraseña

```php
<?php
require 'mof.php';

mof\protect(); // esta página es privada

$email = mof\input('email'); // obtener el usuario
$current = mof\input('current'); // obtener la contraseña actual
$password = mof\input('new'); // obtener la contraseña nueva

mof\restore($users); // leer la estructura de datos $users

if (mof\password($current, $users[$email]['password'])) { // comparar contraseñas cifradas
   $users[$email]['password'] = mof\password($password); // cifrar contraseña nueva
   mof\store($users);  // guardar la estructura de datos $users
   mof\json(array('status' => 'ok')); // contestar el pedido
} else {
   mof\json(array('status' => 'invalid-password')); // contestar el pedido
}
?>
```

### CLI para listar los usuarios

```php
<?php
require 'mof.php';

mof\restore($users); // leer la estructura de datos $users

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
> de escritura para el grupo `www-data` (esto puede variar dependiendo del servidor web). Recomiendo que utilices el comando `setfacl -m g:www-data:rwX -R mof`
> para otorgar dichos privilegios antes de comenzar.

### register.php

```php
<?php
require 'mof.php';

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
```

### login.php

```php
<?php
require 'mof.php';

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
   </body>
</html>
```
