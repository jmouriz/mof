# MOF

MOF significa Mockup Outside Framework (Maqueta Fuera del Marco de Trabajo) y es un pequeño archivo PHP (~3Kb) para que incluyas en tus proyectos en la etapa de fabricación de la maqueta. Incluye algunas funciones básicas que serán de utilidad para:

- Encriptar y comparar contraseñas encriptadas de forma más o menos segura (password).
- Escribir y leer estructuras de datos complejas en archivos (store/restore).
- Autorizar y desautorizar usuarios (login/logout).
- Verificar si un usuario está autorizado (logged).
- Proteger las páginas con acceso restringido (protect).
- Leer datos suministrados por el usuario sea por GET o POST de forma insegura (input).
- Contestar pedidos con datos en notación JSON (json).

MOF es útil especialmente para hacer bosquejos de nuevas funcionalidades sin lidiar con bases de datos, modelos y sentencias SQL. Se puede usar dentro o fuera de un marco de trabajo aunque fuera del marco de trabajo se refiere a que **no debe ser usado en producción**. Consume malas prácticas en favor de prestar una funcionalidad sencilla al programador para la confección de bosquejos de código útiles para maquetar controladores o contestar pedidos de la interfaz frontal.

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

## Tareas pendientes (que postergaré eternamente)

- Documentar: MOF está pensada para el usuario desarrollador y el código es autosuficiente, muy corto y extremadamente sencillo. No requiere documentación.
