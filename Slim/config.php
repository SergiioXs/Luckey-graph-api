<?php

// Activamos las sesiones para el funcionamiento de flash['']
@session_start();
 
require 'Slim/Slim.php';

// El framework Slim tiene definido un namespace llamado Slim
// Por eso aparece \Slim\ antes del nombre de la clase.
\Slim\Slim::registerAutoloader();
 
// Creamos la aplicación.
$app = new \Slim\Slim();
$app->response->headers->set('Access-Control-Allow-Origin', '*');
// Configuramos la aplicación. http://docs.slimframework.com/#Configuration-Overview
// Se puede hacer en la línea anterior con:
// $app = new \Slim\Slim(array('templates.path' => 'vistas'));
// O bien con $app->config();
$app->config(array(
    'templates.path' => 'vistas',
));
 
// Indicamos el tipo de contenido y condificación que devolvemos desde el framework Slim.
$app->contentType('text/html; charset=utf-8');
 
// Definimos conexion de la base de datos.
// Lo haremos utilizando PDO con el driver mysql.
define('BD_SERVIDOR', 'infor011.mysql.guardedhost.com');
define('BD_NOMBRE', 'infor011_igastelum');
define('BD_USUARIO', 'infor011_igastel');
define('BD_PASSWORD', '47mu3JbBp2');
 
// Hacemos la conexión a la base de datos con PDO.
// Para activar las collations en UTF8 podemos hacerlo al crear la conexión por PDO
// o bien una vez hecha la conexión con
// $db->exec("set names utf8");


try {
    $db = new PDO('mysql:host=' . BD_SERVIDOR . ';dbname=' . BD_NOMBRE . ';charset=utf8', BD_USUARIO, BD_PASSWORD);
} catch (Exception $e) {
    echo 0;
    return 0;    
}

?>