<?php
	/**
	 *	Sistema de manejo de datos
	 *	Desarrollador: RexyStudio
	 *	Año de creación: 2019
	*/
	@session_start();
	$_SESSION['tiempo_inicio'] = microtime(true);
	require_once("config/quick_function.php");

	$Quick_function = new Quick_function();

	

	$method = $_SERVER['REQUEST_METHOD'];
	switch ($method) { 
		case 'GET': /** leer y consultar */
			$api=$Quick_function->url_api();
			$modulo=$Quick_function->required_module($api['module']);
			if ($modulo!=null) {
				$modulo->modulo_get($api);
			}
			
		break;
		case 'POST': /** crear y/o peticiones seguras */
			$api=$Quick_function->url_api();
			$modulo=$Quick_function->required_module($api['module']);
			if ($modulo!=null) {
				$modulo->modulo_post($api);
			}
		break; 
		case 'PUT': /** editar */
			$api=$Quick_function->url_api();
			$modulo=$Quick_function->required_module($api['module']);
			if ($modulo!=null) {
				$modulo->modulo_put($api);
			}
		break; 
		case 'DELETE': /** eliminar */
			$api=$Quick_function->url_api();
			$modulo=$Quick_function->required_module($api['module']);
			if ($modulo!=null) {
				$modulo->modulo_delete($api);
			}
		break;
		default:
			$Quick_function->printJSON(array("message"=>"Method not allowed"), 405);
		break; 
	}

?>
