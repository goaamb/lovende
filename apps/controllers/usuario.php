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
	public function cambiarTipoTarifa() {
		$tipo = $this->input->post ( "tipo" );
		$exito = false;
		if ($this->myuser && $tipo && $this->myuser->tipo_tarifa != $tipo) {
			$exito = $this->db->update ( "usuario", array (
					"nueva_tarifa" => $tipo 
			), array (
					"id" => $this->myuser->id 
			) );
		}
		$this->output->set_output ( json_encode ( array (
				"exito" => $exito 
		) ) );
	}
	public function perfil($usuario = false, $seccion = false) {
		$data = array ();
		$usuarioObj = $this->usuario->darUsuarioXSeudonimo ( $usuario );
		$header = array ();
		if ($usuarioObj) {
			if ($usuarioObj->estado !== "Baneado") {
				switch ($seccion) {
					case "sell" :
						$view = "usuario/ventas";
						break;
					case "self" :
						$view = "usuario/compras";
						break;
					case "billing" :
						if ($this->myuser) {
							$this->load->model ( "Paypal_model", "paypal" );
							$view = "usuario/mis-cuentas";
							$pagina = intval ( $this->configuracion->variables ( "cantidadPaginacion" ) );
							$inicio = 0;
							$data ["facturas"] = $this->articulo->darFacturas ( $this->myuser->id, $inicio, $pagina );
							$data ["total"] = $this->articulo->contarFacturas ( $this->myuser->id );
							$mes = date ( "m" );
							$anio = date ( "Y" );
							$bmes = "$mes-$anio";
							$estemes = false;
							if (count ( $data ["facturas"] ) > 0) {
								if ($bmes !== $data ["facturas"] [0]->mes) {
									$estemes = ($this->articulo->darDatosCuentas ( $mes, $anio, $this->myuser ));
								}
							} else {
								$estemes = ($this->articulo->darDatosCuentas ( $mes, $anio, $this->myuser ));
							}
							if ($estemes) {
								array_unshift ( $data ["facturas"], $estemes );
								$data ["total"] ++;
							}
							// var_dump($data);
						} else {
							redirect ( "login", 'refresh' );
						}
						break;
					case "messages" :
						
						if ($this->myuser->seudonimo == $usuario) {
							$id = $this->darIdUsuario ( $usuario );
							$data ['seudonimo'] = $usuario;
							$data ['mensaje'] = $this->cargarMensaje ( $id );
							$data ['cantidadmensaje'] = $this->darCantidadMensaje ( $id );
							$view = "usuario/mensajes";
						} else {
							redirect ( "store/$usuario", 'refresh' );
						}
						
						break;
					case "following" :
						if ($this->myuser) {
							$view = "usuario/seguimiento";
							$id = $this->darIdUsuario ( $usuario );
							
							$this->load->model ( 'articulo_model', 'articulo' );
							$data ['mensaje'] = $this->articulo->articulosSeguidos ( $pagina = 1, $criterio = false, $section = false, $orden = false, $ubicacion = false, $categoria = false, $id );
							
							$this->load->model ( "Siguiendo_model", "siguiendo" );
							$data ['totalseguir'] = $this->siguiendo->get_siguiendo_cantidad ( $id );
						} else {
							redirect ( "login", 'refresh' );
						}
						break;
					default :
						$view = "usuario/perfil";
						break;
				}
				$data ["complejo"] = true;
				$data ["section"] = "profile";
				$data ["profile"] = true;
			} else {
				if ($this->myuser->id == $usuarioObj->id) {
					$this->load->model ( "Paypal_model", "paypal" );
					$view = "usuario/mis-cuentas";
					$pagina = intval ( $this->configuracion->variables ( "cantidadPaginacion" ) );
					$inicio = 0;
					$data ["facturas"] = $this->articulo->darFacturas ( $this->myuser->id, $inicio, $pagina );
					$data ["total"] = $this->articulo->contarFacturas ( $this->myuser->id );
					$mes = date ( "m" );
					$anio = date ( "Y" );
					$bmes = "$mes-$anio";
					$estemes = false;
					if (count ( $data ["facturas"] ) > 0) {
						if ($bmes !== $data ["facturas"] [0]->mes) {
							$estemes = ($this->articulo->darDatosCuentas ( $mes, $anio, $this->myuser ));
						}
					} else {
						$estemes = ($this->articulo->darDatosCuentas ( $mes, $anio, $this->myuser ));
					}
					if ($estemes) {
						array_unshift ( $data ["facturas"], $estemes );
						$data ["total"] ++;
					}
				} else {
					$view = "usuario/baneado";
				}
			}
			$header ["headerTitle"] = "Lovende - Tienda de $usuarioObj->seudonimo";
			$header ["headerDescripcion"] = $usuarioObj->descripcion;
			$header ["headerKeywords"] = $this->configuracion->variables ( "defaultHeaderKeywords" );
			$header ["extraMeta"] = "<meta property='og:title' content='Tienda de $usuarioObj->seudonimo'/>";
			$header ["extraMeta"] .= "<meta property='og:description' content='$usuarioObj->descripcion'/>";
			$header ["extraMeta"] .= "<meta property='og:image' content='" . base_url () . imagenPerfil ( $usuarioObj ) . "' />";
		}
		
		$this->loadGUI ( $view, $data, $header );
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
			$ext = pathinfo ( $imagen, PATHINFO_EXTENSION );
			$name = pathinfo ( $imagen, PATHINFO_FILENAME );
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
				redirect ( "store/{$this->usuario->seudonimo}" );
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
			$new = false;
			$pending = false;
			$this->load->model ( "Articulo_model", "articulo" );
			switch ($lista) {
				case "sell" :
					$preview = true;
					$new = ($id == "new");
					$pending = ($id == "pending");
					if ($id == "detail" || $new || $id == "pending") {
						$preview = false;
					}
					$data ["preview"] = $preview;
					if ($this->myuser) {
						if ($preview) {
							$new = ($action == "new");
							$pending = ($action == "pending");
							$data = array_merge ( $this->articulo->leerArticulosVendidos ( $this->myuser->id, $this->input->get ( "pagina" ), $preview, $pending ), $data );
							$data = array_merge ( $this->articulo->leerArticulosEnVenta ( $this->myuser->id, $this->input->get ( "pagina" ), $preview, $new ), $data );
							$data = array_merge ( $this->articulo->leerArticulosNoVendidos ( $this->myuser->id, $this->input->get ( "pagina" ), $preview ), $data );
						} else {
							switch ($action) {
								case "1" :
									$data = array_merge ( $this->articulo->leerArticulosVendidos ( $this->myuser->id, $this->input->get ( "pagina" ), $preview, $pending ), $data );
									break;
								case "2" :
									$data = array_merge ( $this->articulo->leerArticulosEnVenta ( $this->myuser->id, $this->input->get ( "pagina" ), $preview, $new ), $data );
									break;
								case "3" :
									$data = array_merge ( $this->articulo->leerArticulosNoVendidos ( $this->myuser->id, $this->input->get ( "pagina" ), $preview ), $data );
									break;
							}
						}
					} else {
						redirect ( "login", "refresh" );
						return;
					}
					break;
				case "self" :
					$preview = true;
					$new = ($id == "new");
					$pending = ($id == "pending");
					if ($id == "detail" || $new || $id == "pending") {
						$preview = false;
					}
					$data ["preview"] = $preview;
					if ($this->myuser) {
						if ($preview) {
							$new = ($action == "new");
							$pending = ($action == "pending");
							$data = array_merge ( $this->articulo->leerArticulosComprados ( $this->myuser->id, $this->input->get ( "pagina" ), $preview, $pending ), $data );
							$data = array_merge ( $this->articulo->leerArticulosEnCompra ( $this->myuser->id, $this->input->get ( "pagina" ), $preview, $new ), $data );
							$data = array_merge ( $this->articulo->leerArticulosNoComprados ( $this->myuser->id, $this->input->get ( "pagina" ), $preview ), $data );
						} else {
							switch ($action) {
								case "1" :
									$data = array_merge ( $this->articulo->leerArticulosComprados ( $this->myuser->id, $this->input->get ( "pagina" ), $preview, $pending ), $data );
									break;
								case "2" :
									$data = array_merge ( $this->articulo->leerArticulosEnCompra ( $this->myuser->id, $this->input->get ( "pagina" ), $preview, $new ), $data );
									break;
								case "3" :
									$data = array_merge ( $this->articulo->leerArticulosNoComprados ( $this->myuser->id, $this->input->get ( "pagina" ), $preview ), $data );
									break;
							}
						}
					} else {
						redirect ( "login", "refresh" );
						return;
					}
					break;
				case "messages" :
					if ($this->myuser) {
						$data ["verlink"] = "no";
						$data = array_merge ( $this->articulo->leerMensajes ( $this->myuser->id, $this->input->get ( "pagina" ) ), $data );
					} else {
						redirect ( "login", "refresh" );
						return;
					}
					break;
				case "billing" :
					break;
				default :
					$pagina = intval ( $this->input->get ( "pagina" ) );
					$pagina = $pagina >= 1 ? $pagina : 1;
					$data = array_merge ( $this->articulo->leerArticulos ( $pagina, $this->input->get ( "criterio" ), $action, $this->input->get ( "orden" ), $this->input->get ( "ubicacion" ), $this->input->get ( "categoria" ), $this->idioma->language->id, $usuarioListar ), array (
							"orden" => $this->input->get ( "orden" ),
							"ubicacion" => $this->input->get ( "ubicacion" ),
							"categoria" => $this->input->get ( "categoria" ) 
					), $data );
					break;
			}
			$data ["seccion_nuevo"] = $new;
			$data ["seccion_pendiente"] = $pending;
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
							'field' => 'dni',
							'label' => 'DNI',
							'rules' => 'required' 
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
				$dataIn = array (
						"nombre" => $this->input->post ( "nombre" ),
						"dni" => $this->input->post ( "dni" ),
						"direccion" => $this->input->post ( "direccion" ),
						"codigo_postal" => $this->input->post ( "codigo_postal" ),
						"telefono" => $this->input->post ( "telefono" ),
						"pais" => $this->input->post ( "pais" ),
						"ciudad" => $this->input->post ( "ciudad" ),
						"paypal" => $this->input->post ( "paypal" ),
						"estado" => "Activo" 
				);
				if ($this->myuser->actualizarXCampos ( $dataIn )) {
					$this->myuser->nombre = $dataIn ["nombre"];
					$this->myuser->dni = $dataIn ["dni"];
					$this->myuser->direccion = $dataIn ["direccion"];
					$this->myuser->codigo_postal = $dataIn ["codigo_postal"];
					$this->myuser->telefono = $dataIn ["telefono"];
					$this->myuser->pais = $dataIn ["pais"];
					$this->myuser->ciudad = $dataIn ["ciudad"];
					$this->myuser->paypal = $dataIn ["paypal"];
					$this->myuser->estado = $dataIn ["estado"];
					
					$this->myuser->paypal = $this->input->post ( "paypal" );
					setcookie ( "datos_guardados", $this->myuser->id, false, '/' );
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
			if (count ( $errores ) == 0) {
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
							$this->myuser->seudonimo = $campos ["seudonimo"];
							$this->myuser->login ( $r );
						}
						if (isset ( $campos ["email"] )) {
							$this->myuser->email = $campos ["email"];
							$this->myemail->enviarTemplate ( $oemail, "Cambio de Correo electronico", "mail/emailchange_mail", array (
									"email" => $this->myuser->email 
							) );
						}
						if (isset ( $campos ["password"] )) {
							$this->myuser->password = $campos ["password"];
							$this->myemail->enviarTemplate ( $this->myuser->email, "Cambio de contraseña", "mail/passwordchange_mail", array (
									"password" => $nuevopassword,
									"usuario" => $this->myuser->seudonimo 
							) );
						}
						setcookie ( "datos_guardados", $this->myuser->id, false, "/" );
					}
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
		if (count ( $errores ) == 0) {
			setcookie ( "datos_guardados", $this->myuser->id, false, "/" );
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
					$errores ["error"] = " Le hemos enviado un email con los detalles de recuperación, por favor revise también su bandeja de no deseados.";
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
				$this->myuser = $this->usuario;
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
		$data ["totalVendidos"] = 0;
		$data ["finalVendidos"] = 0;
		$data = array_merge ( $this->articulo->leerArticulosVendidos ( $this->myuser->id, $inicio, false ), array (
				"section" => $section 
		) );
		$json = array ();
		$a = $data ["articulosVendidos"] ? $data ["articulosVendidos"] : false;
		if (isset ( $a ) && $a && is_array ( $a ) && count ( $a ) > 0) {
			$a = $data ["articulosVendidos"];
			$vencimientoOferta = intval ( $this->configuracion->variables ( "vencimientoOferta" ) ) * 86400;
			$gastos = 10;
			$grupo = false;
			foreach ( $data ["articulosVendidos"] as $i => $articulo ) {
				$gastos = $articulo->gastos_envio;
				$x = array ();
				$imagen = array_shift ( explode ( ",", $articulo->foto ) );
				$imagen = imagenArticulo ( $articulo->usuario, $imagen, "thumb" );
				if ($imagen) {
					if ($i < count ( $data ["articulosVendidos"] ) - 1 && $articulo->comprador->id == $data ["articulosVendidos"] [$i + 1]->comprador->id) {
						if (! $grupo) {
							$grupo = true;
						}
					} else {
						$grupo = false;
					}
					$x ["id"] = $articulo->id;
					$x ["titulo"] = $articulo->titulo;
					$x ["tipo"] = $articulo->tipo;
					$x ["furl"] = "product/" . $x ["id"] . "-" . normalizarTexto ( $x ["titulo"] );
					$x ["imagen"] = $imagen;
					list ( $x ["width"], $x ["height"] ) = getimagesize ( BASEPATH . "../$imagen" );
					$x ["estado1"] = ($articulo->estado == "Sin gastos Envio" ? '<a href="home/modal/anadir-gastos-envio/articulosComprados/' . $articulo->comprador->id . '" title="' . traducir ( "Añadir" ) . '" class="nmodal">' . traducir ( "Añadir" ) . '</a>' : (($articulo->estado == "Sin Pago") ? formato_moneda ( $gastos ) . " &euro;" : "<span class='green'>" . formato_moneda ( $gastos ) . " &euro;</span>"));
					if (! ($i > 0 && $articulo->comprador->id == $data ["articulosVendidos"] [$i - 1]->comprador->id)) {
						$totalMonto = 0;
					}
					if ($articulo->tipo == "Cantidad") {
						$totalMonto += $articulo->precio * $articulo->cantidad;
					} else {
						$totalMonto += $articulo->precio;
					}
					
					$x ["estado2"] = "--";
					$x ["estado3"] = "--";
					$x ["estado4"] = date ( "d-m-Y", strtotime ( $articulo->fecha_terminado ) );
					if (! ($i > 0 && $articulo->comprador->id == $data ["articulosVendidos"] [$i - 1]->comprador->id)) {
						$x ["estado4"] .= "<br /><a href='store/{$articulo->comprador->seudonimo}' title='{$articulo->comprador->seudonimo}'>{$articulo->comprador->seudonimo}</a>";
					}
					$x ["totalMonto"] = formato_moneda ( $articulo->tipo == "Fijo" || $articulo->tipo == "Cantidad" ? $articulo->precio : $articulo->mayorPuja ) . " €";
					if ((! $grupo || $i == count ( $data ["articulosVendidos"] ) - 1) && $gastos) {
						$x ["totalMonto"] .= "<br/>+" . formato_moneda ( $gastos ) . " €<br/>";
						$totalMonto += $articulo->gastos_envio;
					}
					if (! $grupo && $totalMonto != $articulo->precio) {
						$x ["totalMonto"] .= "<br /><strong>" . formato_moneda ( $totalMonto ) . " €</strong>";
					}
					if ($articulo->tipo == "Cantidad") {
						$x ["precio"] = formato_moneda ( $articulo->precio * $articulo->cantidad ) . " &euro;";
					} else {
						$x ["precio"] = formato_moneda ( $articulo->precio ) . " &euro;";
					}
					$x ["comprador"] = $articulo->comprador->seudonimo;
					$x ["compradorUrl"] = "store/" . $articulo->comprador->seudonimo . "/";
					$x ["estado"] = $articulo->estado;
					$x ["fecha_terminado"] = date ( "d-m-Y", strtotime ( $articulo->fecha_terminado ) );
					if ($x ["tipo"] == "Fijo") {
						$x ["cantidadOfertas"] = $articulo->cantidadOfertas . " " . traducir ( "ofertas" );
						$x ["cO"] = $articulo->cantidadOfertas;
						$x ["tiempo"] = calculaTiempoDiferencia ( date ( "Y-m-d H:i:s" ), strtotime ( $articulo->fecha_registro ) + $vencimientoOferta, true );
					} elseif ($x ["tipo"] == "Cantidad") {
						$x ["cantidad"] = traducir ( "Cantidad" ) . ": " . $articulo->cantidad;
						$x ["cO"] = $articulo->cantidadOfertas;
						$x ["tiempo"] = calculaTiempoDiferencia ( date ( "Y-m-d H:i:s" ), strtotime ( $articulo->fecha_registro ) + $vencimientoOferta, true );
					} else {
						$x ["mayorPuja"] = formato_moneda ( $articulo->mayorPuja ) . " &euro;";
						$x ["cantidadPujas"] = $articulo->cantidadPujas . " " . traducir ( "pujas" );
						$x ["cP"] = $articulo->cantidadPujas;
						$x ["tiempo"] = calculaTiempoDiferencia ( date ( "Y-m-d H:i:s" ), strtotime ( $articulo->fecha_registro ) + $articulo->duracion * 86400, true );
					}
					$json [] = $x;
				}
			}
		}
		$this->output->set_output ( json_encode ( array (
				"articulos" => $json,
				"totalVendidos" => $data ["totalVendidos"],
				"finalVendidos" => $data ["inicio"] + count ( $json ) 
		) ) );
	}
	public function verMasArticulosComprados() {
		$this->load->model ( "Articulo_model", "articulo" );
		$inicio = $this->input->post ( "inicio" );
		$section = $this->input->post ( "section" );
		$data ["totalComprados"] = 0;
		$data ["finalComprados"] = 0;
		$data = array_merge ( $this->articulo->leerArticulosComprados ( $this->myuser->id, $inicio, false ), array (
				"section" => $section 
		) );
		$json = array ();
		$a = $data ["articulosComprados"] ? $data ["articulosComprados"] : false;
		if (isset ( $a ) && $a && is_array ( $a ) && count ( $a ) > 0) {
			$a = $data ["articulosComprados"];
			$vencimientoOferta = intval ( $this->configuracion->variables ( "vencimientoOferta" ) ) * 86400;
			$gastos = 10;
			$grupo = false;
			foreach ( $data ["articulosComprados"] as $i => $articulo ) {
				$x = array ();
				$imagen = array_shift ( explode ( ",", $articulo->foto ) );
				$imagen = imagenArticulo ( $articulo->usuario, $imagen, "thumb" );
				if ($imagen) {
					if ($i < count ( $data ["articulosComprados"] ) - 1 && $articulo->comprador->id == $data ["articulosComprados"] [$i + 1]->comprador->id) {
						if (! $grupo) {
							$grupo = true;
						}
					} else {
						$grupo = false;
					}
					$x ["id"] = $articulo->id;
					$x ["titulo"] = $articulo->titulo;
					$x ["tipo"] = $articulo->tipo;
					$x ["furl"] = "product/" . $x ["id"] . "-" . normalizarTexto ( $x ["titulo"] );
					$x ["imagen"] = $imagen;
					list ( $x ["width"], $x ["height"] ) = getimagesize ( BASEPATH . "../$imagen" );
					$x ["estado1"] = ($articulo->estado == "Sin gastos Envio" ? traducir ( "Esperando" ) : (($articulo->estado == "Sin Pago") ? $gastos : "<span class='green'>$gastos</span>"));
					if (! ($i > 0 && $articulo->usuario->id == $data ["articulosComprados"] [$i - 1]->usuario->id)) {
						$totalMonto = 0;
					}
					$totalMonto += $articulo->precio;
					$x ["estado2"] = "--";
					$x ["estado3"] = "--";
					$x ["estado4"] = date ( "d-m-Y", strtotime ( $articulo->fecha_terminado ) );
					if (! ($i > 0 && $articulo->usuario->id == $data ["articulosComprados"] [$i - 1]->usuario->id)) {
						$x ["estado4"] .= "<br /><a href='store/{$articulo->usuario->seudonimo}' title='{$articulo->usuario->seudonimo}'>{$articulo->usuario->seudonimo}</a>";
					}
					$x ["totalMonto"] = formato_moneda ( $articulo->tipo == "Fijo" ? $articulo->precio : $articulo->mayorPuja ) . " €";
					if (! $grupo && $totalMonto != $articulo->precio) {
						$x ["totalMonto"] .= "<br /><strong>" . formato_moneda ( $totalMonto ) . " €</strong>";
					}
					
					$x ["precio"] = formato_moneda ( $articulo->precio ) . " &euro;";
					$x ["comprador"] = $articulo->usuario->seudonimo;
					$x ["compradorUrl"] = "store/" . $articulo->usuario->seudonimo . "/";
					$x ["estado"] = $articulo->estado;
					$x ["fecha_terminado"] = date ( "d-m-Y", strtotime ( $articulo->fecha_terminado ) );
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
					$json [] = $x;
				}
			}
		}
		$this->output->set_output ( json_encode ( array (
				"articulos" => $json,
				"totalComprados" => $data ["totalComprados"],
				"finalComprados" => $data ["inicio"] + count ( $json ) 
		) ) );
	}
	public function verMasArticulosEnCompra() {
		$this->load->model ( "Articulo_model", "articulo" );
		$inicio = $this->input->post ( "inicio" );
		$section = $this->input->post ( "section" );
		$data ["totalEnCompra"] = 0;
		$data ["finalEnCompra"] = 0;
		$data = array_merge ( $this->articulo->leerArticulosEnCompra ( $this->myuser->id, $inicio, false ), array (
				"section" => $section 
		) );
		
		$json = array ();
		$a = $data ["articulosEnCompra"] ? $data ["articulosEnCompra"] : false;
		if (isset ( $a ) && $a && is_array ( $a ) && count ( $a ) > 0) {
			$a = $data ["articulosEnCompra"];
			$vencimientoOferta = intval ( $this->configuracion->variables ( "vencimientoOferta" ) ) * 86400;
			$gastos = 10;
			$grupo = false;
			foreach ( $data ["articulosEnCompra"] as $i => $articulo ) {
				$x = array ();
				$imagen = array_shift ( explode ( ",", $articulo->foto ) );
				$imagen = imagenArticulo ( $articulo->usuario, $imagen, "thumb" );
				if ($imagen) {
					$x ["id"] = $articulo->id;
					$x ["titulo"] = $articulo->titulo;
					$x ["tipo"] = $articulo->tipo;
					$x ["furl"] = "product/" . $x ["id"] . "-" . normalizarTexto ( $x ["titulo"] );
					$x ["imagen"] = $imagen;
					list ( $x ["width"], $x ["height"] ) = getimagesize ( BASEPATH . "../$imagen" );
					$x ["precio"] = formato_moneda ( $articulo->precio ) . " &euro;";
					$x ["estadoArticulo"] = ($articulo->tipo == "Fijo" ? ($articulo->estadoOferta == "Pendiente" ? traducir ( "Oferta enviada" ) : "<span class='red'>" . traducir ( "Oferta rechazada" ) . "</span>") : ($articulo->maximoPujador == $this->myuser->id ? "<span class='green'>" . traducir ( "Máximo pujador" ) . "</span>" : "<span class='red'>" . traducir ( "Sobrepujado" ) . "</span>"));
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
					$json [] = $x;
				}
			}
		}
		$this->output->set_output ( json_encode ( array (
				"articulos" => $json,
				"totalEnCompra" => $data ["totalEnCompra"],
				"finalEnCompra" => $data ["inicio"] + count ( $json ),
				"countEnCompra" => count ( $data ["articulosEnCompra"] ) 
		) ) );
	}
	public function verMasArticulosEnVenta() {
		$this->load->model ( "Articulo_model", "articulo" );
		$inicio = $this->input->post ( "inicio" );
		$section = $this->input->post ( "section" );
		$data ["totalEnVenta"] = 0;
		$data ["finalEnVenta"] = 0;
		$data = array_merge ( $this->articulo->leerArticulosEnVenta ( $this->myuser->id, $inicio, false ), array (
				"section" => $section 
		) );
		
		$json = array ();
		$a = $data ["articulosEnVenta"] ? $data ["articulosEnVenta"] : false;
		if (isset ( $a ) && $a && is_array ( $a ) && count ( $a ) > 0) {
			$a = $data ["articulosEnVenta"];
			$vencimientoOferta = intval ( $this->configuracion->variables ( "vencimientoOferta" ) ) * 86400;
			$gastos = 10;
			$grupo = false;
			foreach ( $data ["articulosEnVenta"] as $i => $articulo ) {
				$x = array ();
				$imagen = array_shift ( explode ( ",", $articulo->foto ) );
				$imagen = imagenArticulo ( $articulo->usuario, $imagen, "thumb" );
				if ($imagen) {
					$x ["id"] = $articulo->id;
					$x ["titulo"] = $articulo->titulo;
					$x ["tipo"] = $articulo->tipo;
					$x ["furl"] = "product/" . $x ["id"] . "-" . normalizarTexto ( $x ["titulo"] );
					$x ["imagen"] = $imagen;
					$x ["seguidores"] = $articulo->seguidores;
					list ( $x ["width"], $x ["height"] ) = getimagesize ( BASEPATH . "../$imagen" );
					$x ["precio"] = formato_moneda ( $articulo->precio ) . " &euro;";
					if ($x ["tipo"] == "Fijo") {
						$x ["cantidadOfertas"] = $articulo->cantidadOfertas . " " . traducir ( "ofertas" );
						if ($articulo->ofertasPendientes > "0") {
							$x ["cantidadOfertas"] .= "<br/><a href='articulo/modal/ofertas/ofertas/$articulo->id' class='nmodal'>" . traducir ( "Nueva oferta" ) . "</a>";
						}
						$x ["cO"] = $articulo->cantidadOfertas;
						$x ["tiempo"] = calculaTiempoDiferencia ( date ( "Y-m-d H:i:s" ), strtotime ( $articulo->fecha_registro ) + $vencimientoOferta, true );
					} elseif ($x ["tipo"] == "Cantidad") {
						$x ["cC"] = $articulo->cantidad;
						$x ["cantidad"] = $articulo->cantidad . " " . traducir ( "unidades" );
						$x ["textoOferta"] = $articulo->cantidad . " " . traducir ( "unidades" ) . "<br/><span class='italic'>" . traducir ( "¡Cómpralo" ) . "</span> <span class='red italic'>" . traducir ( "ya!" ) . "</span>";
						$x ["tiempo"] = calculaTiempoDiferencia ( date ( "Y-m-d H:i:s" ), strtotime ( $articulo->fecha_registro ) + $vencimientoOferta, true );
					} else {
						$x ["mayorPuja"] = formato_moneda ( $articulo->mayorPuja ) . " &euro;";
						$x ["cantidadPujas"] = $articulo->cantidadPujas . " " . traducir ( "pujas" );
						if ($articulo->ofertasPendientes > "0") {
							$x ["cantidadPujas"] .= "<br/><a href='articulo/modal/pujas/ofertas/$articulo->id' class='nmodal'>" . traducir ( "Nueva puja" ) . "</a>";
						}
						$x ["cP"] = $articulo->cantidadPujas;
						$x ["tiempo"] = calculaTiempoDiferencia ( date ( "Y-m-d H:i:s" ), strtotime ( $articulo->fecha_registro ) + $articulo->duracion * 86400, true );
					}
					$json [] = $x;
				}
			}
		}
		$this->output->set_output ( json_encode ( array (
				"articulos" => $json,
				"totalEnVenta" => $data ["totalEnVenta"],
				"finalEnVenta" => $data ["inicio"] + count ( $json ),
				"countEnVenta" => count ( $data ["articulosEnVenta"] ) 
		) ) );
	}
	public function verMasArticulosNoComprados() {
		$this->load->model ( "Articulo_model", "articulo" );
		$inicio = $this->input->post ( "inicio" );
		$section = $this->input->post ( "section" );
		$data ["totalNoComprados"] = 0;
		$data ["finalNoComprados"] = 0;
		$data = array_merge ( $this->articulo->leerArticulosNoComprados ( $this->myuser->id, $inicio, false ), array (
				"section" => $section 
		) );
		
		$json = array ();
		$a = $data ["articulosNoComprados"] ? $data ["articulosNoComprados"] : false;
		if (isset ( $a ) && $a && is_array ( $a ) && count ( $a ) > 0) {
			$a = $data ["articulosNoComprados"];
			$vencimientoOferta = intval ( $this->configuracion->variables ( "vencimientoOferta" ) ) * 86400;
			$gastos = 10;
			$grupo = false;
			foreach ( $data ["articulosNoComprados"] as $i => $articulo ) {
				$x = array ();
				$imagen = array_shift ( explode ( ",", $articulo->foto ) );
				$imagen = imagenArticulo ( $articulo->usuario, $imagen, "thumb" );
				if ($imagen) {
					$x ["id"] = $articulo->id;
					$x ["titulo"] = $articulo->titulo;
					$x ["furl"] = "product/" . $x ["id"] . "-" . normalizarTexto ( $x ["titulo"] );
					$x ["imagen"] = $imagen;
					$x ["fecha_terminado"] = date ( "d-m-Y", strtotime ( $articulo->fecha_terminado ) );
					list ( $x ["width"], $x ["height"] ) = getimagesize ( BASEPATH . "../$imagen" );
					$x ["precio"] = formato_moneda ( $articulo->precio ) . " &euro;";
					$json [] = $x;
				}
			}
		}
		$this->output->set_output ( json_encode ( array (
				"articulos" => $json,
				"totalNoComprados" => $data ["totalNoComprados"],
				"finalNoComprados" => $data ["inicio"] + count ( $json ),
				"countNoComprados" => count ( $data ["articulosNoComprados"] ) 
		) ) );
	}
	public function verMasArticulosNoVendidos() {
		$this->load->model ( "Articulo_model", "articulo" );
		$inicio = $this->input->post ( "inicio" );
		$section = $this->input->post ( "section" );
		$data ["totalNoVendidos"] = 0;
		$data ["finalNoVendidos"] = 0;
		$data = array_merge ( $this->articulo->leerArticulosNoVendidos ( $this->myuser->id, $inicio, false ), array (
				"section" => $section 
		) );
		
		$json = array ();
		$a = $data ["articulosNoVendidos"] ? $data ["articulosNoVendidos"] : false;
		if (isset ( $a ) && $a && is_array ( $a ) && count ( $a ) > 0) {
			$a = $data ["articulosNoVendidos"];
			$vencimientoOferta = intval ( $this->configuracion->variables ( "vencimientoOferta" ) ) * 86400;
			$gastos = 10;
			$grupo = false;
			foreach ( $data ["articulosNoVendidos"] as $i => $articulo ) {
				$x = array ();
				$imagen = array_shift ( explode ( ",", $articulo->foto ) );
				$imagen = imagenArticulo ( $articulo->usuario, $imagen, "thumb" );
				if ($imagen) {
					$x ["id"] = $articulo->id;
					$x ["titulo"] = $articulo->titulo;
					$x ["tipo"] = $articulo->tipo == "Fijo" ? traducir ( "Precio Fijo" ) : traducir ( "Subasta" );
					$x ["furl"] = "product/" . $x ["id"] . "-" . normalizarTexto ( $x ["titulo"] );
					$x ["purl"] = "<br><a href='product/begin/$articulo->id'>" . traducir ( "Poner a la venta" ) . "</a>";
					$x ["imagen"] = $imagen;
					$x ["fecha_terminado"] = date ( "d-m-Y", strtotime ( $articulo->fecha_terminado ) );
					list ( $x ["width"], $x ["height"] ) = getimagesize ( BASEPATH . "../$imagen" );
					$x ["precio"] = formato_moneda ( $articulo->precio ) . " &euro;";
					$json [] = $x;
				}
			}
		}
		$this->output->set_output ( json_encode ( array (
				"articulos" => $json,
				"totalNoVendidos" => $data ["totalNoVendidos"],
				"finalNoVendidos" => $data ["inicio"] + count ( $json ),
				"countNoVendidos" => count ( $data ["articulosNoVendidos"] ) 
		) ) );
	}
	
	// editado
	public function perfilMensaje($usuario = false, $emisor = false, $seccion = false) {
		if ($this->myuser) {
			$data = array ();
			switch ($seccion) {
				case "unread" :
					$id = $this->darIdUsuario ( $usuario );
					$data ['verlink'] = "mensaje";
					$data ['seudonimo'] = $usuario;
					$data ['mensaje'] = $this->cargarMensaje ( $id, 'Pendiente' );
					$data ['cantidadmensaje'] = $this->darCantidadMensaje ( $id );
					$view = "usuario/mensajes";
					breaK;
				case "markunread" :
					$view = "usuario/mensajes";
					break;
				case "report" :
					$view = "usuario/mensajes";
					break;
				case "inbox" :
					$id = $this->darIdUsuario ( $usuario );
					$data ['seudonimo'] = $usuario;
					$data ['seudonimoemisor'] = $this->darSeudonimo ( $emisor );
					$data ['mensaje'] = $this->cargarMensaje ( $id, false, $emisor );
					$data ['cantidadmensaje'] = $this->darCantidadMensaje ( $id, $emisor );
					$this->cambiarEstadoMensaje ( $id, $emisor );
					
					$view = "usuario/profilemensaje";
					break;
				case "inboxadmin" :
					$id = $this->darIdUsuario ( $usuario );
					$data ['seudonimo'] = $usuario;
					$data ['seudonimoemisor'] = "LOVENDE";
					$data ['mensaje'] = $this->cargarmensajeadministrador ( $id );
					$data ['cantidadmensaje'] = count ( $data ['mensaje'] );
					
					$this->cambiarEstadoMensaje ( $id, NULL );
					if ($emisor == "ADMINLOVENDE") {
						$view = "administrador/hiloadmin";
					} else {
						$view = "administrador/hilomensaje";
					}
					break;
				case "admin" :
					$idmensaje = $usuario;
					$idusuario = $emisor;
					$data ['mensaje'] = $this->cargarmensajeadmin ( $idmensaje, $idusuario );
					
					$view = "administrador/profilemensaje";
					break;
				default :
					$view = "usuario/perfil";
					break;
			}
			
			$data ["complejo"] = true;
			$data ["section"] = "profile";
			$data ["profile"] = true;
			$this->loadGUI ( $view, $data );
		} else {
			redirect ( "login", "refresh" );
			return;
		}
	}
	public function darSeudonimo($id) {
		$seudonimoreceptor = '';
		$resultado = $this->usuario->darSeudonimo ( $id );
		
		foreach ( $resultado as $row ) {
			$seudonimoreceptor = $row->seudonimo;
		}
		return $seudonimoreceptor;
	}
	public function darIdUsuario($usuario) {
		$this->load->model ( 'usuario_model', 'objusuario' );
		$idusuario = $this->objusuario->darUsuarioId ( $usuario );
		
		$id = 0;
		if (! empty ( $idusuario )) {
			foreach ( $idusuario as $rowusuario ) {
				$id = $rowusuario->id;
			}
		}
		return $id;
	}
	public function cargarMensaje($id, $estado = false, $emisor = false) {
		$this->load->model ( 'usuario_mensaje', 'objeto' );
		$mensajetotal = $this->objeto->get_mensaje ( $id, false, false, $estado, $emisor );
		// var_dump($mensajetotal);
		
		$data = array ();
		$data2 = array ();
		
		$bandera = false;
		// $ver=-1;
		// $copiar = true;
		$sum = 0;
		
		if (! empty ( $mensajetotal )) {
			
			$existe = array ();
			// $existe[0] = -1;
			$bandera = false;
			$cont = 0;
			foreach ( $mensajetotal as $row ) {
				if ($row->seudonimo != 'ADMIN-LOVENDE') {
					// tipomensaje
					if ($row->tipomensaje != 'Admin') {
						
						if ($id == $row->emisor) {
							for($i = 0; $i <= count ( $existe ) - 1; $i ++) {
								if ($existe [$i] == $row->receptor) {
									$bandera = true;
								}
							}
							if ($bandera == false) {
								$existe [] = $row->receptor;
							}
							// $bandera=false;
						} else {
							if ($id == $row->receptor) {
								for($i = 0; $i <= count ( $existe ) - 1; $i ++) {
									if ($existe [$i] == $row->emisor) {
										$bandera = true;
									}
								}
								if ($bandera == false) {
									$existe [] = $row->emisor;
								}
							}
						}
						// print_r($existe);
						if ($emisor != false) {
							$bandera = false;
						}
						if ($bandera == false) {
							$sum ++;
							
							if ($emisor != false) {
								$nuevacadena = str_replace ( "<br />", "[salto]", $row->mensaje );
								$nuevacadena2 = parse_text_html ( $nuevacadena );
								$data ['mensaje'] = str_replace ( "[salto]", "<br />", $nuevacadena2 ); // 0
							} else {
								
								$maximo = 50;
								$cadena = explode ( "<br />", $row->mensaje );
								$cortar = $maximo - 3;
								$cadenanueva = $cadena [0];
								if (strlen ( $cadenanueva ) > $maximo) {
									$cadenanueva = substr ( $cadena [0], 0, $cortar ) . "...";
								}
								$data ['mensaje'] = ($cadenanueva); // 0
							}
							
							$data ['idarticulo'] = $row->articulo; // 3
							
							$this->load->model ( 'usuario_model', 'objusuario' );
							
							// $usuario =
							// $this->usuario_model->darUsuarioXId($id);
							if ($emisor == false) {
								$tam = "";
							} else {
								if ($cont == 0) {
									$tam = "";
								} else {
									$tam = "small";
								}
							}
							
							if ($row->articulo) {
								$this->load->model ( 'articulo_model', 'objarticulo' );
								$articulo = $this->objarticulo->darArticulo ( $row->articulo );
								// print_r($articulo);
								
								$data ['nomarticulo'] = $articulo->titulo; // 5
							} else {
								$data ['nomarticulo'] = '';
							}
							
							$data ['tiempo'] = $this->tiemporestante ( $row->anios, $row->meses, $row->semanas, $row->dias, $row->horas, $row->minutos, $row->segundos );
							
							$cadenadireccion = '';
							$cadenaseu = '';
							
							if ($emisor == false) {
								if ($row->emisor == $id) {
									$data ['receptor'] = $row->emisor;
									$cadenaseu = $row->emisor;
									$data ['emisor'] = $row->receptor;
									$cadenadireccion = $row->receptor;
									$data ['seudonimo'] = $this->darSeudonimo ( $row->receptor );
									
									$idusuario = $this->objusuario->darUsuarioXId ( $row->receptor );
									$data ['estado'] = $row->estado_receptor; // 1
									
									$data ['estadousuario'] = $row->estadousuario2;
								} else {
									$data ['emisor'] = $row->emisor; // 13
									$cadenadireccion = $row->emisor;
									$data ['receptor'] = $row->receptor;
									$cadenaseu = $row->receptor;
									$data ['seudonimo'] = $row->seudonimo; // 2
									$idusuario = $this->objusuario->darUsuarioXId ( $row->emisor );
									$data ['estado'] = $row->estado; // 1
									
									$data ['estadousuario'] = $row->estadousuario;
								}
							} else {
								$data ['emisor'] = $row->emisor; // 13
								$cadenadireccion = $row->emisor;
								$data ['receptor'] = $row->receptor;
								$cadenaseu = $row->receptor;
								$data ['seudonimo'] = $row->seudonimo; // 2
								$idusuario = $this->objusuario->darUsuarioXId ( $row->emisor );
								$data ['estado'] = $row->estado; // 1
								
								$data ['estadousuario'] = $row->estadousuario;
							}
							
							$imagen = imagenPerfil ( $idusuario, $tam );
							$data ['dirimagen'] = $imagen; // 4
							
							$data ['id'] = $row->id; // 14
							
							$data ['valores'] = "$cadenadireccion-$cadenaseu-$row->id";
							
							$cadenaseu = $this->darSeudonimo ( $cadenaseu );
							$data ['direccion'] = "messageprofile/$cadenaseu/$cadenadireccion/inbox";
							
							if (! $estado) {
								$data ['incremento'] = $sum;
							}
							
							$data2 [] = $data;
							$data = array ();
							$cont ++;
						}
						$bandera = false;
					} else { // tipo de mensaje ADMIN
						$rowemisor = - 1;
						if ($row->emisor) {
							$rowemisor = $row->emisor;
						}
						
						$rowreceptor = - 1;
						if ($row->receptor) {
							$rowreceptor = $row->receptor;
						}
						
						if ($id == $rowemisor) {
							for($i = 0; $i <= count ( $existe ) - 1; $i ++) {
								if ($existe [$i] == $rowreceptor) {
									$bandera = true;
								}
							}
							if ($bandera == false) {
								$existe [] = $rowreceptor;
							}
							// $bandera=false;
						} else {
							if ($id == $rowreceptor) {
								for($i = 0; $i <= count ( $existe ) - 1; $i ++) {
									if ($existe [$i] == $rowemisor) {
										$bandera = true;
									}
								}
								if ($bandera == false) {
									$existe [] = $rowemisor;
								}
							}
						}
						// print_r($existe);
						if ($emisor != false) {
							$bandera = false;
						}
						if ($bandera == false) {
							$sum ++;
							
							$maximo = 50;
							$cadena = explode ( "<br />", $row->mensaje );
							$cortar = $maximo - 3;
							$cadenanueva = $cadena [0];
							if (strlen ( $cadenanueva ) > $maximo) {
								$cadenanueva = substr ( $cadena [0], 0, $cortar ) . "...";
							}
							$data ['mensaje'] = parse_text_html ( $cadenanueva ); // 0
							
							$data ['idarticulo'] = $row->articulo; // 3
							
							$this->load->model ( 'usuario_model', 'objusuario' );
							
							// $usuario =
							// $this->usuario_model->darUsuarioXId($id);
							if ($emisor == false) {
								$tam = "";
							} else {
								if ($cont == 0) {
									$tam = "";
								} else {
									$tam = "small";
								}
							}
							
							if ($row->articulo) {
								$this->load->model ( 'articulo_model', 'objarticulo' );
								$articulo = $this->objarticulo->darArticulo ( $row->articulo );
								// print_r($articulo);
								
								$data ['nomarticulo'] = $articulo->titulo; // 5
							} else {
								$data ['nomarticulo'] = '';
							}
							
							$data ['tiempo'] = $this->tiemporestante ( $row->anios, $row->meses, $row->semanas, $row->dias, $row->horas, $row->minutos, $row->segundos );
							
							$cadenadireccion = '';
							$cadenaseu = '';
							
							if ($emisor == false) {
								if ($rowemisor == $id) {
									$data ['receptor'] = $rowemisor;
									$cadenaseu = $rowemisor;
									$data ['emisor'] = $rowreceptor;
									$cadenadireccion = $rowreceptor;
									$data ['seudonimo'] = "LOVENDE";
									
									$idusuario = - 1;
									$data ['estado'] = $row->estado_receptor; // 1
									
									$data ['estadousuario'] = $row->estadousuario2;
								} else {
									$data ['emisor'] = $rowemisor; // 13
									$cadenadireccion = $rowemisor;
									$data ['receptor'] = $rowreceptor;
									$cadenaseu = $rowreceptor;
									$data ['seudonimo'] = "LOVENDE"; // 2
									$idusuario = - 1;
									$data ['estado'] = $row->estado; // 1
									
									$data ['estadousuario'] = $row->estadousuario;
								}
							} else {
								$data ['emisor'] = $rowemisor; // 13
								$cadenadireccion = $rowemisor;
								$data ['receptor'] = $rowreceptor;
								$cadenaseu = $rowreceptor;
								$data ['seudonimo'] = "LOVENDE"; // 2
								$idusuario = - 1;
								$data ['estado'] = $row->estado; // 1
								
								$data ['estadousuario'] = $row->estadousuario;
							}
							
							// $imagen = imagenPerfil ( $id, $tam );
							$data ['dirimagen'] = "assets/images/html/profile-image.png"; // 4
							
							$data ['id'] = $row->id; // 14
							
							$data ['valores'] = "LOVENDE-$cadenaseu-$row->id";
							
							$cadenaseu = "LOVENDE";
							$cadenaseu2 = $this->darSeudonimo ( $id );
							$data ['direccion'] = "messageprofile/$cadenaseu2/$cadenaseu/inboxadmin";
							
							$data ['estadousuario'] = $row->estadousuario;
							
							if (! $estado) {
								$data ['incremento'] = $sum;
							}
							
							$data2 [] = $data;
							$data = array ();
							$cont ++;
						}
						$bandera = false;
					}
				} else {
					// tipo de mensaje notificacion
					$resultados22 = $this->objeto->verestadonotificacion ( $row->id, $id );
					$banderaadmi = false;
					if ($resultados22) {
						foreach ( $resultados22 as $algo ) {
							// 0 = pendiente, 1=leido, 2=eliminado
							if ($algo->estado == 2) {
								$banderaadmi = true;
							} else {
								$banderaadmi = false;
								if ($algo->estado == 0) {
									$data ['estado'] = 'Pendiente';
								} else {
									$data ['estado'] = 'Leido';
								}
							}
							// modificar
						}
					} else {
						$banderaadmi = false;
						// guardar
						$this->objeto->guardardetallenotificacion ( $row->id, $id );
						$data ['estado'] = 'Pendiente';
					}
					
					if ($banderaadmi == false) {
						$sum ++;
						
						$documento = explode ( '<p style="margin: 0 0 15px">', $row->mensaje );
						
						if (isset ( $documento [1] )) {
							$documento = explode ( '</p>', $documento [1] );
						} else {
							
							$documento = explode ( 'Anuncio', $row->mensaje );
							if (isset ( $documento [1] )) {
								$documento = explode ( '</h1>', $documento [1] );
								$documento [0] = "Anuncio" . $documento [0];
							} else {
								$auxd = explode ( "<br />", parse_text_html ( $documento [0] ) );
								do {
									$daux = array_shift ( $auxd );
								} while ( count ( $auxd ) >= 0 && (trim ( $daux ) === "" || trim ( $daux ) === "Lovende") );
								$documento [0] = substr ( $daux, 0, 150 ) . "...";
							}
							// $documento = $documento[0];
						}
						
						$data ['mensaje'] = $documento [0];
						
						$data ['valores'] = "ADMIN-$id-$row->id";
						// $data['mensaje'] = $cadena;
						$data ['idarticulo'] = '';
						$data ['nomarticulo'] = '';
						$data ['tiempo'] = $this->tiemporestante ( $row->anios, $row->meses, $row->semanas, $row->dias, $row->horas, $row->minutos, $row->segundos );
						$data ['receptor'] = '';
						$data ['emisor'] = '';
						$data ['seudonimo'] = $row->seudonimo;
						
						$data ['dirimagen'] = 'assets/images/html/profile-image.png';
						$data ['id'] = $row->id;
						$data ['direccion'] = "messageprofile/$row->id/$id/admin";
						
						$data ['estadousuario'] = $row->estadousuario;
						
						if (! $estado) {
							$data ['incremento'] = $sum;
						}
						$data2 [] = $data;
						$data = array ();
						$cont ++;
					}
				}
			}
		}
		// echo 'fsfsdf'.$estado;
		if ((! $estado) && (count ( $data ) > 0)) {
			$data2 [0] ['incremento'] = $sum;
		}
		
		// $data2['id'] = $sum;
		// print_r($data2);
		return $data2;
	}
	public function darCantidadMensaje($id, $idemisor = false) {
		$this->load->model ( 'usuario_mensaje', 'objeto' );
		$mensajetotal = $this->objeto->get_mensaje_cantidad ( $id, $idemisor );
		return $mensajetotal;
		// get_mensaje_cantidad
	}
	public function darCantidadLista($id, $estado = false) {
		$this->load->model ( 'usuario_mensaje', 'objeto' );
		$mensajetotal = $this->objeto->get_cantidad_listas ( $id, $estado );
		return $mensajetotal;
	}
	public function cambiarEstadoMensaje($receptor, $emisor = null) {
		$datosmodificar = array (
				'estado' => 'Leido' 
		);
		
		$this->load->model ( 'usuario_mensaje', 'objeto' );
		$this->objeto->cambiar_estado ( $receptor, $emisor, $datosmodificar );
		
		$datosmodificar = array (
				'estado_receptor' => 'Leido' 
		);
		
		$this->objeto->cambiar_estado ( $emisor, $receptor, $datosmodificar );
	}
	public function eliminarMensaje() {
		if ($this->myuser) {
			if ($this->input->get ( 'id' )) {
				$mensaje = $this->input->get ( 'id' );
				
				$cant = 0;
				$cant = substr_count ( $mensaje, "," );
				
				for($i = 0; $i <= $cant; $i ++) {
					$tal = explode ( ",", $mensaje );
					$ids = explode ( "-", $tal [$i] );
					
					$emisor = $ids [0];
					
					if ($emisor != "ADMIN") {
						if ($emisor != "LOVENDE") {
							$receptor = explode ( "-", $ids [1] );
							$receptor = $receptor [0];
							
							$visibilidad = '';
							
							$this->load->model ( 'usuario_mensaje', 'objeto' );
							$visibilidad2 = $this->objeto->devolver_estado ( $receptor, $emisor );
							
							// print_r($visibilidad);
							if ($visibilidad2) {
								foreach ( $visibilidad2 as $row ) {
									$visibilidad = $row->visible;
								}
								
								if ($visibilidad == 0) {
									$visibilidad = $emisor;
								}
								
								if ($visibilidad == $receptor) {
									$visibilidad = 2;
								}
							} else {
								$visibilidad2 = $this->objeto->devolver_estado ( $emisor, $receptor );
								if ($visibilidad2) {
									foreach ( $visibilidad2 as $row ) {
										$visibilidad = $row->visible;
									}
									
									if ($visibilidad == 0) {
										$visibilidad = $emisor;
									}
									
									if ($visibilidad == $receptor) {
										$visibilidad = 2;
									}
								}
							}
							
							$datosmodificar = array (
									'visible' => $visibilidad 
							);
							
							// print_r($datosmodificar);
							
							$this->objeto->cambiar_estado2 ( $receptor, $emisor, $datosmodificar );
						} else {
							
							$cadena159 = explode ( "-", $ids [1] );
							$idusuario = $cadena159 [0];
							$visibilidad = 1;
							$datosmodificar = array (
									'visible' => $visibilidad 
							);
							$this->load->model ( 'usuario_mensaje', 'objeto' );
							$this->objeto->cambiar_estado2 ( $idusuario, false, $datosmodificar, true );
						}
					} else {
						$cadena159 = explode ( "-", $ids [1] );
						$idusuario = $cadena159 [0];
						$cadena159 = explode ( "-", $ids [2] );
						$idnotificacion = $cadena159 [0];
						$this->load->model ( 'usuario_mensaje', 'objeto' );
						$datosmodificar = array (
								'visible' => 2 
						);
						
						$this->objeto->modificarestadoadmin ( $idnotificacion, $idusuario, $datosmodificar );
					}
				}
				echo 1;
			} else {
				echo 2;
			}
		} else {
			echo 3;
		}
	}
	public function eliminarMensajeBD() {
		if ($this->myuser) {
			if ($this->input->get ( 'id' )) {
				$idmensaje = $this->input->get ( 'id' );
				$this->load->model ( 'usuario_mensaje', 'objeto' );
				$this->objeto->eliminar_mensaje ( $idmensaje );
				echo 1;
			} else {
				echo 2;
			}
		} else {
			echo 3;
		}
	}
	public function guardarMensajeBD() {
		if ($this->myuser) {
			if ($this->input->get ( 'id' )) {
				$id = $this->input->get ( 'id' );
				// $mensaje=$this->input->get('mensaje');
				$mensaje = preg_replace ( "/\n/", "<br/>", $this->input->get ( 'mensaje' ) );
				$datosmodificar = array (
						'mensaje' => $mensaje 
				);
				$this->load->model ( 'usuario_mensaje', 'objeto' );
				$this->objeto->cambiar_estado_id ( $id, $datosmodificar );
				echo 1;
			} else {
				echo 2;
			}
		} else {
			echo 3;
		}
	}
	public function guarda_nuevomensaje() {
		if ($this->myuser) {
			$emisor = $this->darIdUsuario ( $this->input->post ( 'receptor' ) );
			$receptor = $this->darIdUsuario ( $this->input->post ( 'emisor' ) );
			$mensaje = $this->input->post ( 'mensaje' );
			// $mensaje = utf8_decode($mensaje);
			
			$res = false;
			$res = $this->usuario->guardarMensaje ( $emisor, $receptor, $mensaje );
			if ($res != false) {
				// ////////////////////////////////
				$id = $emisor;
				$emisor = $receptor;
				
				$this->load->model ( 'usuario_mensaje', 'objeto' );
				
				$limite = $this->darCantidadMensaje ( $id, $emisor );
				
				$limite = $limite - 1;
				
				$mensajetotal = $this->objeto->get_mensaje ( $id, 1, $limite, false, $emisor );
				
				// print_r($limite);
				foreach ( $mensajetotal as $row ) {
					/*
					 * $imagen = imagenPerfil ( $usuario, "small" );
					 */
					$this->load->model ( 'usuario_model', 'objusuario' );
					$idusuario = $this->objusuario->darUsuarioXId ( $id );
					// $usuario = $this->usuario_model->darUsuarioXId($id);
					$imagen = imagenPerfil ( $idusuario, "small" );
					
					// background:white url(files/22/22.jpg?rand=2407) center
					// center
					// no-repeat scroll;width:150px;height:150px;
					?>
<img id="loader_gif<?php echo $row->id;?>" style="display: none;"
	alt="Ver más" src="assets/images/ico/ajax-loader-see-more.gif">
<li id=li <?php echo $row->id;?> style="float: left; width: 100%">
	<div id="grupo<?php echo $row->id;?>" class="comment">
		<div class="avatar"
			style="padding-right: 10px; height: 50px; width: 30px; text-align: right;">
			<img src="<?php echo $imagen;?>"
				alt="Imagen de perfil de <?php echo $row->seudonimo;?>" />
		</div>
				<?php
					if ($row->receptor != $id) {
						echo '<p class="edit"><a class="editComment" title="Editar" href="#">Editar</a></p>';
					}
					
					?>
					<div class="user-comment">
			<p class="user-name">
				<strong><a href="store/<?php echo $row->seudonimo;?>"><?php echo $row->seudonimo;?></a></strong> hace<?php
					echo $this->tiemporestante ( $row->anios, $row->meses, $row->semanas, $row->dias, $row->horas, $row->minutos, $row->segundos );
					?></p>
			</p>
			<div class="justify">
				<?php
					if ($row->articulo) {
						$this->load->model ( 'articulo_model', 'objarticulo' );
						$articulo = $this->objarticulo->darArticulo ( $row->articulo );
						// print_r($articulo);
						
						echo 'Desde <a href="';
						echo 'product/' . $row->articulo . '"';
						echo 'title="ver lorem ipsum">' . $articulo->titulo . '</a><br />';
					}
					?>
					<p id="mensaje<?php echo $row->id;?>"><?php
					
					$nuevacadena = str_replace ( "<br />", "[salto]", $row->mensaje );
					$nuevacadena2 = parse_text_html ( $nuevacadena );
					echo str_replace ( "[salto]", "<br />", $nuevacadena2 ); // 0					?></p>
			</div>
		</div>
	</div> <!--comment-->

	<div id="grupomensaje<?php echo $row->id;?>" class="edit-comment"
		style="display: none;">
		<p>
			<textarea cols="" id="<?php echo $row->id;?>" rows="3"><?php echo str_replace( "<br />", "\n", "$row->mensaje" );?></textarea>
		</p>
		<p class="t-r">
			<input type="button" class="action"
				onClick="guarda_mensaje('<?php echo $row->id;?>');"
				value="Guardar cambios" /> | <input type="button"
				class="action deleteEditComment"
				onClick="elimina_mensaje('<?php echo $row->id;?>');"
				value="Eliminar" /> | <input type="button"
				class="action cancelEditComment" value="Cancelar" />
		</p>
	</div>
</li>
<?php
				}
				
				// ///////////////////////////////
			} else {
				echo "Error";
			}
		} else {
			echo 3;
		}
	}
	public function cambiarEstadoMensajeUnico() {
		if ($this->myuser) {
			if ($this->input->get ( 'id' )) {
				$mensaje = $this->input->get ( 'id' );
				
				$cant = 0;
				$cant = substr_count ( $mensaje, "," );
				
				$tal = explode ( ",", $mensaje );
				for($i = 0; $i <= $cant; $i ++) {
					
					$ids = explode ( "-", $tal [$i] );
					
					$emisor = $ids [0];
					if ($emisor != "ADMIN") {
						$receptor = explode ( "-", $ids [1] );
						
						$mensaje = $ids [2];
						
						$datosmodificar = array (
								'estado' => 'Pendiente' 
						);
						
						$this->load->model ( 'usuario_mensaje', 'objeto' );
						
						$datosxmensaje = $this->objeto->devolverXmensaje ( $mensaje );
						
						foreach ( $datosxmensaje as $rowmensaje ) {
							// echo $rowmensaje->id;
							// echo $rowmensaje->emisor;
							// echo $rowmensaje->receptor;
							
							if ($ids [1] == $rowmensaje->emisor) {
								$datosmodificar = array (
										'estado_receptor' => 'Pendiente' 
								);
								$this->objeto->cambiar_estado_id ( $mensaje, $datosmodificar );
							} else {
								$datosmodificar = array (
										'estado' => 'Pendiente' 
								);
								$this->objeto->cambiar_estado_id ( $mensaje, $datosmodificar );
							}
						}
					} else {
						$cadena159 = explode ( "-", $ids [1] );
						$idusuario = $cadena159 [0];
						$cadena159 = explode ( "-", $ids [2] );
						$idnotificacion = $cadena159 [0];
						$this->load->model ( 'usuario_mensaje', 'objeto' );
						$datosmodificar = array (
								'visible' => 0 
						);
						
						$this->objeto->modificarestadoadmin ( $idnotificacion, $idusuario, $datosmodificar );
					}
				}
				echo 1;
			} else {
				echo 2;
			}
		} else {
			echo 3;
		}
	}
	public function mostrarTotalMensaje() {
		$id = $this->darIdUsuario ( $this->input->get ( 'receptor' ) );
		$emisor = $this->darIdUsuario ( $this->input->get ( 'emisor' ) );
		
		$this->load->model ( 'usuario_mensaje', 'objeto' );
		
		$limite = $this->darCantidadMensaje ( $id, $emisor );
		
		if ($limite >= 4) {
			$limite = $limite - 4;
		}
		
		$mensajetotal = $this->objeto->get_mensaje ( $id, $limite, 1, false, $emisor );
		
		foreach ( $mensajetotal as $row ) {
			$this->load->model ( 'usuario_model', 'objusuario' );
			$idusuario = $this->objusuario->darUsuarioXId ( $row->emisor );
			// $usuario = $this->usuario_model->darUsuarioXId($id);
			
			$imagen = imagenPerfil ( $idusuario, "small" );
			?>
<img id="loader_gif<?php echo $row->id;?>" style="display: none;"
	alt="Ver más" src="assets/images/ico/ajax-loader-see-more.gif">
<li id=li <?php echo $row->id;?> style="float: left; width: 100%;">
	<div id="grupo<?php echo $row->id;?>" class="comment">
		<div class="avatar"
			style="padding-right: 10px; height: 50px; width: 30px; text-align: right;">
			<img src="<?php echo $imagen;?>"
				alt="Imagen de perfil de <?php echo $row->seudonimo;?>" />
		</div>
			<?php
			
			if ($row->emisor == $id) {
				
				echo '<p class="edit"><a class="editComment" title="Editar" href="#">Editar</a></p>';
			} else {
				?><p class="edit">
			<a class="nmodal" title="Denunciar"
				href="home/modal/denunciar-mensaje/denunciamensaje/<?php echo $row->emisor;?>/<?php echo $row->id;?>/<?php echo $row->receptor;?>">Denunciar</a>
		</p>
			<?php
			}
			
			// verbaneo
			$varseudonimo = '';
			if ($row->estadousuario != 'Baneado') {
				$varseudonimo = $row->seudonimo;
			} else {
				$varseudonimo = "<strike>" . $row->seudonimo . "</strike>";
			}
			// finverbaneo
			?>										
										
			<div class="user-comment">
			<p class="user-name">
				<strong><a href="store/<?php echo $row->seudonimo;?>"><?php echo $varseudonimo;?></a></strong> hace <?php
			echo $this->tiemporestante ( $row->anios, $row->meses, $row->semanas, $row->dias, $row->horas, $row->minutos, $row->segundos );
			
			echo '</p>';
			?></p>
			<div class="justify">
											<?php
			if ($row->articulo) {
				$this->load->model ( 'articulo_model', 'objarticulo' );
				$articulo = $this->objarticulo->darArticulo ( $row->articulo );
				// print_r($articulo);
				
				echo 'Desde <a href="';
				echo 'product/' . $row->articulo . '"';
				echo 'title="' . $articulo->titulo . '">' . $articulo->titulo . '</a><br />';
			}
			?>
					<p id="mensaje<?php echo $row->id;?>"><?php
			$nuevacadena = str_replace ( "<br />", "[salto]", $row->mensaje );
			$nuevacadena2 = parse_text_html ( $nuevacadena );
			echo str_replace ( "[salto]", "<br />", $nuevacadena2 ); // 0
			
			?></p>
			</div>
		</div>
	</div> <!--comment-->

	<div id="grupomensaje<?php echo $row->id;?>" class="edit-comment"
		style="display: none;">
		<p>
			<textarea cols="" id="<?php echo $row->id;?>" rows="3"><?php echo str_replace( "<br />", "\n", "$row->mensaje" );?></textarea>
		</p>
		<p class="t-r">
			<input type="button" class="action"
				onClick="guarda_mensaje('<?php echo $row->id;?>');"
				value="Guardar cambios" /> | <input type="button"
				class="action deleteEditComment"
				onClick="elimina_mensaje('<?php echo $row->id;?>');"
				value="Eliminar" /> | <input type="button"
				class="action cancelEditComment" value="Cancelar" />
		</p>
	</div>
</li>
<?php
		}
	}
	public function listamensaje() {
		$seudonimo = $this->input->post ( 'seudonimo' );
		$limite = $this->input->post ( 'limite' );
		$id = $this->darIdUsuario ( $seudonimo );
		
		$this->load->model ( 'usuario_mensaje', 'objeto' );
		
		$mensajetotal = $this->objeto->get_mensaje ( $id, $limite, 1, false, false );
		
		echo $seudonimo;
		foreach ( $mensajetotal as $row ) {
			
			if ($row->estado == 'Pendiente') {
				echo '<tr class="red" >';
			} else {
				echo '<tr>';
			}
			
			$this->load->model ( 'usuario_model', 'objusuario' );
			$idusuario = $this->objusuario->darUsuarioXId ( $row->emisor );
			// $usuario = $this->usuario_model->darUsuarioXId($id);
			
			$imagen = imagenPerfil ( $idusuario, "thumb" );
			
			// verbaneo
			$varseudonimo = '';
			if ($row->estadousuario != 'Baneado') {
				$varseudonimo = $row->seudonimo;
			} else {
				$varseudonimo = "<strike>" . $row->seudonimo . "</strike>";
			}
			// finverbaneo
			
			?>
<td width="15"><input type="checkbox" /></td>
<td class="td-item"><a
	href="messageprofile/<?php echo  $seudonimo.'/'.$row->emisor;?>/inbox">
		<img src="<?php echo $imagen;?>"
		alt="Avatar de <?php echo $row->seudonimo;?>" class="imagen"
		width="64" /> <strong><?php echo $varseudonimo;?></strong>
</a> <span class="when">hace 
						<?php echo $this->tiemporestante($row->anios,$row->meses,$row->semanas,$row->dias,$row->horas,$row->minutos,$row->segundos);?>
						</span><br />
						<?php
			if ($row->articulo) {
				echo 'Desde <a href="';
				echo 'product/' . $row->articulo . '"';
				echo 'title="' . $row->articulo . '">' . $row->articulo . '</a><br />';
			}
			$nuevomensaje = parse_text_html ( $row->mensaje );
			echo str_replace ( "<br />", "\n", "$nuevomensaje" );
			?>										
					</td>
</tr>
<?php
		}
	}
	public function vermas() {
		$salir = false;
		$seudonimo = $this->input->post ( 'seudonimo' );
		$limite = $this->input->post ( 'limite' );
		
		$id = $this->darIdUsuario ( $seudonimo );
		
		$cantidad = $this->darCantidadLista ( $id );
		
		if ($cantidad > $limite) {
			if ($cantidad > ($limite + 25)) {
				$limite = $limite + 25;
			} else {
				$limite = $cantidad - $limite;
				if ($limite >= 0) {
					$salir = true;
				}
			}
		} else {
			$salir = true;
		}
		if ($salir == false) {
			?><a
	href="javascript:listamensaje('<?php echo $seudonimo."','".$limite;?>')"
	title='Ver más ventas'>Ver más</a>
<?php
		} else {
			echo '';
		}
	}
	public function tiemporestante($anios, $meses, $semanas, $dias, $horas, $minutos, $segundos) {
		if ($anios >= 1) {
			return $anios . ' años';
		} else {
			if ($meses >= 1) {
				return $meses . ' meses';
			} else {
				if ($semanas >= 1) {
					return $semanas . ' semanas';
				} else {
					if ($dias >= 1) {
						return $dias . ' dias';
					} else {
						if ($horas >= 1) {
							return $horas . ' horas';
						} else {
							if ($minutos >= 1) {
								return $minutos . ' minutos';
							} else {
								return $segundos . ' segundos';
							}
						}
					}
				}
			}
		}
	}
	
	// seguimiento
	public function quitarseguimiento() {
		if ($this->myuser) {
			$seguimiento = $this->input->post ( 'id' );
			$id = $this->input->post ( 'idusuario' );
			$this->load->model ( "Siguiendo_model", "siguiendo" );
			$this->siguiendo->eliminar_siguiendo ( $seguimiento );
			$cantidad = $this->siguiendo->get_siguiendo_cantidad ( $id );
			
			if ($cantidad == 0) {
				?>
<h1>Seguimientos</h1>
<p>Aquí se mostrarán los artículos a los que se este haciendo
	seguimiento.</p>
<?php
			} else {
				?>
<h1>Seguimientos</h1>
<p>
	            <?php echo $cantidad;?> artículos, mostrando del <strong>1</strong>
	al <strong id="contadorFinal"><? echo $cantidad;?></strong>
</p>

<?php
			}
		} else {
			echo 3;
		}
	}
	public function listar() {
		$idusuario = $this->input->post ( 'id' );
		$inicio = $this->input->post ( 'cantidad' ) + 1;
		
		$this->load->model ( 'articulo_model', 'articulo' );
		$data = $this->articulo->articulosSeguidos ( $pagina = 1, $criterio = false, $section = false, $orden = false, $ubicacion = false, $categoria = false, 22, $inicio );
		
		// /////////////////////////////////////
		$vw = intval ( $this->configuracion->variables ( "imagenArticuloMinimoAncho" ) );
		$vh = intval ( $this->configuracion->variables ( "imagenArticuloMinimoAlto" ) );
		$vencimientoOferta = intval ( $this->configuracion->variables ( "vencimientoOferta" ) ) * 86400;
		$idusuario = '';
		if (count ( $data ) > 3) {
			foreach ( $data ['articulos'] as $row ) {
				$idusuario = $row->usuarioseguimiento;
				?>
<tr id=tr <?php echo $row->idseguimiento;?>>
	<th class="td-item"><a
		href="product/<?="$row->id - ".normalizarTexto($row->titulo);?>"
		title="<?=($row->titulo)?>"><?php
				
				echo $row->titulo;
				
				$imagen = array_shift ( explode ( ",", $row->foto ) );
				$imagen = imagenArticulo ( $row->usuario, $imagen, "thumb" );
				
				list ( $w, $h ) = getimagesize ( BASEPATH . "../$imagen" );
				$nw = intval ( $vw );
				$nh = ceil ( $nw * $h / $w );
				if ($nh > $vh) {
					$nh = intval ( $vh );
					$nw = ceil ( $nh * $w / $h );
				}
				?>
                		<div class="imagen td-imagen">
				<img src="<?="$imagen"?>" width="<?=$nw;?>" height="<?=$nh;?>" />
			</div> </a></th>
	<th style="text-align: right;">
                   <?php $tipooferta= ($row->tipo == 'Fijo' ? $row->cantidadOfertas.' ofertas': $row->cantidadPujas.' pujas'); echo $tipooferta; ?>
                </th>
	<th style="text-align: right;">
		<div>
                	<?php
				$this->load->helper ( 'utilidad' );
				
				if ($row->tipo == "Fijo") {
					print calculaTiempoDiferencia ( date ( "Y-m-d H:i:s" ), strtotime ( $row->fecha_registro ) + $vencimientoOferta, true );
				} else {
					$expira = calculaTiempoDiferencia ( date ( "Y-m-d H:i:s" ), strtotime ( $row->fecha_registro ) + $row->duracion * 86400, true );
					$expira = ($expira == 0 ? "Terminado" : $expira);
					echo $expira;
				}
				?>
                   </div>
		<div>
			<a
				href="javascript:eliminar('<?php echo $row->idseguimiento."','".$row->usuarioseguimiento;?>')">Eliminar</a>
		</div>
	</th>
	<th style="text-align: right;">
               		<?php $precio= ($row->tipo == 'Fijo' ? formato_moneda($row->precio) : formato_moneda($row->mayorPuja)); echo $precio.' EUR'; ?>
               </th>

</tr>
<?php
			}
		}
		// ////////////////////////////////////
	}
	public function vermasseguimiento() {
		$idusuario = $this->input->post ( 'id' );
		$inicio = $this->input->post ( 'cantidad' ) + $this->configuracion->variables ( "cantidadPaginacion" );
		
		$this->load->model ( "Siguiendo_model", "siguiendo" );
		$totalseguir = $this->siguiendo->get_siguiendo_cantidad ( $idusuario );
		
		if ($inicio < $totalseguir) {
			?>
<p class="ver-mas">
	<a
		href="javascript:completarlista('<?php echo $idusuario."','".$inicio;?>')"
		title="Ver más ventas">Ver más</a>
</p>
<?php
		}
	}
	public function cargarmensajeadmin($idmensaje, $idusuario) {
		$data = array ();
		$data2 = array ();
		
		$this->load->model ( 'usuario_mensaje', 'objeto' );
		$mensaje = $this->objeto->darnotificacion ( $idmensaje );
		
		foreach ( $mensaje as $row ) {
			// echo parse_text_html($row->mensaje);
			$nuevacadena = str_replace ( "<br />", "[salto]", $row->mensaje );
			$nuevacadena2 = parse_text_html ( $nuevacadena );
			$data ['mensaje'] = $row->mensaje; // 0
			
			$data ['emisor'] = "El equipo de Lovende";
			$data ['tiempo'] = $this->tiemporestante ( $row->anios, $row->meses, $row->semanas, $row->dias, $row->horas, $row->minutos, $row->segundos );
			$data ['seudonimo'] = $this->darSeudonimo ( $idusuario );
			$data ['imagen'] = "assets/images/html/profile-image.png";
		}
		
		$datosmodificar = array (
				'visible' => 1 
		);
		
		$this->objeto->modificarestadoadmin ( $idmensaje, $idusuario, $datosmodificar );
		return $data;
	}
	public function cargarmensajeadministrador($id, $inicio = false, $limite = false) {
		$data = array ();
		$data2 = array ();
		
		$this->load->model ( 'usuario_mensaje', 'objeto' );
		$mensajetotal = $this->objeto->get_mensaje_admin ( $id, $inicio, $limite );
		// print_r($mensajetotal);
		if ($mensajetotal) {
			$cont = 1;
			foreach ( $mensajetotal as $row ) {
				
				if ($row->receptor) {
					$seudonimoreceptor = $this->darSeudonimo ( $row->receptor );
					
					$data ['emisor'] = "LOVENDE";
					
					$data ['receptor'] = $seudonimoreceptor;
					
					$data ['dirimagen'] = "assets/images/html/profile-image.png";
				} else {
					$idusuario = $this->objusuario->darUsuarioXId ( $row->emisor );
					
					$imagen = imagenPerfil ( $idusuario, "" );
					$data ['dirimagen'] = $imagen;
					
					$data ['emisor'] = $this->darSeudonimo ( $row->emisor );
					$data ['receptor'] = "LOVENDE";
				}
				
				$data ['id'] = $row->id;
				
				$nuevacadena = str_replace ( "<br />", "[salto]", $row->mensaje );
				$nuevacadena2 = parse_text_html ( $nuevacadena );
				$data ['mensaje'] = str_replace ( "[salto]", "<br />", $nuevacadena2 ); // 0
				
				$data ['tiempo'] = $this->tiemporestante ( $row->anios, $row->meses, $row->semanas, $row->dias, $row->horas, $row->minutos, $row->segundos );
				
				$data2 [] = $data;
				$data = array ();
			}
		}
		
		return $data2;
	}
	// /hilomensajeadmin
	public function guarda_nuevomensajedesdehilo() {
		if ($this->input->get ( 'receptor' ) != "LOVENDE") {
			
			$receptor = $this->darIdUsuario ( $this->input->get ( 'receptor' ) );
			$emisor = null;
			$id = $receptor;
		} else {
			
			$receptor = $this->darIdUsuario ( $this->input->get ( 'receptor' ) );
			$emisor = $this->darIdUsuario ( $this->input->get ( 'receptor' ) );
			$receptor = null;
			$id = $receptor;
		}
		$mostrarmas = $this->input->get ( 'vermas' );
		$mensaje = $this->input->get ( 'mensaje' );
		
		if ($mostrarmas == 'no') {
			$res = false;
			
			if ($this->myuser->tipo == "Administrador") {
				$res = $this->usuario->guardarMensaje ( $emisor, $receptor, $mensaje, null, "Admin" );
			} else {
				$res = $this->usuario->guardarMensaje ( $receptor, $emisor, $mensaje, null, "Admin" );
			}
		} else {
			$res = true;
		}
		
		if ($res != false) {
			// ////////////////////////////////
			// $id = $emisor;
			$emisor = $receptor;
			
			$datos = $this->cargarmensajeadministrador ( $id );
			// $data['cantidadmensaje'] = count($data['mensaje']);
			
			$limite = count ( $datos );
			
			if ($mostrarmas == 'si') {
				if ($limite >= 4) {
					$limite = $limite - 4;
				}
				$mensajetotal = $this->objeto->get_mensaje_admin ( $id, $limite, 1 );
			} else {
				$limite = $limite - 1;
				$mensajetotal = $this->objeto->get_mensaje_admin ( $id, 1, $limite );
			}
			
			// print_r($limite);
			foreach ( $mensajetotal as $row ) {
				/*
				 * $imagen = imagenPerfil ( $usuario, "small" );
				 */
				$this->load->model ( 'usuario_model', 'objusuario' );
				$idusuario = $this->objusuario->darUsuarioXId ( $id );
				// $usuario = $this->usuario_model->darUsuarioXId($id);
				$imagen = imagenPerfil ( $idusuario, "" );
				
				// background:white url(files/22/22.jpg?rand=2407) center center
				// no-repeat scroll;width:150px;height:150px;
				?>
<img id="loader_gif<?php echo $row->id;?>" style="display: none;"
	alt="Ver más" src="assets/images/ico/ajax-loader-see-more.gif">
<li id=li <?php echo $row->id;?> style="float: left; width: 100%">
	<div id="grupo<?php echo $row->id;?>" class="comment">
		<div class="avatar"
			style="padding-right: 10px; height: 50px; width: 30px; text-align: right;">
			<img src="<?php echo $imagen;?>"
				alt="Imagen de perfil de <?php if($row->emisor){echo $row->emisor;}else{echo "LOVENDE";}?>"
				width="32" height="32" />
		</div>
				<?php
				if ($row->receptor != $id) {
					echo '<p class="edit"><a class="editComment" title="Editar" href="#">Editar</a></p>';
				}
				?>
					<div class="user-comment">
			<p class="user-name">
				<strong><?php if($row->seudonimo){echo $row->seudonimo;}else{echo "LOVENDE";}?></strong> hace<?php
				echo $this->tiemporestante ( $row->anios, $row->meses, $row->semanas, $row->dias, $row->horas, $row->minutos, $row->segundos );
				?>	</p>
			</p>
			<div class="justify">
				<?php
				if ($row->articulo) {
					$this->load->model ( 'articulo_model', 'objarticulo' );
					$articulo = $this->objarticulo->darArticulo ( $row->articulo );
					// print_r($articulo);
					echo 'Desde <a href="';
					echo 'product/' . $row->articulo . '"';
					echo 'title="ver lorem ipsum">' . $articulo->titulo . '</a><br />';
				}
				?>
				<p id="mensaje<?php echo $row->id;?>"><?php echo parse_text_html($row->mensaje);?></p>
			</div>
		</div>
	</div> <!--comment-->

	<div id="grupomensaje<?php echo $row->id;?>" class="edit-comment"
		style="display: none;">
		<p>
			<textarea cols="" id="<?php echo $row->id;?>" rows="3"><?php echo str_replace( "<br />", "\n", "$row->mensaje" );?></textarea>
		</p>
		<p class="t-r">
			<input type="button" class="action"
				onClick="guarda_mensaje('<?php echo $row->id;?>');"
				value="Guardar cambios" /> | <input type="button"
				class="action deleteEditComment"
				onClick="elimina_mensaje('<?php echo $row->id;?>');"
				value="Eliminar" /> | <input type="button"
				class="action cancelEditComment" value="Cancelar" />
		</p>
	</div>
</li>
<?php
			}
			
			// ///////////////////////////////
		} else {
			echo "Error";
		}
	}
}

?>
