<?php
	/**
	 *	Sistema de manejo de datos
	 *	Desarrollador: RexyStudio
	 *	Año de creación: 2019
	*/
	require_once("config.php");
	
	class ConexionDB{
		private $config;
	 
		public function __construct(){
			$this->config = new config();
		}

		public function conexion(){
			$db=$this->config->get_connection();
			try {
				$conector = new PDO("mysql:dbname=".$db['NOMBRE_BD'].";host=".$db['HOST_BD'], $db['USUARIO_BD'], $db['CONTRASENA_BD'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES  'UTF8'"));
				$conector->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	
			}
			catch (PDOException $e) { echo 'Falló la conexión: ' . $e->getMessage(); }
			return $conector;
		}

		public function __destruct(){
			$this->config = null;
		}
	}
?>
