<?php
	/**
	 *	Sistema de manejo de datos
	 *	Desarrollador: RexyStudio
	 *	Año de creación: 2019
	*/
	{ /* Reporta los errores */
		error_reporting(E_ALL);
		ini_set("display_errors", 0);
		ini_set("log_errors", "ON");
		ini_set("error_log", "error_log.log");
	/* Reporta los errores */ }
	
	require_once("config/config.php");
	require_once("config/quick_function.php");
	
	class Modulo{
		private $config;
		private $Quick_function;
	 
		public function __construct(){
			$this->config = new config();
			$this->Quick_function = new Quick_function();
		}
		
		public function modulo_get($api){
			$valido=false;

			if(!$valido){
				$this->Quick_function->printJSON(array("message"=>"Property not allowed"), 400);
			}
        }
		
		public function modulo_post($api){
			$valido=false;

			if(!$valido){
				$this->Quick_function->printJSON(array("message"=>"Property not allowed"), 400);
			}
        }
		
		public function modulo_put($api){
			$valido=false;

			if(!$valido){
				$this->Quick_function->printJSON(array("message"=>"Property not allowed"), 400);
			}
        }
		
		public function modulo_delete($api){
			$valido=false;

			if(!$valido){
				$this->Quick_function->printJSON(array("message"=>"Property not allowed"), 400);
			}
        }
		

		public function __destruct(){
			$this->config = null;
			$this->Quick_function = null;
		}
	}
?>