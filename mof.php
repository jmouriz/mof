<?php 

function password($password, $hash = null) {
   if (version_compare(PHP_VERSION, '5.5.0', '>=')) {
      if ($hash) {
         return password_verify($password, $hash);
      }
      return password_hash($password, PASSWORD_BCRYPT);
   } else {
      if ($hash) {
         return $hash == crypt($password, $hash);
      }
      $prefix = '$2y$07$';
      $blowfish = bin2hex(openssl_random_pseudo_bytes(22));
      return crypt($password, "$prefix$blowfish");
   }
}

function libraries($path) {
   if (file_exists($path)) {
      define('__CLASSES__', $path);
      spl_autoload_register(function($class) {
         $path = __CLASSES__;
         $class = strtr($class, '\\', '/');
         $file = "$path/$class.php";
         if (file_exists($file)) {
            require $file;
            return true;
         }
         /* todo => probar MyClassName -> My_Class_Name */
      });
   }
}

function filename($backtrace) {
   $path = __DIR__;
   $backtrace = $backtrace[0];
   $filename = $backtrace['file']; 
   $line = $backtrace['line'] -1;
   $file = file($filename);
   $input = $file[$line];
   preg_match('#\\$(\w+)#', $input, $match);
   $name = $match[1];
   return "$path/database/$name.dbz";
}

function store($variable) {
   $backtrace = debug_backtrace();
   $filename = filename($backtrace);
   $data = serialize($variable);
   $raw = gzdeflate($data, 1);
   file_put_contents($filename, $raw);
}

function restore(&$variable) {
   $backtrace = debug_backtrace();
   $filename = filename($backtrace);
   if (file_exists($filename)) {
      $raw = file_get_contents($filename);
      $data = gzinflate($raw);
      $variable = unserialize($data);
   } else {
      $variable = array();
   }
}

function input($variable, $default = false) {
   $post = filter_input(INPUT_POST, $variable);
   $get = filter_input(INPUT_GET, $variable);
   return $post ? $post : $get ? $get : $default;
}

function session() {
   $id = session_id();
   if ($id == '') {
      $session = session_start();
      if ($session) {
         return session_id();
      } else {
         return false;
      }
   } else {
      return $id;
   }
}

function protect($location = null) {
   if (!logged()) {
      if ($location) {
         redirect($location);
      }
      json(array('status' => 'unauthorized'));
      exit;
   }
}

function logged() {
   session();
   return isset($_SESSION['email']) ? $_SESSION['email'] : false;
}

function login($email) {
   session();
   $_SESSION['email'] = $email;
}

function logout($location = null) {
   session();
   unset($_SESSION['email']);
   session_destroy();
   if ($location) {
      redirect($location);
   }
}

function json($data, $pretty = false) {
   $json = json_encode($data, $pretty ? JSON_PRETTY_PRINT : 0);
   $length = strlen($json);

   header('Content-type: application/json; charset=utf-8');
   header("Content-length: $length");

   print $json;
}

function css($css) {
   $length = strlen($css);

   header('Content-type: text/css; charset=utf-8');
   header("Content-length: $length");

   print $css;
}

function redirect($location) {
   header("Location: $location");
   exit;
}

function debug($data, $exit = false) {
   print '<pre>';
   print_r($data);
   print '</pre>';
   if ($exit) {
      exit;
   }
}

function _log($message, $dump = false) {
   $path = __DIR__;
   $file = "$path/logs/mof.log";
   $log = fopen($file, 'a') or die("¡No se pudo abrir el archivo $file!");
   $now = date('d/m/Y H:i:s');
   fwrite($log, $dump ? print_r($message, true) : "$now: $message\n");
   fclose($log);
}

?>
