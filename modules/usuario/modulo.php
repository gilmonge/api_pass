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
			if (isset($api['listar'])) {
				if(isset($api['admin']) && ($api['admin']!='' && $api['admin']!=null)){
					$this->listar_usuarios();
					$valido=true;
				}
			}
			else if (isset($api['id'])) {
				$this->userinfo($api['id']);
				$valido=true;
			}
			
			
			if(!$valido){
				$this->Quick_function->printJSON(array("message"=>"Property not allowed"), 400);
			}
        }
		
		public function modulo_post($api){
			$valido=false;

			if (isset($api['insertar'])) {
				if(
					isset($_POST['name']) && ($_POST['name']!='' && $_POST['name']!=null) &&
					isset($_POST['email']) && ($_POST['email']!='' && $_POST['email']!=null) &&
					isset($_POST['password']) && ($_POST['password']!='' && $_POST['password']!=null)&&
					isset($_POST['confpass']) && ($_POST['confpass']!='' && $_POST['confpass']!=null)
				){
					$this->insertar_usuarios($_POST['name'], $_POST['email'], $_POST['password'], $_POST['confpass']);
					$valido=true;
				}
			}
			else if (isset($api['actualizar'])) {
				if(
					isset($_POST['id']) && ($_POST['id']!='' && $_POST['id']!=null) &&
					isset($_POST['name']) && ($_POST['name']!='' && $_POST['name']!=null)
				){
					$this->actualizar_usuario($_POST['id'], $_POST['name']);
					$valido=true;
				}
			}

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


		private function listar_usuarios(){
			$usuarios=array(); $cont=0;
			
			$user = $this->Quick_function->SQLDatos_SA("SELECT * FROM pass_usuario");
			
			while ($row=$user->fetch()) {
				$temp_users=array($cont=>array(
					'id'=>$row['id'], 
					'nombre'=>$row['nombre'], 
					'correo'=>$row['correo'], 
					'contrasenia'=>$row['contrasenia']
				));
				$usuarios=array_merge($usuarios, $temp_users);
				$cont++;
			}
			
			$resp=array('users'=>$usuarios);
			$this->Quick_function->printJSON($resp);
		}
		
		private function userinfo($id){
			$usuarios=array(); $cont=0;
			
			$user = $this->Quick_function->SQLDatos_CA("SELECT * FROM pass_usuario where id=:id", array(':id'=>$id));
			
			while ($row=$user->fetch()) {
				$temp_users=array($cont=>array(
					'id'=>$row['id'], 
					'nombre'=>$row['nombre'], 
					'correo'=>$row['correo'], 
					'contrasenia'=>$row['contrasenia']
				));
				$usuarios=array_merge($usuarios, $temp_users);
				$cont++;
			}
			
			$resp=array('users'=>$usuarios);
			$this->Quick_function->printJSON($resp);
		}
		
		private function insertar_usuarios($name, $email, $password, $confpass){
			$usuarios=array();
			$tabla = $this->config->get_table('TABLA_USUARIOS');

			$user = $this->Quick_function->SQLDatos_CA("SELECT * FROM $tabla WHERE correo=:correo", array(':correo'=>$email));
			$cuenta = $user->rowCount();
			
			if($cuenta==0){
				if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
					if($password==$confpass){
						$id = $this->Quick_function->generarIdUnico();
						$datos = array(':id'=>$id, ':name'=>$name, ':email'=>$email, ':password'=>$password);

						try {
							$res=$this->Quick_function->SQLDatos_CA("INSERT INTO $tabla (id, nombre, correo, contrasenia) VALUES (:id, :name, :email, :password)", $datos);
							$this->Quick_function->printJSON(array("message"=>"Success saved", "id"=>$id), 201);
						} catch (Exception $e) {
							$Quick_function->printJSON(array("status" => "409", "message"=>"Error saving"), 500);
						}
					}
					else{
						$this->Quick_function->printJSON(array("message"=>"Passwords do not match"), 409);
					}
				}
				else{
					$this->Quick_function->printJSON(array("message"=>"Email is not valid"), 409);
				}
			}
			else{
				$this->Quick_function->printJSON(array("message"=>"Email has been used"), 409);
			}
		}

		private function actualizar_usuario($id, $name){
			$tabla = $this->config->get_table('TABLA_USUARIOS');

			try {
				$user = $this->Quick_function->SQLDatos_CA("UPDATE $tabla SET nombre=:name WHERE id=:id", array(':id'=>$id, ':name'=>$name));
				$this->Quick_function->printJSON(array("message"=>"Success update", "id"=>$id), 201);
			} catch (Exception $e) {
				$Quick_function->printJSON(array("status" => "409", "message"=>"Error saving"), 500);
			}
		}
	}
?>