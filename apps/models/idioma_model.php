<?php

class Idioma_model extends CI_Model {
	
	public function __construct() {
		parent::__construct ();
	}
	
	public function darDiccionario($lenguaje) {
		$this->db->join ( "lenguaje", "diccionario.lenguaje=lenguaje.id and lenguaje.codigo='$lenguaje'" );
		$res = $this->db->get ( "diccionario" );
		$diccionario = array ();
		if ($res) {
			$res = $res->result ();
			if ($res && is_array ( $res ) && count ( $res ) > 0) {
				foreach ( $res as $item ) {
					$traducc = trim ( $item->traduccion );
					if ($traducc !== "") {
						$diccionario [$item->hash] = $traducc;
					} else {
						$diccionario [$item->hash] = $item->original;
					}
				}
			}
		}
		return $diccionario;
	}
	
	public function darLenguaje($lenguaje) {
		$this->db->where ( array (
				"codigo" => "$lenguaje" 
		) );
		return $this->darUnIdioma ();
	}
	
	public function guardarPalabra($palabra) {
		if (is_array ( $palabra )) {
			return $this->db->insert ( "diccionario", $palabra );
		}
	}
	
	public function darLenguajeXPais($codigo) {
		$this->db->join ( "pais", "1" );
		$this->db->join ( "paislenguaje", "paislenguaje.pais=pais.codigo3 and lenguaje.id=paislenguaje.lenguaje and paislenguaje.porcentaje=(select max(paislenguaje.porcentaje) from paislenguaje where paislenguaje.pais=pais.codigo3)" );
		$this->db->where( "pais.codigo2", $codigo );
		return $this->darUnIdioma ();
	}
	
	public function darNombreLenguaje($codigo) {
		$this->db->where ( array (
				"codigo" => $codigo 
		) );
		return $this->darUnIdioma ();
	}
	
	private function darUnaPalabra() {
		$res = $this->db->get ( "diccionario" );
		if ($res) {
			$res = $res->result ();
			if ($res && is_array ( $res ) && count ( $res ) > 0) {
				$this->bind ( $res [0] );
				return $this;
			}
		}
		return false;
	}
	private function darUnIdioma() {
		$res = $this->db->get ( "lenguaje", 1, 0 );
		if ($res) {
			$res = $res->result ();
			if ($res && is_array ( $res ) && count ( $res ) > 0) {
				return $res [0];
			}
		}
		return false;
	}

}

?>