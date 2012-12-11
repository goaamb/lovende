<?php
class Categoria_model extends CI_Model {
	public function __construct() {
		parent::__construct ();
	}
	public function darSubCategorias($cat = false) {
		$d = array (
				"activo" => 1 
		);
		if ($cat) {
			$d ["padre"] = $cat;
		} else {
			$d ["nivel"] = 1;
		}
		return $this->darCategoriasX ( $d );
	}
	public function darCategoriasXNivel($nivel) {
		
		return $this->darCategoriasX ( array (
				"nivel" => $nivel,
				"activo" => 1 
		) );
	}
	public function darArbolHijos($categoria, $noincusivo = false) {
		$this->db->select ( "id,nivel" );
		$this->db->where ( array (
				"padre" => $categoria,
				"activo" => 1 
		) );
		$c = $this->darTodo ( "categoria" );
		$res = array ();
		if (! $noincusivo) {
			$res [] = $categoria;
		}
		if ($c) {
			foreach ( $c as $cx ) {
				$res [] = $cx->id;
				$res = array_merge ( $res, $this->darArbolHijos ( $cx->id, true ) );
			}
		}
		return $res;
	}
	public function darArbolCategoria($id, $lang) {
		$arbol = array ();
		do {
			$this->db->where ( array (
					"id" => $id 
			) );
			$resp = $this->darUno ( "categoria" );
			if ($resp) {
				$cn = $this->darCategoriaNombre ( $id, $lang );
				array_unshift ( $arbol, array (
						"nombre" => $cn->nombre,
						"id" => $id,
						"cantidad" => $resp->cantidad,
						"url" => $cn->url_amigable,
						"nivel" => $resp->nivel 
				) );
				$id = $resp->padre;
			} else {
				$id = false;
			}
		} while ( $id );
		return $arbol;
	}
	public function darCategoriasX($campo) {
		$this->db->select("categoria.*");
		$this->db->join("nombrecategoria","nombrecategoria.categoria=categoria.id and lenguaje='{$this->idioma->language->id}'");
		$this->db->order_by("final asc,nombre asc");
		$this->db->group_by("categoria.id");
		$this->db->where ( $campo );
		return $this->darTodo ( "categoria" );
	}
	public function darCategoriasNombreX() {
		return $this->darTodo ( "nombrecategoria" );
	}
	public function darCategoriaNombre($id, $lang) {
		$this->db->where ( array (
				"categoria" => $id,
				"lenguaje" => $lang 
		) );
		return $this->darUno ( "nombrecategoria" );
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