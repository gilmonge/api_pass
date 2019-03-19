<?php
	/**
	 *	Sistema de manejo de datos
	 *	Desarrollador: RexyStudio
	 *	Año de creación: 2019
	*/
$tiempo_inicio = microtime(true);

/*require_once("config.php");
$config = new config();*/

/**
 * Trae la conexion de la bd
 * $test=$config->get_connection();
	echo '
		<b>HOST_BD</b> : '.$test['HOST_BD'].'<br/>
		<b>NOMBRE_BD</b> : '.$test['NOMBRE_BD'].'<br/>
		<b>USUARIO_BD</b> : '.$test['USUARIO_BD'].'<br/>
		<b>CONTRASENA_BD</b> : '.$test['CONTRASENA_BD'].'<br/>
	';
*/
 
/**
 * Trae la configuración solicitada
 * echo($config->get_config('version'));
*/
 
/**
 * Trae la tabla solicitada
 * print_r($config->get_table('TABLA_ROLES_USUARIO'));
*/
 

$tiempo_fin = microtime(true);
//echo "<br/>Tiempo empleado: " . ($tiempo_fin - $tiempo_inicio); 
?>