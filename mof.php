<?php namespace mof;

if (!isset($___mof_loaded)) {
   $___mof_loaded = true;
   
   function password($password, $hash = null) {
      $verify = (func_num_args() == 2);
      if (version_compare(PHP_VERSION, '5.5.0', '>=')) {
         if ($verify) {
            return password_verify($password, $hash);
         }
         return password_hash($password, PASSWORD_BCRYPT);
      } else {
         if ($verify) {
            return $hash == crypt($password, $hash);
         }
         $prefix = '$2y$07$';
         $blowfish = bin2hex(openssl_random_pseudo_bytes(22));
         return crypt($password, "$prefix$blowfish");
      }
   }
   
   function libraries($path = __DIR__) {
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
      $success = file_put_contents($filename, $raw);
      if ($success === false) {
         die("¡No se pudo escribir en el archivo $filename!");
      }
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
   
   function read($filename, $fallback = false) {
      if (file_exists($filename)) {
         return file_get_contents($filename);
      } else {
         return $fallback;
      }
   }

   function volatile() {
      $date = gmdate('D, d M Y H:i:s');
      header('Expires: Tue, 12 Ago 1980 23:30:00 GMT'); // La págnina expira en fecha pasada
      header("Last-Modified: $date GMT"); // Última actualización ahora cuando la cargamos 
      header('Cache-Control: no-cache, must-revalidate'); // No guardar en caché
      header('Pragma: no-cache'); // Encabezado paranóico para deshablitar caché
   }
   
   function upload($path = __DIR__, $filename = false, $mode = 0640) {
      $method =  $_SERVER['REQUEST_METHOD'];
      $file = $_FILES['file'];
      if ($method == 'POST' && !empty($file)) {
         if (!$filename) {
            $filename = $file['name'];
         }
         $folder = "$path/upload";
         $source = $file['tmp_name'];
         $target = "$folder/$filename";
   
         if ($file['error'] !== UPLOAD_ERR_OK) {
            print "Error al subir el archivo $filename";
            exit;
         }
   
         $success = move_uploaded_file($source, $target);
   
         if (!$success) { 
            print "Error al escribir el archivo $target";
            exit;
         }
   
         chmod($target, $mode);
      }
   }
   
   $___internal_input = null;
   
   function input($variable, $default = false) {
      $post = filter_input(INPUT_POST, $variable);
      if ($post) {
         return $post;
      }
      $get = filter_input(INPUT_GET, $variable);
      if ($get) {
         return $get;
      }
      global $___internal_input;
      if ($___internal_input === null) {
         $input = file_get_contents('php://input');
         $___internal_input = json_decode($input);
      }
      if ($___internal_input && property_exists($___internal_input, $variable)) {
         return $___internal_input->{$variable};
      }
      return $default;
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
      }
   }
   
   function logged($default = false) {
      session();
      $user = isset($_COOKIE['user']) ? $_COOKIE['user'] : false;
      if ($user) {
         restore($cookies);
         if (array_key_exists($user, $cookies)) {
            $key = isset($_COOKIE['key']) ? $_COOKIE['key'] : false;
            return $cookies[$user] == $key ? $user : $default;
         }
      }
      return isset($_SESSION['user']) ? $_SESSION['user'] : $default;
   }
   
   function login($user, $persist = false) {
      session();
      $_SESSION['user'] = $user;
      mt_srand(time());
      $key = mt_rand(1000000, 999999999);
      if ($persist) {
         $time = time() + 60 * 60 * 24 * 365;
         setcookie('user', $user, $time, '/');
         setcookie('key', $key, $time, '/');
         log($_COOKIE, true);
         restore($cookies);
         $cookies[$user] = $key;
         store($cookies);
         return $key;
      }
      return $key;
   }
   
   function logout($location = null) {
      session();
      $user = logged();
      if ($user) {
         restore($cookies);
         if (array_key_exists($user, $cookies)) {
            unset($cookies[$user]);
            store($cookies);
         }
         $time = time() -1;
         setcookie('user', '', $time, '/');
         setcookie('key', '', $time, '/');
         unset($_COOKIE['user']);
         unset($_COOKIE['key']);
         unset($_SESSION['user']);
      }
      session_destroy();
      if ($location) {
         redirect($location);
      }
   }
   
   function response($data, $type = 'text/xml') {
      $origin = $_SERVER['HTTP_ORIGIN'];
      $length = strlen($data);
      header("Access-Control-Allow-Origin: $origin");
      header("Access-Control-Allow-Credentials: true");
      header("Content-Type: $type; charset=utf-8");
      header("Content-Length: $length");
      print $data;
      exit;
   }
   
   function json($data, $pretty = false) {
      $json = json_encode($data, $pretty ? JSON_PRETTY_PRINT : 0);
      response($json, 'application/json');
   }
   
   function css($css) {
      response($css, 'text/css');
   }
   
   function html($html) {
      response($html, 'text/html');
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
   
   function log($message) {
      $path = __DIR__;
      $file = "$path/log/mof.log";
      $log = fopen($file, 'a') or die("¡No se pudo abrir el archivo $file!");
      $now = date('d/m/Y H:i:s');
      fwrite($log, (is_array($message) || is_object($message)) ? print_r($message, true) : "$now: $message\n");
      fclose($log);
   }
}

?>
