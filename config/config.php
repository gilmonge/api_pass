<?php
	/**
	 *	Sistema de manejo de datos
	 *	Desarrollador: RexyStudio
	 *	Año de creación: 2019
	*/
require_once("db.php");

class config{
    private $connection;
 
    public function __construct(){
        $connection = new Connection();
        $this->connection = $connection->get_connection();
    }
 
    public function get_connection(){
        /**
		 * Proporciona los datos de conexión a la base de datos
		 */
        $conexion = $this->connection->query('SELECT * FROM conexion'); $datos = array();
        if( ! empty( $conexion ) ) {
			while ($row=$conexion->fetchObject()) {
				$linea = array(get_object_vars($row)['clave'] => get_object_vars($row)['valor']);
				$datos += $linea;
			}
        }
		return $datos;
    }
 
    public function get_config( $clave ){
        /**
		 * Proporciona el parámetro de configuración solicitado
		 */
		$dato = '';
		$config = $this->connection->query('SELECT * FROM configuraciones WHERE clave = :clave');
        $config->execute(array(':clave'=>$clave));
        if( ! empty( $config ) ){
			while ($row=$config->fetchObject()) {
				$dato = get_object_vars($row)['valor'];
			}
        }
        return $dato;
    }

    public function get_table( $clave ){
        /**
		 * Proporciona el parámetro de configuración solicitado
		 */
		$dato = '';
		$config = $this->connection->query('SELECT * FROM tablas WHERE clave = :clave');
        $config->execute(array(':clave'=>$clave));
        if( ! empty( $config ) ){
			while ($row=$config->fetchObject()) {
				$dato = get_object_vars($row)['valor'];
			}
        }
        return $dato;
    }
 
    public function __destruct(){
        $this->connection = null;
    }
}
?>