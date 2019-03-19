<?php
	/**
	 *	Sistema de manejo de datos
	 *	Desarrollador: RexyStudio
	 *	Año de creación: 2019
	*/
	class Connection{
		private $connection;
	
		public function __construct(){
			$servidor = PHP_OS; $sqlite='';
			if($servidor==='Linux'){ $sqlite='/home/parpatch/config/'; }
			$this->connection = new PDO('sqlite:'.$sqlite.'config.sqlite3') or die('Base de datos inaccesible');
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
		}
	
		public function get_connection(){
			return $this->connection;
		}
	}
?>