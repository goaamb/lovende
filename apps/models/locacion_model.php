<?php
class Locacion_model extends CI_Model {
	public function __construct() {
		parent::__construct ();
	}
	public function darPaisCodigo2($codigo2) {
		$this->db->where ( array (
				"codigo2" => $codigo2 
		) );
		return $this->darUno ( "pais" );
	}
	public function darPaisDominio($dominio) {
		$this->db->select ( "codigo2,codigo3,nombre,continente" );
		$this->db->where ( array (
				"codigo2" => strtoupper ( $dominio ) 
		) );
		return $this->darUno ( "pais" );
	}
	public function listarPaises() {
		$this->db->select ( "codigo3,nombre" );
		$this->db->order_by ( "nombre", "asc" );
		return $this->darTodo ( "pais" );
	}
	public function listarCiudades($pais = false) {
		if ($pais) {
			$this->db->where ( array (
					"pais" => $pais 
			) );
		}
		$this->db->select ( "id,nombre" );
		$this->db->order_by ( "nombre", "asc" );
		return $this->darTodo ( "ciudad" );
	}
	private function darTodo($tabla) {
		$res = $this->db->get ( $tabla );
		if ($res) {
			$res = $res->result ();
			if ($res && is_array ( $res ) && count ( $res ) > 0) {
				return $res;
			}
		}
		return false;
	}
	private function darUno($tabla) {
		$res = $this->db->get ( $tabla );
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