<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class BaseController extends CI_Controller {
	private $facebook_url;
	public $myuser = false;
	public $predata = array ();
	public $tipoUbicacion = "dominio";
	public $dominio = "es";
	public $pais = false;
	public function __construct() {
		parent::__construct ();

		$this->load->database ();
		$this->load->library ( "mysession" );
		$this->load->library ( "configuracion" );
		$this->load->helper ( 'utilidad' );
		$this->tipoUbicacion = $this->configuracion->variables ( "tipoUbicacion" );
		$this->load->model ( "locacion_model", "locacion" );
		$this->load->model ( "idioma_model", "idioma_model" );

		if ($this->tipoUbicacion == "dominio") {
			$this->dominio = array_pop ( explode ( ".", $_SERVER ["SERVER_NAME"] ) );
			$this->dominio = $this->dominio ? $this->dominio : "com";
		}

		$this->pais = $this->locacion->darPaisDominio ( $this->dominio );
		if (! $this->pais) {
			$this->pais = $this->locacion->darPaisDominio ( "us" );
		}

		$lg = "es-ES";
		if ($this->pais) {
			$lenguaje = $this->idioma_model->darLenguajeXPais ( $this->pais->codigo2 );
			if ($lenguaje) {
				$lg = $lenguaje->codigo;
			}
		}
		if (! $this->mysession->userdata ( "lg" )) {
			$this->mysession->set_userdata ( "lg", $lg );
		}

		$this->load->library ( "idioma" );
		$auxp = "paisReal" . $this->idioma->darIP ();
		if (! $this->mysession->userdata ( $auxp )) {
			$this->idioma->darCodigo2AlfaPais ( $this->idioma->darIP () );
		}

		if ($this->mysession->userdata ( $auxp )) {
			$this->pais = $this->locacion->darPaisCodigo2 ( $this->mysession->userdata ( $auxp ) );
		}
		$this->load->library ( "user_agent" );
		if (isset ( $this->idioma ) && $this->idioma) {
			$this->idioma->darLenguaje ();
		}

		$this->load->helper ( 'url' );
		$this->load->helper ( 'idioma' );

		$this->facebook_url = "https://www.facebook.com/dialog/oauth?client_id=350662618351362&scope=email,read_stream&redirect_uri=" . base_url () . "facebook/connect/";
		if (isset ( $this->mysession ) && $this->mysession) {
			$this->isLogged ();
		}

		$this->predata = $this->process ();
	}
	public function index($noview = false) {
		if (isset ( $_SERVER ["argv"] ) && count ( $_SERVER ["argv"] ) > 0 && strstr ( $_SERVER ["argv"] [0], "index.php" ) !== false) {
			$this->load->library ( 'servicio' );
			$this->serviceMode ();
		} elseif (! $noview) {
			$this->loadGUI ();
		}
	}
	public function serviceMode() {
		$this->servicio->initServer ();
		$this->servicio->run2 ();
	}
	public function modal($modal, $datos = array()) {
		if (! $datos) {
			$datos = array ();
		}
		if (isset ( $modal )) {
			$this->view ( "modal/$modal", $datos );
		} else {
			$this->loadGUI ();
		}
	}
	public function loadGUI($view = false, $data = array(), $header = array()) {
		if (! $data || ! is_array ( $data )) {
			$data = array ();
		}
		if (! $header || ! is_array ( $header )) {
			$header = array ();
		}
		if ($this->myuser) {
			$this->load->model ( "Articulo_Model", "articulo" );
			$header ["totalVentas"] = $this->articulo->totalVentas ( $this->myuser->id );
			$header ["totalCompras"] = $this->articulo->totalCompras ( $this->myuser->id );
			$header ["totalSeguimientos"] = $this->articulo->totalSeguimientos ( $this->myuser->id );
			$header ["totalMensajes"] = $this->articulo->totalMensajes ( $this->myuser->id );
			$header ["totalCuentas"] = $this->articulo->totalCuentas ( $this->myuser->id );
			$header ["soloPendientes"] = $this->articulo->soloPendientes ( $this->myuser->id );
		} else {
			$header ["totalVentas"] = 0;
			$header ["totalCompras"] = 0;
			$header ["totalSeguimientos"] = 0;
			$header ["totalMensajes"] = 0;
			$header ["totalCuentas"] = 0;
			$header ["soloPendientes"] = 0;
		}
		$header = array_merge ( array (
				"logged" => ($this->myuser !== false),
				"usuario" => $this->myuser
		), $header );
		if ($this->predata) {
			$data = array_merge ( $data, $this->predata );
		}
		$this->view ( "includes/header", $header );
		$this->uri = & load_class ( 'URI', 'core' );
		$this->RTR = & load_class ( 'Router', 'core' );
		$df = strtolower ( $this->RTR->routes ["default_controller"] );
		if (! $view && count ( $this->uri->segments ) > 0) {
			$view = $this->uri->segments [1];
		} elseif (! $view) {
			$view = $df;
		}

		if (! isset ( $data )) {
			$data = array ();
		}
		$this->view ( $view, $data );
		$this->view ( "includes/footer" );
	}
	public function view($view, $data = null) {
		if (is_file ( APPPATH . "views/$view" . EXT )) {
			$this->load->view ( $view, $data );
		}
	}
	public function isLogged() {
		$this->load->model ( "Usuario_model", "usuario" );
		$session = $this->mysession->userdata ( "LVSESSION" );
		if ($this->usuario->darSesion ( $this->input->cookie ( "LVSESSION" ) )) {
			if ($this->usuario->login ()) {
				$this->myUser ( $this->usuario );
			}
		}
		if (isset ( $session ) && is_array ( $session ) && isset ( $session ["usuario"] ) && trim ( $session ["usuario"] ) !== "") {
			if (! $this->myuser && $this->usuario->darUsuarioXSeudonimo ( $session ["usuario"] )) {
				$this->myUser ( $this->usuario );
			}
			return true;
		}
		return false;
	}
	private function myUser($usuario) {
		if ($usuario) {
			$this->myuser = clone ($usuario);
			$this->myuser->pais = $this->myuser->darPais ();
			if (! $this->myuser->pais) {
				$this->myuser->pais = new stdClass ();
				$this->myuser->pais->nombre = "";
				$this->myuser->pais->codigo3 = "";
			}
		}
	}
	public function process() {
		$data = array (
				"facebook_url" => $this->facebook_url
		);
		return $data;
	}
}

?>