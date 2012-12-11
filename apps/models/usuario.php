<?php
require_once 'basecontroller.php';
class Usuario extends BaseController {
	public function __construct() {
		parent::__construct ();
		$this->load->helper ( 'url' );
		$this->load->model ( "Usuario_model", "usuario" );
		$this->load->model ( "locacion_model", "locacion" );
	}
	public function guardarMensaje() {
		$res = false;
		if ($this->myuser) {
			$mensaje = $this->input->post ( "mensaje" );
			$articulo = $this->input->post ( "articulo" );
			$receptor = $this->input->post ( "receptor" );
			$articulo = $articulo ? $articulo : null;
			$res = $this->usuario->guardarMensaje ( $this->myuser->id, $receptor, $mensaje, $articulo );
		}
		$this->output->set_output ( json_encode ( array (
				"exito" => $res 
		) ) );
	}
	public function codigos() {
		$todos = $this->usuario->darUsuarios ();
		if ($todos) {
			foreach ( $todos as $usuario ) {
				$codigo = encriptarNombre ( $usuario->seudonimo );
				var_dump ( $codigo );
				$this->usuario->id = $usuario->id;
				$this->usuario->actualizarXCampos ( array (
						"codigo_oculto" => $codigo 
				) );
			}
		}
	}
	public function cargarCiudades() {
		$resp = "";
		$p = $this->input->post ( "pais" );
		if ($p) {
			$ciudades = $this->locacion->listarCiudades ( $p );
			$resp = "<option value=''>Elegir</option>";
			foreach ( $ciudades as $ciudad ) {
				$resp .= "<option value='$ciudad->id'>$ciudad->nombre</option>";
			}
		}
		$this->output->set_output ( $resp );
	}
	public function index() {
		$this->loadGUI ();
	}
	public function login($urlBack = false) {
		$this->loginRedirect ();
		$data = array ();
		if ($urlBack) {
			$data ["urlBack"] = $urlBack;
		}
		$this->loadGUI ( "usuario/login", $data );
	}
	public function perfil($usuario = false, $seccion = false) {
		$data = array ();
		switch ($seccion) {
			case "sell" :
				$view = "usuario/ventas";
				break;
			default :
				$view = "usuario/perfil";
				break;
		}
		
		$data ["complejo"] = true;
		$data ["seccion"] = "profile";
		$data ["profile"] = true;
		$this->loadGUI ( $view, $data );
	}
	public function messages() {
		$view = "usuario/messages";
		$this->load->library ( 'table' );
		$this->load->library ( 'pagination' );
		$this->load->model ( "usuario_mensaje" ); // cargamos el archivo
		                                          // usuario_mensaje.php
		                                          // $config['total_rows'] =
		                                          // count($data_paging);
		                                          // $config['total_rows_usuario']
		                                          // =
		                                          // $this->usuario_mensaje->cantidad_mensaje_usuario($this->myuser->id);
		                                          // $config['total_rows'] =
		                                          // $this->usuario_mensaje->get_mensaje_cantidad();
		                                          // //llamo a una funcion del
		                                          // modelo
		                                          // que me retorna la cantidad de
		                                          // usuarios que tengo en la
		                                          // tabla
		                                          // usuario.
		$config ['per_page'] = '30'; // cantidad de filas a mostrar por pagina
		                             
		// $this->pagination->initialize($config); // le paso el vector con mis
		                             // configuraciones al paginador
		
		$view = "usuario/messages";
		$this->load->library ( 'table' );
		$this->load->library ( 'pagination' );
		$this->load->model ( "usuario_mensaje" ); // cargamos el archivo
		                                          // usuario_mensaje.php
		
		$data ['result'] = $this->usuario_mensaje->get_message ( $this->myuser->id );
		$data ['num_results'] = $this->usuario_mensaje->cantidad_mensaje_usuario ( $this->myuser->id );
		// $data['num_results'] = $results['num_rows'];
		// $config['total_rows'] = count($data_paging);
		// $config['total_rows_usuario'] =
		// $this->usuario_mensaje->cantidad_mensaje_usuario($this->myuser->id);
		// $config['total_rows'] =
		// $this->usuario_mensaje->get_mensaje_cantidad(); //llamo a una funcion
		// del modelo que me retorna la cantidad de usuarios que tengo en la
		// tabla usuario.
		$config ['per_page'] = '25'; // cantidad de filas a mostrar por pagina
		$config = array ();
		// $this->pagination->initialize($config); // le paso el vector con mis
		// configuraciones al paginador
		
		// $data['result'] =
		// $this->usuario_mensaje->get_message($this->myuser->id);
		// $data['messages_count'] =
		// $this->usuario_mensaje->cantidad_mensaje_usuario($this->myuser->id);
		// $data["messages_count"] = $res[1];
		
		$this->loadGUI ( $view, $data );
	}
	private function numArticulosXCategoriaXUser() {
		$this->load->model ( "articulo_model", "articulo" );
		$this->load->model ( "categoria_model", "categoria" );
		$cats = $this->articulo->numArticulosXCategoriaXUser ( $this->myuser->id );
		$cs = array ();
		if (is_array ( $cats )) {
			foreach ( $cats as $categoria ) {
				$arbolpadre = $this->categoria->darArbolCategoria ( $categoria->categoria, $this->idioma->language->id );
				$puntero = &$cs;
				for($i = 0; $i < count ( $arbolpadre ); $i ++) {
					if (! isset ( $puntero [$arbolpadre [$i] ["id"]] )) {
						$puntero [$arbolpadre [$i] ["id"]] = array (
								"datos" => $arbolpadre [$i],
								"hijos" => array () 
						);
						$puntero [$arbolpadre [$i] ["id"]] ["datos"] ["cantidad"] = 0;
					}
					$puntero = &$puntero [$arbolpadre [$i] ["id"]] ["hijos"];
				}
				$puntero = &$cs;
				for($i = 0; $i < count ( $arbolpadre ); $i ++) {
					if (isset ( $puntero [$arbolpadre [$i] ["id"]] )) {
						$puntero [$arbolpadre [$i] ["id"]] ["datos"] ["cantidad"] += $categoria->cantidad;
						$puntero = &$puntero [$arbolpadre [$i] ["id"]] ["hijos"];
					}
				}
			}
		}
		return $cs;
	}
	public function removeImage() {
		$return = array ();
		if (isset ( $this->myuser ) && $this->myuser) {
			$imagen = $this->myuser->imagen;
			$baseruta = "files/" . $this->myuser->id . "/";
			$ruta = BASEPATH . "../" . $baseruta;
			$ext = strtolower ( pathinfo ( $imagen, PATHINFO_EXTENSION ) );
			$name = strtolower ( pathinfo ( $imagen, PATHINFO_FILENAME ) );
			if (is_file ( $ruta . $imagen )) {
				@unlink ( $ruta . $imagen );
			}
			if (is_file ( "$ruta$name.original.$ext" )) {
				@unlink ( "$ruta$name.original.$ext" );
			}
			if (is_file ( "$ruta$name.thumb.$ext" )) {
				@unlink ( "$ruta$name.thumb.$ext" );
			}
			if (is_file ( "$ruta$name.small.$ext" )) {
				@unlink ( "$ruta$name.small.$ext" );
			}
			$this->usuario->eliminarImagen ( $this->myuser->seudonimo );
			$return = array (
					"quien" => $this->input->post ( "quien" ) 
			);
		} else {
			$return = array (
					'error' => true,
					'mensaje' => "No se pudo eliminar la imagen, intententelo mas tarde.",
					"quien" => $this->input->post ( "quien" ) 
			);
		}
		$this->output->set_content_type ( 'text/plain' )->set_output ( json_encode ( $return ) );
	}
	public function editar($seccion = "") {
		if ($this->isLogged ()) {
			$datos = array ();
			switch ($seccion) {
				case "buy-sell" :
					$defecto = $this->input->post ( "pais" );
					$defecto = $defecto ? $defecto : $this->myuser->pais->codigo3;
					$defecto = $defecto ? $defecto : "ESP";
					$datos ["view"] = "usuario/compra-venta";
					$datos ["pos"] = 2;
					$datos ["paises"] = $this->locacion->listarPaises ();
					$datos ["ciudades"] = $this->locacion->listarCiudades ( $defecto );
					$datos ["paisDefecto"] = $defecto;
					break;
				case "account" :
					$datos ["view"] = "usuario/cuenta";
					$datos ["pos"] = 3;
					break;
				default :
					$datos ["view"] = "usuario/edit-perfil";
					$datos ["pos"] = 1;
					break;
			}
			$this->loadGUI ( "usuario/edit", $datos );
		} else {
			$this->loadGUI ( "usuario/login" );
		}
	}
	public function changepassword($code) {
		$code = decodificarPassword ( $code );
		$password = substr ( $code, 0, 8 );
		$id = substr ( $code, 8 );
		if ($this->usuario->darUsuarioXID ( $id )) {
			$this->usuario->actualizarPassword ( $password );
			if ($this->usuario->login ()) {
				redirect ( "store/{$this->myuser->seudonimo}" );
				return;
			}
		}
		redirect ( "login" );
	}
	public function forgot() {
		// $this->loginRedirect ();
		$this->loadGUI ( "usuario/forgot" );
	}
	public function register() {
		$this->loginRedirect ();
		$this->load->library ( 'session' );
		$this->load->library ( 'antispam' );
		$this->mysession->set_userdata ( "CAPTCHAANT", $this->mysession->userdata ( "CAPTCHA" ) );
		$captcha = $this->antispam->get_antispam_image ( array (
				'img_path' => './captcha/',
				'img_url' => base_url () . 'captcha/',
				'img_height' => '45' 
		) );
		$this->mysession->set_userdata ( "CAPTCHA", $captcha );
		$this->loadGUI ( "usuario/registro" );
	}
	private function loginRedirect() {
		if ($this->isLogged ()) {
			redirect ( "/" );
			exit ();
		}
	}
	public function logout() {
		$this->load->library ( "mysession" );
		
		$this->load->helper ( "cookie" );
		$this->mysession->unset_userdata ( "LVSESSION" );
		$this->mysession->unset_userdata ( "USER_DATA" );
		delete_cookie ( "LVSESSION", "", "/" );
		redirect ( "login" );
	}
	public function process() {
		$this->load->helper ( "form" );
		$this->load->library ( 'session' );
		$data = parent::process ();
		if (isset ( $_POST ["__accion"] )) {
			switch ($_POST ["__accion"]) {
				case "registrar" :
					return array_merge ( $data, $this->post_registrar () );
				case "login" :
					return array_merge ( $data, $this->post_login () );
				case "olvidar" :
					return array_merge ( $data, $this->post_olvidar () );
				case "editar-perfil" :
					return array_merge ( $data, $this->post_editarPerfil () );
				case "editar-cuenta" :
					return array_merge ( $data, $this->post_editarCuenta () );
				case "compra-venta" :
					return array_merge ( $data, $this->post_compraVenta () );
			}
		}
		$uri = explode ( "/", uri_string () );
		$section = array_shift ( $uri );
		$user = array_shift ( $uri );
		$lista = array_shift ( $uri );
		$usuarioListar = ($this->myuser ? $this->myuser->id : false);
		if ($section == "store") {
			$section = "profile";
			if ($this->myuser && $user !== $this->myuser->seudonimo) {
				$data ["usuarioExterno"] = $this->usuario->darUsuarioXSeudonimo ( $user );
				if ($data ["usuarioExterno"]) {
					$data ["usuarioExterno"]->pais = $data ["usuarioExterno"]->darPais ();
					$data ["externo"] = $data ["usuarioExterno"];
					$data ["usuario"] = $data ["usuarioExterno"];
					$data ["usuarioPropio"] = $this->myuser;
					$usuarioListar = $data ["usuario"]->id;
				}
			} else {
				$data ["usuarioExterno"] = $this->usuario->darUsuarioXSeudonimo ( $user );
				if ($data ["usuarioExterno"]) {
					$data ["usuarioExterno"]->pais = $data ["usuarioExterno"]->darPais ();
					$data ["externo"] = $data ["usuarioExterno"];
					$data ["usuario"] = $data ["usuarioExterno"];
					$data ["usuarioPropio"] = false;
					$usuarioListar = $data ["usuario"]->id;
				} else {
					redirect ( "/", "refresh" );
				}
			}
		}
		$action = array_shift ( $uri );
		$id = array_shift ( $uri );
		if ($section == "profile") {
			$this->load->model ( "Articulo_model", "articulo" );
			// $action = $id;
			switch ($lista) {
				case "sell" :
					if ($this->myuser) {
						$data = array_merge ( $this->articulo->leerArticulosVendidos ( $this->myuser->id, $this->input->get ( "pagina" ) ), $data );
					} else {
						redirect ( "login", "refresh" );
						return;
					}
					break;
				
				default :
					$data = array_merge ( $this->articulo->leerArticulos ( 0, $this->input->get ( "criterio" ), $action, $this->input->get ( "orden" ), $this->input->get ( "ubicacion" ), $this->input->get ( "categoria" ), $this->idioma->language->id, $usuarioListar ), array (
							"orden" => $this->input->get ( "orden" ),
							"ubicacion" => $this->input->get ( "ubicacion" ),
							"categoria" => $this->input->get ( "categoria" ) 
					), $data );
					break;
			}
			$data = array_merge ( $data, array (
					"section" => $action 
			) );
		}
		return array_merge ( parent::process (), $data );
	}
	private function post_compraVenta() {
		$errores = array ();
		if ($this->myuser) {
			$this->load->library ( 'form_validation' );
			$config = array (
					array (
							'field' => 'nombre',
							'label' => 'Nombre',
							'rules' => 'required' 
					),
					array (
							'field' => 'apellido',
							'label' => 'Apellido',
							'rules' => 'required' 
					),
					array (
							'field' => 'dni-num',
							'label' => 'DNI(numero)',
							'rules' => 'required|integer' 
					),
					array (
							'field' => 'dni-letra',
							'label' => 'DNI(letra)',
							'rules' => 'required|alpha' 
					),
					array (
							'field' => 'direccion',
							'label' => 'Direccion',
							'rules' => 'required' 
					),
					array (
							'field' => 'pais',
							'label' => 'País',
							'rules' => 'required' 
					),
					array (
							'field' => 'ciudad',
							'label' => 'ciudad',
							'rules' => 'required' 
					),
					array (
							'field' => 'codigo_postal',
							'label' => 'Código postal',
							'rules' => 'required|integer' 
					),
					array (
							'field' => 'telefono',
							'label' => 'Teléfono',
							'rules' => 'required|integer' 
					),
					array (
							'field' => 'paypal',
							'label' => 'Email de paypal',
							'rules' => 'email' 
					) 
			);
			$this->form_validation->set_error_delimiters ( '', '' );
			$this->form_validation->set_rules ( $config );
			if ($this->form_validation->run ()) {
				if ($this->myuser->actualizarXCampos ( array (
						"nombre" => $this->input->post ( "nombre" ),
						"apellido" => $this->input->post ( "apellido" ),
						"dni" => $this->input->post ( "dni-num" ) . "-" . $this->input->post ( "dni-letra" ),
						"direccion" => $this->input->post ( "direccion" ),
						"codigo_postal" => $this->input->post ( "codigo_postal" ),
						"telefono" => $this->input->post ( "telefono" ),
						"pais" => $this->input->post ( "pais" ),
						"ciudad" => $this->input->post ( "ciudad" ),
						"paypal" => $this->input->post ( "paypal" ),
						"estado" => "Activo" 
				) )) {
					redirect ( "store/{$this->myuser->seudonimo}" );
				}
			}
		}
		return $errores;
	}
	private function post_editarCuenta() {
		$errores = array ();
		if ($this->myuser) {
			$seudonimo = $this->input->post ( "seudonimo" );
			$campos = array ();
			if ($seudonimo && $seudonimo !== $this->myuser->seudonimo) {
				if ($this->usuario->darUsuarioXSeudonimo ( $seudonimo )) {
					$errores ["errorSeudonimo"] = "El seudonimo no puede usarse por que ya existe";
				} else {
					$campos ["seudonimo"] = $seudonimo;
				}
			}
			$password = $this->input->post ( "password" );
			$chp = false;
			if ($password) {
				$p = encriptacion ( $this->myuser->base, $password );
				if ($p === $this->myuser->password) {
					$chp = true;
				} else {
					$errores ["errorPassword"] = "El Password Actual es incorrecto";
				}
			}
			$nuevopassword = $this->input->post ( "nuevoPassword" );
			$repetirpassword = $this->input->post ( "repetirPassword" );
			if ($chp && ($repetirpassword || $nuevopassword)) {
				
				if ($nuevopassword == $repetirpassword && $nuevopassword) {
					if (strlen ( $nuevopassword ) >= 8) {
						$campos ["password"] = encriptacion ( $this->myuser->base, $nuevopassword );
					} else {
						$errores ["errorNuevoPassword"] = "La Contraseña debe contener mas de 8 caracteres.";
					}
				} else {
					$errores ["errorNuevoPassword"] = "Ambos Campos deben ser iguales";
				}
			}
			$email = $this->input->post ( "email" );
			$oemail = $this->myuser->email;
			if ($email && $email !== $this->myuser->email) {
				if ($this->usuario->darUsuarioXEmail ( $email )) {
					$errores ["errorEmail"] = "El Email no puede usarse por que ya existe";
				} else {
					$campos ["email"] = $email;
				}
			}
			
			$notificaciones = $this->input->post ( "notificaciones" );
			if (! $notificaciones) {
				$campos ["notificaciones"] = true;
			} else {
				$campos ["notificaciones"] = false;
			}
			
			if (count ( $campos ) > 0) {
				if (isset ( $campos ["seudonimo"] )) {
					$campos ["seudonimo"] = preg_replace ( "/[^a-zA-Z0-9\._-]/i", "", $campos ["seudonimo"] );
				}
				if ($this->myuser->actualizarXCampos ( $campos )) {
					$this->load->library ( "myemail" );
					if (isset ( $campos ["seudonimo"] )) {
						$r = ($this->input->cookie ( "LVSESSION" ) ? true : false);
						$dir = BASEPATH . "../files/";
						// @rename ( $dir . $this->myuser->seudonimo, $dir .
						// $campos ["seudonimo"] );
						$this->myuser->seudonimo = $campos ["seudonimo"];
						$this->myuser->login ( $r );
					}
					if (isset ( $campos ["email"] )) {
						$this->myuser->email = $campos ["email"];
						$this->myemail->enviarTemplate ( $oemail, "Cambio de Correo electronico", "mail/emailchange_mail", array (
								"email" => $this->myuser->email 
						) );
						/*
						 * $template = $this->load->view (
						 * "mail/emailchange_mail", array ( "email" =>
						 * $this->myuser->email ), true );
						 * $this->myemail->enviar ( $oemail, "Cambio de Correo
						 * electronico", $template );
						 */
					}
					if (isset ( $campos ["password"] )) {
						$this->myuser->password = $campos ["password"];
						$this->myemail->enviarTemplate ( $this->myuser->email, "Cambio de contraseña", "mail/passwordchange_mail", array (
								"password" => $nuevopassword,
								"usuario" => $this->myuser->seudonimo 
						) );
						/*
						 * $template = $this->load->view (
						 * "mail/passwordchange_mail", array ( "password" =>
						 * $nuevopassword, "usuario" => $this->myuser->seudonimo
						 * ), true ); $this->myemail->enviar (
						 * $this->myuser->email, "Cambio de contraseña",
						 * $template );
						 */
					}
					
					redirect ( "edit/account" );
				}
			}
		}
		return $errores;
	}
	private function post_editarPerfil() {
		$imagenes = $this->input->post ( "imagenes" );
		$descripcion = $this->input->post ( "descripcion" );
		$errores = array ();
		if ($imagenes) {
			$this->usuario->actualizarImagen ( $this->myuser->id, $imagenes );
			$this->myuser->imagen = $this->usuario->imagen;
		}
		if ($descripcion) {
			if ($this->myuser) {
				$descripcion = strip_tags ( $descripcion );
				$this->myuser->descripcion = $descripcion;
				$this->myuser->actualizarXCampo ( "descripcion" );
			}
		}
		return $errores;
	}
	private function post_olvidar() {
		$this->load->library ( 'form_validation' );
		$config = array (
				array (
						'field' => 'email',
						'label' => 'Email',
						'rules' => 'required|valid_email' 
				) 
		);
		$errores = array ();
		$this->form_validation->set_error_delimiters ( '<span class="errorTxt">', '</span>' );
		$this->form_validation->set_rules ( $config );
		if ($this->form_validation->run ()) {
			if ($this->usuario->darUsuarioXEmail ( $this->input->post ( "email" ) )) {
				if ($this->usuario->enviarPassword ()) {
					$errores ["error"] = "Se le enviara un correo con los detalles de recuperacion, por favor revise tambien su bandeja de mensajes no deseados.";
				} else {
					$errores ["error"] = "No se pudo enviar el correo de confirmación.";
				}
			} else {
				$errores ["error"] = "El email que ingresaste no se encuentra en nuestro sistema.";
			}
		}
		return $errores;
	}
	private function post_login() {
		$this->load->library ( 'form_validation' );
		$config = array (
				array (
						'field' => 'seudonimo',
						'label' => 'Seudónimo',
						'rules' => 'required' 
				),
				array (
						'field' => 'password',
						'label' => 'Contraseña',
						'rules' => 'required' 
				) 
		);
		$errores = array ();
		$this->form_validation->set_error_delimiters ( '<span class="errorTxt">', '</span>' );
		$this->form_validation->set_rules ( $config );
		if ($this->form_validation->run ()) {
			$this->usuario->seudonimo = $_POST ["seudonimo"];
			$this->usuario->password = $_POST ["password"];
			if ($this->usuario->login ( $this->input->post ( "recuerdame" ), true )) {
				$urlBack = $this->input->post ( "urlBack" );
				if ($urlBack) {
					redirect ( base64_decode ( $urlBack ) );
				} else {
					redirect ( "store/{$this->myuser->seudonimo}" );
				}
			} else {
				if (isset ( $this->usuario->__error ["seudonimo"] )) {
					$errores ["errorSeudonimo"] = $this->usuario->__error ["seudonimo"];
				} elseif (isset ( $this->usuario->__error ["password"] )) {
					$errores ["errorPassword"] = $this->usuario->__error ["password"];
				} else {
					$errores ["error"] = "El usuario o la contraseña son Incorrectos.";
				}
			}
		}
		return $errores;
	}
	private function post_registrar() {
		$this->load->library ( 'form_validation' );
		$config = array (
				array (
						'field' => 'seudonimo',
						'label' => 'Seudónimo',
						'rules' => 'required|max_length[50]|min_length[6]' 
				),
				array (
						'field' => 'password',
						'label' => 'Contraseña',
						'rules' => 'required|max_length[50]|min_length[8]' 
				),
				array (
						'field' => 'passconf',
						'label' => 'Repetir Contraseña',
						'rules' => 'required|max_length[50]|min_length[8]|callback_passconf_check' 
				),
				array (
						'field' => 'email',
						'label' => 'Email',
						'rules' => 'required|valid_email' 
				),
				array (
						'field' => 'codigo',
						'label' => 'Código de Imagen',
						'rules' => 'required|callback_codigo_check' 
				) 
		);
		$errores = array ();
		if ($this->usuario->verificarSeudonimo ( $_POST ["seudonimo"] )) {
			$errores ["errorSeudonimo"] = "El Seudónimo ya se encuentra Registrado";
		}
		if ($this->usuario->verificarEmail ( $_POST ["email"] )) {
			$errores ["errorEmail"] = "El email ya fue Registrado";
		}
		$this->form_validation->set_error_delimiters ( '<span class="errorTxt">', '</span>' );
		$this->form_validation->set_rules ( $config );
		if ($this->form_validation->run () && ! isset ( $errores ["errorEmail"] ) && ! isset ( $errores ["errorSeudonimo"] )) {
			$this->usuario->seudonimo = $_POST ["seudonimo"];
			$this->usuario->codigo_oculto = encriptarNombre ( $_POST ["seudonimo"] );
			$this->usuario->password = $_POST ["password"];
			$this->usuario->email = $_POST ["email"];
			if ($this->usuario->registrar ()) {
				redirect ( "store/{$this->myuser->seudonimo}" );
			}
		}
		return $errores;
	}
	public function codigo_check($codigo) {
		$captcha = $this->mysession->userdata ( "CAPTCHA" );
		if (strtolower ( $codigo ) !== strtolower ( $captcha ["word"] )) {
			$this->form_validation->set_message ( "codigo_check", "El Codigo es incorrecto" );
			return false;
		}
		return true;
	}
	public function passconf_check() {
		if ($_POST ["password"] != $_POST ["passconf"]) {
			$this->form_validation->set_message ( "passconf_check", "Ambos campos de contraseña deben ser iguales" );
			return false;
		}
		return true;
	}
	public function verMasArticulosVendidos() {
		$this->load->model ( "Articulo_model", "articulo" );
		$inicio = $this->input->post ( "inicio" );
		$section = $this->input->post ( "section" );
		$data ["total"] = 0;
		$data ["final"] = 0;
		$data = array_merge ( $this->articulo->leerArticulosVendidos ( $this->myuser->id, $inicio ), array (
				"section" => $section 
		) );
		$json = array ();
		$a = $data ["articulos"] ? $data ["articulos"] : false;
		if (isset ( $a ) && $a && is_array ( $a ) && count ( $a ) > 0) {
			$a = $data ["articulos"];
			$vencimientoOferta = intval ( $this->configuracion->variables ( "vencimientoOferta" ) ) * 86400;
			foreach ( $data ["articulos"] as $articulo ) {
				$x = array ();
				
				$imagen = array_shift ( explode ( ",", $articulo->foto ) );
				$imagen = imagenArticulo ( $articulo->usuario, $imagen, "thumb" );
				if ($imagen) {
					$x ["id"] = $articulo->id;
					$x ["titulo"] = $articulo->titulo;
					$x ["tipo"] = $articulo->tipo;
					$x ["furl"] = "product/" . $x ["id"] . "-" . normalizarTexto ( $x ["titulo"] );
					$x ["imagen"] = $imagen;
					list ( , $x ["height"] ) = getimagesize ( BASEPATH . "../$imagen" );
					$x ["precio"] = formato_moneda ( $articulo->precio ) . " &euro;";
					if ($x ["tipo"] == "Fijo") {
						$x ["cantidadOfertas"] = $articulo->cantidadOfertas . " " . traducir ( "ofertas" );
						$x ["cO"] = $articulo->cantidadOfertas;
						$x ["tiempo"] = calculaTiempoDiferencia ( date ( "Y-m-d H:i:s" ), strtotime ( $articulo->fecha_registro ) + $vencimientoOferta, true );
					} else {
						$x ["mayorPuja"] = formato_moneda ( $articulo->mayorPuja ) . " &euro;";
						$x ["cantidadPujas"] = $articulo->cantidadPujas . " " . traducir ( "pujas" );
						$x ["cP"] = $articulo->cantidadPujas;
						$x ["tiempo"] = calculaTiempoDiferencia ( date ( "Y-m-d H:i:s" ), strtotime ( $articulo->fecha_registro ) + $articulo->duracion * 86400, true );
					}
					$x ["pais_nombre"] = traducir ( "Ubicación:" ) . " " . $articulo->pais_nombre;
					$json [] = $x;
				}
			}
		}
		$this->output->set_output ( json_encode ( array (
				"articulos" => $json,
				"total" => $data ["total"],
				"final" => $data ["inicio"] + count ( $json ) 
		) ) );
	}
}

?>