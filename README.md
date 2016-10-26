# mof
Mockup Outside Framework

MOF es un simple archivo PHP para que incluyas en tus proyectos en la etapa de fabricación de la maqueta. Incluye algunas funciones básicas que serán de utilidad para:

- Encriptar y comparar contraseñas encriptadas de forma más o menos segura (password).
- Escribir y leer estructuras de datos complejas en archivos (store/restore).
- Autorizar y desautorizar usuarios (login/logout).
- Verificar si un usuario está autorizado (logged).
- Proteger las páginas con acceso restringido (protect).
- Leer datos suministrados por el pedido del usuario sea por GET o POST (input).
- Contestar pedidos con estructuras de datos a través de JSON (json).

MOF es útil especialmente para hacer bosquejos de nuevas funcionalidades sin lidiar con bases de datos, modelos, sentencias SQL. Se puede usar dentro o fuera de un marco de trabajo aunque fuera del marco de trabajo se refiere a que no debe ser usada en producción y es útil para escribir pequeños controladores. Consume malas prácticas en favor de prestar una funcionalidad sencilla para el maquetado rápido.

## Ejemplos

### Inicio de sesión

```php
require 'mof.php';

$email = input('email');
$password = input('password');

restore($users); // leer la estructura de datos $users

if (array_key_exists($email, $users)) {
   if (password($password, $users[$email]['password'])) {
      json(array('status' => 'authorized'));
   } else {
      json(array('status' => 'invalid-password'));
   }
} else {
   json(array('status' => 'unknown-email'));
}
```

### Editar datos de un usuario

```php
require 'mof.php';

protect(); // esta página es privada

$email = input('email');

restore($users); // leer la estructura de datos $users

$users[$email]['name'] = input('name');
$users[$email]['phone'] = input('phone');
$users[$email]['city'] = input('city');

store($users); // guardar la estructura de datos $users

json(array('status' => 'ok'));
```
