<?php
    /**
     * Accesso a la base de datos.
     */
    define('USER2', 'u892946951_luck');
    define('PASS2', '1521390s');
    define('HOST2', 'mysql.hostinger.in');
    define('DB2', 'u892946951_graph');

    final class Conexion2 {
         
        private $conexion2;
         
        function __construct()
        {
            $this->conexion2 = new PDO("mysql:host=".HOST2.";dbname=".DB2.";charset=utf8", USER2, PASS2);
            $this->conexion2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexion2->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }
         
        public function getConexion2() {
            return $this->conexion2;
        }
         
        public function cerrar2() {
            $conexion2 = null;
        }
         
        public function __destruct() {
            $this->cerrar2();
        }
    }
     
?>