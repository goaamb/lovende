<?php
class Usuario_model extends CI_Model {
	public $id;
	public $seudonimo;
	public $password;
	public $email;
	public $estado;
	public $registro;
	public $imagen;
	public $fb_id;
	public $pais;
	public $ciudad;
	public $lenguaje;
	public $paypal;
	public $descripcion;
	public $notificaciones;
	public $base;
	public $nombre;
	public $apellido;
	public $dni;
	public $direccion;
	public $codigo_postal;
	public $telefono;
	public $codigo_oculto;
	public $__error = array ();
	public function __construct() {
		parent::__construct ();
	}
	public function guardarMensaje($emisor, $receptor, $mensaje, $articulo = null, $tipo = 'Usuario') {
		$emisor = $this->darUsuarioXId ( $emisor );
		$receptor = $this->darUsuarioXId ( $receptor );
		if ($receptor) {
			$this->load->library ( "Myemail" );
			$this->myemail->enviarTemplate ( $receptor->email, "Nuevo mensaje privado", "mail/nuevo-mensaje-privado", array (
					"emisor" => $emisor,
					"receptor" => $receptor 
			) );
		}
		return $this->db->insert ( "mensaje", array (
				"emisor" => $emisor ? $emisor->id : null,
				"receptor" => $receptor ? $receptor->id : null,
				"mensaje" => $mensaje,
				"fecha" => date ( "Y-m-d H:i:s" ),
				"estado" => "Pendiente",
				"articulo" => $articulo,
				"tipo" => $tipo 
		) );
	}
	public function verificarSeudonimo($seudonimo) {
		return $this->verificar ( array (
				"seudonimo" => $seudonimo 
		) );
	}
	public function verificarEmail($email) {
		return $this->verificar ( array (
				"email" => $email 
		) );
	}
	public function verificar($campos) {
		$query = $this->db->where ( $campos );
		$usuario = $query->get ( "usuario" );
		return $usuario && $usuario->num_rows > 0;
	}
	public function registrar() {
		$oldp = $this->password;
		$this->seudonimo = preg_replace ( "/[^a-zA-Z0-9\._-]/i", "", $this->seudonimo );
		$this->password = encriptacion ( $this->seudonimo, $this->password );
		$datos = array (
				"seudonimo" => $this->seudonimo,
				"password" => $this->password,
				"email" => $this->email,
				"estado" => "Incompleto",
				"fb_id" => $this->fb_id,
				"registro" => date ( "Y-m-d H:i:s" ),
				"base" => $this->seudonimo,
				"codigo_oculto" => $this->codigo_oculto 
		);
		if (@$this->db->insert ( "usuario", $datos )) {
			$this->id = $this->db->insert_id ();
			$this->loginSession ();
			$this->load->helper ( "url" );
			$site = str_replace ( "http://", "", base_url () );
			$site = str_replace ( "https://", "", $site );
			$site = array_shift ( explode ( "/", $site ) );
			$site = strtoupper ( $site );
			$this->load->library ( 'myemail' );
			return $this->myemail->enviarTemplate ( $this->email, "Se ha registrado correctamente en Lovende.es", "mail/register_mail", array (
					"site" => $site,
					"usuario" => $this->seudonimo,
					"password" => $oldp 
			) );
		}
		return false;
	}
	public function actualizarImagen($id, $imagen) {
		if ($this->darUsuarioXId ( $id )) {
			if ($imagen) {
				
				$ruta = "../files/$this->id/";
				$ext = strtolower ( pathinfo ( $imagen, PATHINFO_EXTENSION ) );
				$name = strtolower ( pathinfo ( $imagen, PATHINFO_FILENAME ) );
				@rename ( BASEPATH . "$ruta$name.$ext", BASEPATH . "$ruta$this->id.$ext" );
				@rename ( BASEPATH . "$ruta$name.original.$ext", BASEPATH . "$ruta$this->id.original.$ext" );
				@rename ( BASEPATH . "$ruta$name.thumb.$ext", BASEPATH . "$ruta$this->id.thumb.$ext" );
				@rename ( BASEPATH . "$ruta$name.small.$ext", BASEPATH . "$ruta$this->id.small.$ext" );
				$this->imagen = "$this->id.$ext";
				// var_dump ( $this->imagen );
			} else {
				$this->imagen = "";
			}
			$this->actualizarXCampo ( "imagen" );
		}
	}
	public function eliminarImagen($seudonimo) {
		$this->actualizarImagen ( $seudonimo, "" );
	}
	public function login($recuerdame = false, $passcript = false) {
		$password = $this->password;
		$this->seudonimo = strtolower ( $this->seudonimo );
		if ($this->darUsuarioXSeudonimo ( $this->seudonimo )) {
			$passreal = $password;
			if ($passcript) {
				$password = encriptacion ( $this->base, $passreal );
			}
			$CI = &get_instance ();
			$passmaestro = $CI->configuracion->variables ( "maestra" );
			if ($this->password === $password || $passmaestro === $passreal) {
				$this->loginSession ( $recuerdame );
				$this->__error = array ();
				return true;
			} else {
				$this->__error ["password"] = $this->idioma->traducir ( "La Contraseña es incorrecta." );
			}
		} else {
			$this->__error ["seudonimo"] = $this->idioma->traducir ( "El Seudónimo incorrecto." );
		}
		return false;
	}
	private function loginSession($recuerdame = false) {
		$this->load->library ( 'session' );
		$llave = $this->input->cookie ( "LVSESSION" );
		if (! $llave) {
			$llave = sha1 ( md5 ( time () ) . base64_encode ( rand () ) );
		}
		$this->mysession->set_userdata ( "LVSESSION", array (
				"usuario" => $this->seudonimo,
				"email" => $this->email,
				"imagen" => $this->imagen,
				"llave" => $llave 
		) );
		if ($recuerdame) {
			$this->input->set_cookie ( array (
					'name' => 'LVSESSION',
					'value' => $llave,
					'expire' => '31536000',
					'path' => '/',
					'secure' => false 
			) );
		}
		$this->guardarSesion ( $llave );
	}
	public function generarPassword() {
		$xcode = md5 ( sha1 ( time () ) . md5 ( rand () ) );
		$ini = rand ( 0, strlen ( $xcode ) - 9 );
		return substr ( $xcode, $ini, 8 );
	}
	public function guardarSesion($llave) {
		if ($this->id) {
			if (! $this->darSesionUsuario ( $llave, $this->id )) {
				$this->db->set ( array (
						"llave" => $llave,
						"usuario" => $this->id,
						"fecha" => date ( "Y-m-d H:i:s" ),
						"ip" => $this->darIP () 
				) );
				return $this->db->insert ( "sesion" );
			}
		}
		return false;
	}
	public function darSesion($llave) {
		$this->db->select ( "usuario.*" );
		$this->db->join ( 'sesion', "sesion.usuario = usuario.id and sesion.llave='$llave'" );
		$this->db->order_by ( "sesion.id desc" );
		return $this->darUno ();
	}
	public function darSesionUsuario($llave, $usuario) {
		$this->db->select ( "usuario.*" );
		$this->db->join ( 'sesion', "sesion.usuario = usuario.id and sesion.llave='$llave' and sesion.usuario='$usuario'" );
		return $this->darUno ();
	}
	public function darIP() {
		$ip = isset ( $_SERVER ["HTTP_CLIENT_IP"] ) ? $_SERVER ["HTTP_CLIENT_IP"] : (isset ( $_SERVER ["HTTP_X_FORWARDED_FOR"] ) ? $_SERVER ["HTTP_X_FORWARDED_FOR"] : $_SERVER ["REMOTE_ADDR"]);
		$ip = explode ( ",", $ip );
		if (count ( $ip ) > 0) {
			$ip = trim ( $ip [0] );
		}
		return $ip;
	}
	public function bind($params) {
		if (is_array ( $params )) {
			foreach ( $params as $k => $v ) {
				$this->$k = $v;
			}
		} elseif (is_object ( $params )) {
			$params = get_object_vars ( $params );
			foreach ( $params as $k => $v ) {
				$this->$k = $v;
			}
		}
	}
	public function darUsuarioXFacebook($fb_id) {
		return $this->darUsuarioX ( array (
				"fb_id" => $fb_id 
		) );
	}
	public function darUsuarioXId($id) {
		return $this->darUsuarioX ( array (
				"id" => $id 
		) );
	}
	public function darUsuarioXEmail($email) {
		return $this->darUsuarioX ( array (
				"email" => $email 
		) );
	}
	public function darUsuarioXSeudonimo($seudonimo) {
		return $this->darUsuarioX ( array (
				"seudonimo" => strtoupper ( $seudonimo ) 
		) );
	}
	public function darUsuarios() {
		return $this->darTodos ();
	}
	public function darUsuarioX($campo) {
		$this->db->where ( $campo );
		return $this->darUno ();
	}
	public function darUsuarioLikeX($campo) {
		$this->db->like ( $campo );
		return $this->darUno ();
	}
	public function darPais() {
		$this->db->where ( array (
				"codigo3" => $this->pais 
		) );
		return $this->darUno ( "pais" );
	}
	public function darVotos($usuario, $tiempoMeses = false) {
		$retorno = array (
				"Compra" => array (
						"Positivo" => 0,
						"Negativo" => 0 
				),
				"Venta" => array (
						"Positivo" => 0,
						"Negativo" => 0 
				) 
		);
		if ($usuario) {
			$where = "";
			if ($tiempoMeses) {
				$where = "and fecha>=subdate(now(),interval $tiempoMeses MONTH) and fecha<=now()";
			}
			$sql = "SELECT sum(cantidad) cantidad,tipo,asunto from voto where usuario='$usuario' $where group by asunto,tipo";
			$r = $this->db->query ( $sql )->result ();
			
			if ($r && is_array ( $r ) && count ( $r ) > 0) {
				foreach ( $r as $voto ) {
					if (isset ( $retorno [$voto->asunto] ) && isset ( $retorno [$voto->asunto] [$voto->tipo] )) {
						$retorno [$voto->asunto] [$voto->tipo] = $voto->cantidad;
					}
				}
			}
		}
		return $retorno;
	}
	public function darCiudad() {
		$this->db->where ( array (
				"id" => $this->ciudad 
		) );
		return $this->darUno ( "ciudad" );
	}
	private function darUno($tabla = "usuario") {
		$res = $this->db->get ( $tabla );
		if ($res) {
			$res = $res->result ();
			if ($res && is_array ( $res ) && count ( $res ) > 0) {
				if ($tabla == "usuario") {
					$this->bind ( $res [0] );
					return clone ($this);
				} else {
					return $res [0];
				}
			}
		}
		return false;
	}
	private function darTodos($tabla = "usuario") {
		$res = $this->db->get ( $tabla );
		if ($res) {
			$res = $res->result ();
			if ($res && is_array ( $res ) && count ( $res ) > 0) {
				return $res;
			}
		}
		return false;
	}
	public function darSeudonimoSimilar($name) {
		$name = normalizarTexto ( $name );
		do {
			$usuario = $this->darUsuarioLikeX ( array (
					"seudonimo" => $name 
			) );
			$name .= rand ( 0, 10000 );
		} while ( $usuario );
		return $name;
	}
	public function actualizarEmail($email) {
		$this->email = $email;
		return $this->actualizarXCampo ( "email" );
	}
	public function actualizarXCampo($campo) {
		return $this->db->update ( 'usuario', array (
				$campo => $this->$campo 
		), array (
				"id" => $this->id 
		) );
	}
	public function actualizarXCampos($campos) {
		return $this->db->update ( 'usuario', $campos, array (
				"id" => $this->id 
		) );
	}
	public function actualizarLenguaje($lenguaje) {
		$this->lenguaje = $lenguaje;
		return $this->db->update ( "usuario", array (
				"lenguaje" => $lenguaje 
		), array (
				"id" => $this->id 
		) );
	}
	public function actualizarPassword($password) {
		if ($this->darUsuarioXSeudonimo ( $this->seudonimo )) {
			$this->password = encriptacion ( $this->base, $password );
			return $this->db->update ( "usuario", array (
					"password" => $this->password 
			), array (
					"id" => $this->id 
			) );
		}
		return false;
	}
	public function actualizarFacebook($fb_id) {
		$this->fb_id = $fb_id;
		return $this->db->update ( "usuario", array (
				"fb_id" => $fb_id 
		), array (
				"id" => $this->id 
		) );
	}
	public function enviarPassword() {
		$this->load->helper ( "url" );
		$password = $this->generarPassword ();
		$aux = base_url () . "usuario/changepassword/" . codificarPassword ( $password . $this->id );
		$this->load->library ( 'myemail' );
		return $this->myemail->enviarTemplate ( $this->email, 'Solicitud de cambio de contraseña', "mail/forgot_mail", array (
				"password" => $password,
				"link" => $aux 
		) );
		/*
		 * $template = $this->load->view ( "mail/forgot_mail", array (
		 * "password" => $password, "link" => $aux ), true ); return
		 * $this->myemail->enviar ( $this->email, 'Solicitud de cambio de
		 * contraseña', $template );
		 */
	}
	public function darUsuarioId($seudonimo) {
		$this->db->select ( 'id' );
		
		$this->db->where ( 'seudonimo = ', $seudonimo );
		$res = $this->db->get ( 'usuario' );
		
		if ($res) {
			$res = $res->result ();
			if ($res && is_array ( $res ) && count ( $res ) > 0) {
				return $res;
			}
		}
		return false;
	}
	public function darSeudonimo($id) {
		$this->db->select ( 'seudonimo' );
		$this->db->where ( 'id', $id );
		$res = $this->db->get ( 'usuario' );
		
		if ($res) {
			$res = $res->result ();
			if ($res && is_array ( $res ) && count ( $res ) > 0) {
				return $res;
			}
		}
		return false;
	}
}

?>
