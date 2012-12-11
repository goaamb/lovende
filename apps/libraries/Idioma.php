<?php

class CI_Idioma {
	public $diccionario = array ();
	public $language = null;
	public $CI;
	
	public function __construct($lang = false) {
		$this->CI = &get_instance ();
		$this->CI->load->library ( "mysession" );
		$this->CI->load->database ();
		$this->CI->load->model ( "Idioma_model", "idiomabd" );
		if ($lang) {
			$this->actualizar ( $lang );
		}
	}
	public function actualizar($lang) {
		$this->CI->load->model ( "Usuario_model", "usuario" );
		$this->diccionario = $this->CI->idiomabd->darDiccionario ( $lang );
		$this->language = $this->CI->idiomabd->darLenguaje ( $lang );
		$this->CI->config->set_item ( 'language', strtolower ( $this->language->nombre ) );
		$session = $this->CI->mysession->userdata ( "LVSESSION" );
		if (isset ( $session ) && is_array ( $session ) && isset ( $session ["usuario"] ) && trim ( $session ["usuario"] ) !== "" && isset ( $session ["llave"] ) && $this->CI->usuario->darSesion ( $session ["llave"] )) {
			$this->CI->usuario->actualizarLenguaje ( $this->language->id );
		}
	}
	public function traducir($texto) {
		$texto = trim ( $texto );
		$hash = md5 ( $texto );
		if (isset ( $this->diccionario [$hash] )) {
			return $this->diccionario [$hash];
		} else {
			$this->CI->idiomabd->guardarPalabra ( array (
					"original" => $texto,
					"traduccion" => $texto,
					"lenguaje" => $this->language->id,
					"hash" => $hash 
			) );
			$this->diccionario [$hash] = $texto;
		}
		return $texto;
	}
	
	public function darCodigo2AlfaPais($ip) {
		if (! $this->CI->mysession->userdata ( "paisReal" . $ip )) {
			$default = "ES";
			$country = $default;
			try {
				$ip = explode ( ",", $ip );
				if (count ( $ip ) > 0) {
					$ip = trim ( $ip [0] );
				}
				ip2long ( $ip ) == - 1 || ip2long ( $ip ) === false ? trigger_error ( "Invalid IP", E_USER_ERROR ) : "";
			} catch ( Exception $e ) {
				$country = $default;
			
			}
			try {
				$country = strtoupper ( trim ( @file_get_contents ( "http://api.wipmania.com/" . $ip ) ) );
			} catch ( Exception $e ) {
				$country = $default;
			}
			
			if (strtolower ( $country ) == "xx" || strlen ( $country ) > 2) {
				try {
					$doc = simplexml_load_file ( "http://api.hostip.info/?ip=" . $ip );
					$result = $doc->xpath ( '/HostipLookupResultSet/gml:featureMember/Hostip/countryAbbrev' );
					list ( , $country ) = each ( $result );
					if (is_object ( $country )) {
						$country = ( string ) $country;
					}
				} catch ( Exception $e ) {
					$country = $default;
				}
			}
			if ($country == "XX") {
				$country = $default;
			}
			$this->CI->mysession->set_userdata ( "paisReal" . $ip, ($country ? $country : "ES") );
		}
		return $this->CI->mysession->userdata ( "paisReal" . $ip );
	}
	public function darIP() {
		$ip = isset ( $_SERVER ["HTTP_CLIENT_IP"] ) ? $_SERVER ["HTTP_CLIENT_IP"] : (isset ( $_SERVER ["HTTP_X_FORWARDED_FOR"] ) ? $_SERVER ["HTTP_X_FORWARDED_FOR"] : (isset ( $_SERVER ["REMOTE_ADDR"] ) ? $_SERVER ["REMOTE_ADDR"] : "127.0.0.1"));
		$ip = explode ( ",", $ip );
		if (count ( $ip ) > 0) {
			$ip = trim ( $ip [0] );
		}
		return $ip;
	}
	public function darLenguaje() {
		$lg = "es-ES";
		$ip = $this->darIP ();
		if ($this->CI->input->get ( "lang" )) {
			$lg = $this->CI->input->get ( "lang" );
		} elseif ($this->CI->input->cookie ( "lang" )) {
			$lg = $this->CI->input->cookie ( "lang" );
		} elseif ($this->CI->mysession->userdata ( "lg" )) {
			$lg = $this->CI->mysession->userdata ( "lg" );
		} else {
			$codigo = $this->darCodigo2AlfaPais ( $ip );
			$aux = $this->CI->idiomabd->darLenguajeXPais ( $codigo );
			if ($aux) {
				$lg = $aux->codigo;
			}
		}
		$lg = $this->CI->idiomabd->darLenguaje ( $lg );
		if (! $lg) {
			$lg = $this->CI->idiomabd->darLenguaje ( "es-ES" );
		}
		if ($this->CI->mysession->userdata ( "lang" ) != $lg->codigo) {
			$this->CI->mysession->set_userdata ( "lang", $lg->codigo );
			$this->CI->input->set_cookie ( "lang", $lg->codigo, 31536000, "", "/" );
		}
		$this->actualizar ( $lg->codigo );
	}
}

?>