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
	
	require_once("config.php");
	require_once("database.php");
	
	class Quick_function{
		private $config;
		private $dbc;
	 
		public function __construct(){
			$this->config = new config();
			$this->dbc = new ConexionDB();
		}
		
		public function SQLDatos_SA($sql){ /* SQL datos sin argumentos */
			$dbo = $this->dbc->conexion();
            $stmt = $dbo->prepare($sql);
            $stmt->execute();
            return $stmt;
        }
		
		public function SQLDatos_CA($sql, $argumentos){ /* SQL datos con argumentos */
			$dbo = $this->dbc->conexion();
            $stmt = $dbo->prepare($sql);
            $stmt->execute($argumentos);
            return $stmt;
        }
		
		public function TraerParametro($parametro){ /* Trae parametro */
			$dbo = $this->dbc->conexion();
            $stmt = $dbo->prepare("SELECT valor FROM ".TABLA_PARAMETROS." WHERE identificador=:parametro");
            $stmt->execute(array(':parametro'=>$parametro));
			$par = $stmt->fetch();
            return $par['valor'];
        }
		
		public function Topnumber($campo, $tabla){ /* trae numero top */
			$dbo = $this->dbc->conexion();
            $stmt = $dbo->prepare("SELECT max(".$campo.") AS maximo FROM ".$tabla.";");
            $stmt->execute();
			$max = $stmt->fetch();
            return $max['maximo'];
        }
		
		public function codigo(){ /* Genera numero aleatorio para la contraseña*/
			return rand((int) 1000000000000, (int) 9999999999999);
        }
		
		public function generarStringRandom($length) {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$charactersLength = strlen($characters);
			$randomString = '';
			for ($i = 0; $i < $length; $i++) {
				$randomString .= $characters[rand(0, $charactersLength - 1)];
			}
			return $randomString;
		}
		
		public function generarIdUnico() {
			$idUnico = hash('ripemd160', (date("YmdHis").$this->generarStringRandom(20)));
			return $idUnico;
		}
		
		public function es_logueado(){ /* analiza si esta logueado */
			if(isset($_COOKIE['usuario'])){ $usuario=$_COOKIE['usuario']; }
			else{ $usuario=''; }
			
			if(isset($_COOKIE['codigo'])){ $codigo=$_COOKIE['codigo']; } 
			/* else if(isset($_SESSION['codigo'])){ $codigo=$_SESSION['codigo']; }  */
			else{ $codigo=''; }
			$ip= $this->get_ip_address();
		
			$par= $this->SQLDatos_CA("SELECT * FROM ".TABLA_USUARIOS_CONECTADOS." WHERE usuario=:usuario", array(':usuario'=>$usuario));
			$par = $par->fetch();
			
			
			if($par['usuario']!=''){
				$hora_acceso = date($par['hora_acceso']);
				$ipBD = $par['ip'];
				$codigoBD = $par['codigo'];
				
				if($codigoBD==$codigo){ /*($ipBD==$ip) && */
					
					if($par['abierto']==1){ return true; }
					else{
						$tiempo_permitido=$this->TraerParametro('login_time');
						
						$hora_actual = date('Y-m-d h:i:s');
						$minutos = ceil((strtotime($hora_actual) - strtotime($hora_acceso)) / 60);
						if ($tiempo_permitido>$minutos) { return true; }
						else{ 
							$this->SQLDatos_CA("DELETE FROM ".TABLA_USUARIOS_CONECTADOS." WHERE usuario=:usuario", array(':usuario'=>$usuario));
							unset($_SESSION['usuario']);	unset($_COOKIE['usuario']);
							unset($_SESSION['codigo']);		unset($_COOKIE['codigo']);
						return false; }
					}
					
				}
				else {
					unset($_SESSION['usuario']);	unset($_COOKIE['usuario']);
					unset($_SESSION['codigo']);		unset($_COOKIE['codigo']);
					return false; 
				}
			}
			else { $_SESSION['usuario']=''; $_SESSION['codigo']=''; return false; }
        }
		
		public function tiene_permiso(){ /* analiza si tiene acceso al archivo*/
			$archivo=$_SERVER['PHP_SELF'];
			$archivo=explode('/', $archivo);
			$archivo=array_pop($archivo);
			
			if(isset($_COOKIE['usuario'])){ $usuario=$_COOKIE['usuario']; }
			else{ $usuario=''; }
			
			$par= $this->SQLDatos_CA("SELECT * FROM ".TABLA_ADMINISTRADORES." WHERE usuario=:usuario", array(':usuario'=>$usuario));
			$par = $par->fetch();
			
			if($par==null || $par==''){
				$par= $this->SQLDatos_CA("SELECT * FROM ".TABLA_CREADORES." WHERE usuario=:usuario", array(':usuario'=>$usuario));
				$par = $par->fetch();
			}
			$_SESSION['idrol']=$par['rol'];
			$novalida=0;
			if($archivo=='index.php'){ $novalida=1; }
			else if($archivo=='documentos-info.php'){ $novalida=1; }
			
			if($novalida==0){
				
				$per= $this->SQLDatos_CA("
					SELECT ".TABLA_MENU_PERMISOS.".visualizar, ".TABLA_MENU_PERMISOS.".agregar, ".TABLA_MENU_PERMISOS.".editar, ".TABLA_MENU_PERMISOS.".borrar
					FROM ".TABLA_MENU_PERMISOS." 
					INNER JOIN ".TABLA_MENU_ADMIN." on ".TABLA_MENU_ADMIN.".id=".TABLA_MENU_PERMISOS.".id_menu_admin
					where (".TABLA_MENU_ADMIN.".script=:script OR ".TABLA_MENU_ADMIN.".subscripts like '%$archivo%') and  ".TABLA_MENU_PERMISOS.".id_rol=:id_rol", array(':script'=>$archivo, ':id_rol'=>$par['rol']));
				$per = $per->fetch();
					
					$_SESSION['visualizar']=$per['visualizar'];
					$_SESSION['agregar']=$per['agregar'];
					$_SESSION['editar']=$per['editar'];
					$_SESSION['borrar']=$per['borrar'];
			}
			else{ $_SESSION['visualizar']=1; $_SESSION['agregar']=1; $_SESSION['editar']=1; $_SESSION['borrar']=1; }
        }
		
		public function datos_administrador(){ /* analiza si esta logueado */
			$par= $this->SQLDatos_SA("SELECT * FROM ".TABLA_ADMINISTRADORES." WHERE usuario='".$_COOKIE['usuario']."' ");
			$admin=$par->fetch();
			return $admin;
        }
		
		public function get_ip_address() { /* obtiene la ip del cliente */
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) { $ip = $_SERVER['HTTP_CLIENT_IP']; }
			else {
				if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { $ip = $_SERVER['HTTP_X_FORWARDED_FOR']; }
				else { $ip = $_SERVER['REMOTE_ADDR']; }
			}
			return $ip;
		}
		
		public function encryptlabel($string) { /* genera encriptado */
			$key=LLAVELBL;
			$result = '';
			for($i=0; $i<strlen($string); $i++) {
				$char = substr($string, $i, 1);
				$keychar = substr($key, ($i % strlen($key))-1, 1);
				$char = chr(ord($char)+ord($keychar));
				$result.=$char;
			}
			return base64_encode($result);
		}

		public function decryptlabel($string) { /* genera desencriptado */
			$key=LLAVELBL;
			$result = '';
			$string = base64_decode($string);
			for($i=0; $i<strlen($string); $i++) {
				$char = substr($string, $i, 1);
				$keychar = substr($key, ($i % strlen($key))-1, 1);
				$char = chr(ord($char)-ord($keychar));
				$result.=$char;
			}
			return $result;
		}

		
		public function url_api() { /* genera desencriptado */
			$result='';
			$url = explode('/', str_replace('/api_pass/', '', $_SERVER["REQUEST_URI"]));

			$api = array("module" => $url[0]);

			for($i=1; $i<sizeof($url); $i++){
				$tem_URL = explode('=', $url[$i]);
				$key = (isset($tem_URL[0])) ? $tem_URL[0] : '' ;
				$value = (isset($tem_URL[1])) ? $tem_URL[1] : '' ;
				$tem_api = array($key => $value);
				$api+=$tem_api;
			}

			$result=$api;
			return $result;
		}

		public function printJSON($array, $status=200){
			header("Content-type: application/json; charset=utf-8");
			http_response_code($status);

			$array=array_merge(array("status" => $status), $array);
			
			$tiempo_fin = microtime(true);
			$tiempoUsado = array('response time'=>($tiempo_fin - $_SESSION['tiempo_inicio']));
			$array=array_merge($array, $tiempoUsado);
			echo json_encode($array); 
		}

		public function required_module($module){
			global $Quick_function;
	
			if(include_once("modules/$module/modulo.php")) {
				return  new Modulo();
			} else {
				$Quick_function->printJSON(array("message"=>"Not found module"), 404);
				return null;
			}
		}

		public function __destruct(){
			$this->config = null;
			$this->dbc = null;
		}
	}
?>