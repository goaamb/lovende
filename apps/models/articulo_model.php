<?php
class Articulo_model extends CI_Model {
	public $id;
	public $usuario;
	public $titulo;
	public $descripcion;
	public $categoria;
	public $foto;
	public $tipo;
	public $precio;
	public $precio_rechazo;
	public $moneda;
	public $duracion;
	public $pagos;
	public $fecha_registro;
	public $estado;
	public $fecha_alta;
	public $cantidad;
	public $cantidad_original;
	public $gastos_pais;
	public $gastos_continente;
	public $gastos_todos;
	public $envio_local;
	public static $formas_pago = array (
			1 => "Otros",
			2 => "Pago contra reembolso",
			3 => "Transferencia bancaria",
			4 => "Paypal" 
	);
	public static $tarifa = array ();
	public function __construct() {
		parent::__construct ();
		$this->cargarTarifas ();
	}
	public function listarSeguimientosPorFinalizar($tiempo) {
		$tiempo = intval ( $tiempo ) * 3600;
		$sql = "select s.id as seguimiento,u.seudonimo,u.email,a.id,a.titulo from (select id,titulo,
				if(tipo='Subasta',
				duracion*86400+unix_timestamp(fecha_registro-unix_timestamp())
				," . $this->configuracion->variables ( "vencimientoOferta" ) . "*86400-(unix_timestamp()-unix_timestamp(fecha_registro))) as tiempo
				From articulo
				where terminado=0
				having tiempo<= $tiempo ) as a
				inner join siguiendo as s on a.id=s.articulo and s.notificado='No'
				inner join usuario as u on u.id=s.usuario
				order by u.email asc,a.titulo asc,a.id asc";
		return $this->db->query ( $sql )->result ();
	}
	public function modificarCantidad($a) {
		if ($a && $a->tipo == "Cantidad") {
			$this->db->update ( "articulo", array (
					"cantidad" => $a->cantidad,
					"cantidad_original" => $a->cantidad_original,
					"terminado" => 0,
					"fecha_terminado" => null 
			), array (
					"id" => $a->id 
			) );
		}
	}
	public function cargarTarifas() {
		$r = $this->db->order_by ( "tipo_tarifa asc,tipo_articulo asc,inicio asc" )->get ( "tarifa" )->result ();
		$tarifa = array ();
		if ($r && is_array ( $r ) && count ( $r ) > 0) {
			foreach ( $r as $t ) {
				$tt = $t->tipo_tarifa;
				$ta = $t->tipo_articulo;
				if (! isset ( $tarifa [$tt] )) {
					$tarifa [$tt] = array ();
				}
				switch ($tt) {
					case "Comision" :
						if (! isset ( $tarifa [$tt] [$ta] )) {
							$tarifa [$tt] [$ta] = array ();
						}
						$tarifa [$tt] [$ta] [] = array (
								"inicio" => $t->inicio,
								"porcentaje" => $t->porcentaje 
						);
						break;
					case "Plana" :
						$tarifa [$tt] [] = array (
								"inicio" => $t->inicio,
								"monto" => $t->monto,
								"nombre" => $t->nombre 
						);
						break;
				}
			}
		}
		self::$tarifa = $tarifa;
		// var_dump ( self::$tarifa );
	}
	public function darCuentasPorArticulos($articulos) {
		$narticulos = array ();
		$articulos = explode ( ",", $articulos );
		foreach ( $articulos as $a ) {
			if (trim ( $a ) !== "") {
				$narticulos [] = trim ( $a );
			}
		}
		$articulos = implode ( ",", $narticulos );
		if (trim ( $articulos ) !== "") {
			$cuentas = $this->db->query ( "select cuenta.*,articulo.titulo as titulo,if(isnull(articulo.precio_oferta),articulo.precio,articulo.precio_oferta)as precio,cuenta.cantidad as cantidad from cuenta inner join articulo on articulo.id=cuenta.articulo where articulo in ($articulos ) order by articulo.titulo asc" )->result ();
			if ($cuentas) {
				return $cuentas;
			}
		}
		return false;
	}
	public function darCuentasFakePorArticulos($articulos, $usuario) {
		$narticulos = array ();
		$articulos = explode ( ",", $articulos );
		$mes = date ( "m" );
		$anio = date ( "Y" );
		foreach ( $articulos as $a ) {
			if (trim ( $a ) !== "") {
				$narticulos [] = trim ( $a );
			}
		}
		$articulos = implode ( ",", $narticulos );
		if (trim ( $articulos ) !== "") {
			
			if ($usuario->tipo == "Comision") {
				$sql = "select articulo.titulo as titulo,
				articulo.precio as precio,
				articulo.id as articulo,
				if(articulo.tipo='Subasta',(select monto from cuenta where cuenta.articulo=articulo.id),'--') as monto,
				articulo.fecha_registro as fecha,
				if(articulo.tipo='Cantidad',(select cantidad from cuenta where cuenta.articulo=articulo.id and cuenta.usuario='$usuario->id'),0) as cantidad,
				if(articulo.tipo='Cantidad',(select transaccion.paquete from transaccion where transaccion.articulo=articulo.id),0) as paquete
				from articulo
				where articulo.id in ( $articulos ) order by articulo.titulo asc";
			} else {
				$sql = "select * from((select
				articulo.titulo as titulo,
				articulo.precio as precio,
				articulo.id as articulo,
				(select monto from cuenta where cuenta.articulo=articulo.id) as monto,
				articulo.fecha_registro as fecha,
				articulo.cantidad as cantidad,
				articulo.paquete as paquete,
				articulo.usuario as usuario
				from articulo
				where articulo.id in ( $articulos ) and articulo.tipo='Subasta' and articulo.usuario='$usuario->id')
				union
				(select
				articulo.titulo as titulo,
				if(articulo.precio_oferta,articulo.precio_oferta,articulo.precio) as precio,
				articulo.id as articulo,
				0 as monto,
				articulo.fecha_registro as fecha,
				articulo.cantidad as cantidad,
				articulo.paquete as paquete,
				articulo.usuario as usuario
				from articulo inner join (select articulos from paquete where not isnull(fecha_envio) and Month(fecha_envio)='$mes' and year(fecha_envio)='$anio') as xx on (xx.articulos like articulo.id or xx.articulos like concat('%,',articulo.id) or xx.articulos like concat(articulo.id,',%') or xx.articulos like concat('%,',articulo.id,',%'))
				where articulo.tipo<>'Subasta' and articulo.usuario='$usuario->id'
				)
				union
				(select
				articulo.titulo as titulo,
				articulo.precio as precio,
				articulo.id as articulo,
				0 as monto,
				articulo.fecha_registro as fecha,
				x.cantidad as cantidad,
				x.paquete as paquete,
				articulo.usuario as usuario
				from articulo inner join (select articulo,cantidad,paquete from transaccion inner join (select transacciones from paquete where not isnull(fecha_envio) and Month(fecha_envio)='$mes' and year(fecha_envio)='$anio') as xx on (xx.transacciones like transaccion.id or xx.transacciones like concat('%,',transaccion.id) or xx.transacciones like concat(transaccion.id,',%') or xx.transacciones like concat('%,',transaccion .id,',%')))as x on x.articulo=articulo.id
				where articulo.tipo<>'Subasta' and articulo.usuario='$usuario->id'
				))as xxx
				order by xxx.titulo asc";
			}
			// print "<div style='display:none;'>$sql</div>";
			$cuentas = $this->db->query ( $sql )->result ();
			if ($cuentas) {
				return $cuentas;
			}
		}
		return false;
	}
	public function darFactura($factura) {
		$r = $this->db->where ( array (
				"id" => $factura 
		) )->get ( "factura" )->result ();
		if ($r && is_array ( $r ) && count ( $r ) > 0) {
			return $r [0];
		}
		return false;
	}
	public function darFacturas($usuario, $inicio = 0, $pagina = 0) {
		$this->load->model ( "Usuario_model", "usuarioM" );
		$usuario = $this->usuarioM->darUsuarioXId ( $usuario );
		if ($usuario) {
			$this->db->where ( array (
					"factura.usuario" => $usuario->id 
			) );
			$this->db->order_by ( "mes desc" );
			$inicio = intval ( $inicio );
			$pagina = intval ( $pagina );
			if ($pagina) {
				$this->db->limit ( $pagina, $inicio );
			} else {
				$this->db->limit ( false, $inicio );
			}
			$this->db->join ( "usuario", "usuario.id =factura.usuario" );
			$this->db->select ( "factura.*,usuario.id as idusu,usuario.seudonimo,usuario.positivo,usuario.negativo" );
			return $this->db->get ( "factura" )->result ();
		}
		return array ();
	}
	public function contarFacturas($usuario) {
		$this->load->model ( "Usuario_model", "usuarioM" );
		$usuario = $this->usuarioM->darUsuarioXId ( $usuario );
		if ($usuario) {
			$this->db->where ( array (
					"usuario" => $usuario->id 
			) );
			$this->db->select ( "count(id) as cantidad" );
			$r = $this->db->get ( "factura" )->result ();
			if ($r && is_array ( $r ) && count ( $r ) > 0) {
				return $r [0]->cantidad;
			}
		}
		return 0;
	}
	public function listarSubastas() {
		$this->db->where ( array (
				"tipo" => "Subasta",
				"terminado" => 0 
		) );
		return $this->darTodos ( "articulo" );
	}
	public function actualizarPublicacion($articulo) {
		if ($this->db->update ( "articulo", array (
				"fecha_registro" => date ( "Y-m-d H:i:s" ) 
		), array (
				"id" => $articulo 
		) )) {
			return $this->db->update ( "siguiendo", array (
					"notificado" => "No" 
			), array (
					"articulo" => $articulo 
			) );
		}
	}
	private function calcularCantidades(&$arbol, $cantidades) {
		if ($arbol && $cantidades) {
			$mc = 0;
			foreach ( $arbol as $id => &$padre ) {
				$c = 0;
				if (isset ( $cantidades [$id] )) {
					$c = $cantidades [$id];
				}
				$c += $this->calcularCantidades ( $padre ["hijos"], $cantidades );
				if ($c >= 0) {
					$padre ["datos"] ["cantidad"] = $c;
				}
				$mc += $c;
			}
			return $mc;
		}
		return 0;
	}
	public function darDatosCuentas($mes, $anio, $usuario) {
		$this->db->select ( "id" );
		$mes = intval ( $mes );
		if ($mes < 10) {
			$mes = "0" . $mes;
		}
		
		$this->db->where ( array (
				"mes" => "$mes-$anio",
				"usuario" => $usuario->id 
		) );
		$r = $this->db->get ( "factura" )->result ();
		if ($r && is_array ( $r ) && count ( $r ) > 0) {
			// print "ya existe la factura del $mes-$anio y usuario:
			// $usuario->id - $usuario->seudonimo<br/>";
			return false;
		}
		$total = 0;
		$articulos2 = array ();
		$this->db->where ( "fecha_envio between '" . date ( "$anio-$mes-01 00:00:00" ) . "' and '" . date ( "$anio-$mes-t 23:59:59" ) . "' and vendedor='$usuario->id'" );
		$r = $this->db->get ( "paquete" )->result ();
		if ($r && is_array ( $r ) && count ( $r ) > 0) {
			$paquetes = $r;
		}
		// var_dump($this->db->last_query());
		$articulos = array ();
		$transacciones = array ();
		
		if (isset ( $paquetes )) {
			foreach ( $paquetes as $p ) {
				$articulos = array_merge ( $articulos, explode ( ",", $p->articulos ) );
				$transacciones = array_merge ( $transacciones, explode ( ",", $p->transacciones ) );
			}
		}
		$articulos = array_unique ( $articulos );
		$transacciones = array_unique ( $transacciones );
		$as = array ();
		$ts = array ();
		$totalVendido = 0;
		foreach ( $articulos as $a ) {
			$a = $this->darArticulo ( $a );
			if ($a) {
				$totalVendido += floatval ( $a->precio_oferta ? $a->precio_oferta : $a->precio );
				$as [] = $a;
			}
		}
		
		foreach ( $transacciones as $t ) {
			$r = $this->db->where ( array (
					"id" => $t 
			) )->get ( "transaccion" )->result ();
			if ($r && is_array ( $r ) && count ( $r ) > 0) {
				$articulos2 [] = $r [0]->articulo;
				$totalVendido += floatval ( $r [0]->precio * $r [0]->cantidad );
				$ts [] = $r [0];
			}
		}
		$totalVentas = $this->sumarArticulosEnVentaFijo ( $usuario->id );
		if ($usuario->tipo_tarifa == "Comision") {
			
			if (! isset ( $paquetes )) {
				// print "no hay facturas<br/>";
				return false;
			}
			$total = $totalVendido;
			$monto = 0;
			foreach ( $as as $a ) {
				$this->db->where ( array (
						"articulo" => $a->id 
				) );
				$r = $this->db->get ( "cuenta" )->result ();
				if ($r && is_array ( $r ) && count ( $r ) > 0) {
					$tarifa = floatval ( $r [0]->monto );
				} else {
					$tarifa = $this->calcularTarifa ( $a, "Comision" );
					$this->db->insert ( "cuenta", array (
							"articulo" => $a->id,
							"paquete" => $a->paquete,
							"monto" => $tarifa,
							"fecha" => date ( "Y-m-d H:i:s" ),
							"usuario" => $usuario->id 
					) );
				}
				$monto += $tarifa;
			}
			foreach ( $ts as $t ) {
				$this->db->where ( array (
						"articulo" => $t->articulo 
				) );
				$r = $this->db->get ( "cuenta" )->result ();
				if ($r && is_array ( $r ) && count ( $r ) > 0) {
					$tarifa = floatval ( $r [0]->monto );
				} else {
					$tarifa = $this->calcularTarifa ( $t, "Comision", false, true );
					$this->db->insert ( "cuenta", array (
							"articulo" => $t->articulo,
							"paquete" => $t->paquete,
							"monto" => $tarifa,
							"fecha" => date ( "Y-m-d H:i:s" ),
							"usuario" => $usuario->id,
							"cantidad" => $t->cantidad 
					) );
				}
				$monto += $tarifa;
			}
		} else {
			$monto = 0;
			$tarifa = 0;
			$rs = $this->db->query ( "select * from articulo where terminado=0 and usuario='$usuario->id' and tipo<>'Subasta'" )->result ();
			foreach ( $rs as $a ) {
				$articulos [] = $a->id;
			}
			
			$rs = $this->db->query ( "select * from articulo where terminado=1 and usuario='$usuario->id' and tipo='Subasta' and not isnull(paquete)" )->result ();
			foreach ( $rs as $a ) {
				$this->db->where ( array (
						"articulo" => $a->id 
				) );
				$r = $this->db->get ( "cuenta" )->result ();
				if ($r && is_array ( $r ) && count ( $r ) > 0) {
					$tarifa = floatval ( $r [0]->monto );
				} else {
					$tarifa = $this->calcularTarifa ( $a, "Comision" );
					$this->db->insert ( "cuenta", array (
							"articulo" => $a->id,
							"paquete" => null,
							"monto" => $tarifa,
							"fecha" => date ( "Y-m-d H:i:s" ),
							"usuario" => $usuario->id 
					) );
				}
				$monto += $tarifa;
			}
			$total = $totalVentas;
			$monto += $this->calcularTarifa ( false, "Plana", $usuario->id );
		}
		$nc = 0;
		$r = $this->db->query ( "SELECT substr(codigo,1,length(codigo)-5) as nc from factura where substr(codigo,length(codigo)-3)='$anio' order by codigo desc limit 0,1" )->result ();
		if ($r && is_array ( $r ) && count ( $r ) > 0) {
			$nc = intval ( $r [0]->nc );
		}
		$nc ++;
		$iva = $monto * 0.21;
		$x = new stdClass ();
		$x->codigo = "$nc/$anio";
		$x->mes = "$mes-$anio";
		$x->usuario = $usuario->id;
		$x->fecha = date ( "Y-m-d H:i:s" );
		$x->articulos = isset ( $articulos ) ? implode ( ",", array_merge ( $articulos, $articulos2 ) ) : null;
		$x->monto_stock = $totalVentas;
		$x->monto_venta = $totalVendido;
		$x->monto_tarifa = $monto;
		$x->iva = $iva;
		return $x;
	}
	private function darCategorias2($categoria = false, $categorias = false, $dosniveles = false) {
		$CI = &get_instance ();
		$CI->load->model ( "Categoria_model", "categoria" );
		$ret = array ();
		$cantidades = array ();
		if ($categorias && is_array ( $categorias ) && count ( $categorias ) > 0) {
			$cs = array ();
			foreach ( $categorias as $c ) {
				$cantidades [$c->categoria] = $c->cantidad;
			}
		}
		$arboles = Array ();
		$contados = array ();
		$cR = array ();
		foreach ( $cantidades as $id => $c ) {
			$arbol = $this->darPadres ( $id, $contados );
			foreach ( $arbol as $i => $v ) {
				$a = $arbol [$i];
				if ($i == 0) {
					if (! isset ( $cR [$a ["id"]] )) {
						$ret [$a ["id"]] = array (
								"datos" => array (
										"nombre" => $a ["nombre"],
										"cantidad" => $a ["cantidad"],
										"url" => $a ["url"],
										"nivel" => $a ["nivel"],
										"padre" => $a ["padre"] 
								),
								"hijos" => array () 
						);
						$cR [$a ["id"]] = &$ret [$a ["id"]];
					}
				} else {
					if (isset ( $cR [$a ["padre"]] )) {
						if (! isset ( $cR [$a ["padre"]] ["hijos"] [$a ["id"]] )) {
							$cR [$a ["padre"]] ["hijos"] [$a ["id"]] = array (
									"datos" => array (
											"nombre" => $a ["nombre"],
											"cantidad" => $a ["cantidad"],
											"url" => $a ["url"],
											"nivel" => $a ["nivel"],
											"padre" => $a ["padre"] 
									),
									"hijos" => array () 
							);
							$cR [$a ["id"]] = &$cR [$a ["padre"]] ["hijos"] [$a ["id"]];
						}
					}
				}
			}
		}
		$this->calcularCantidades ( $ret, $cantidades );
		if ($categoria) {
			if (isset ( $ret [$categoria] )) {
				foreach ( $ret [$categoria] ["hijos"] as &$r ) {
					$r ["hijos"] = array ();
				}
			} else {
				$ret = ($this->encuentraHijo ( $ret, $categoria ));
				$ret [$categoria] ["datos"] ["nivel"] = 1;
			}
		} else if ($dosniveles) {
			foreach ( $ret as $c => &$v ) {
				foreach ( $v ["hijos"] as &$vv ) {
					$vv ["hijos"] = array ();
				}
			}
		} else {
			foreach ( $ret as $c => &$v ) {
				$v ["hijos"] = array ();
			}
		}
		
		return $ret;
	}
	private function ordenarArbol(&$arbol) {
		if ($arbol && is_array ( $arbol ) && count ( $arbol ) > 0) {
			$keys = array_keys ( $arbol );
			$values = array_values ( $arbol );
			$cantidad = count ( $arbol );
			
			for($i = 0; $i < $cantidad; $i ++) {
				for($j = $i; $j < $cantidad; $j ++) {
					$ini = intval ( $values [$i] ["datos"] ["cantidad"] );
					$inik = $keys [$i];
					$valk = $keys [$j];
					$val = intval ( $values [$j] ["datos"] ["cantidad"] );
					if ($ini < $val) {
						$aux = $values [$i];
						$auxk = $keys [$i];
						$values [$i] = $values [$j];
						$keys [$i] = $keys [$j];
						$values [$j] = $aux;
						$keys [$j] = $auxk;
					}
				}
			}
			
			foreach ( $values as &$a ) {
				$a ["hijos"] = $this->ordenarArbol ( $a ["hijos"] );
			}
			return array_combine ( $keys, $values );
		}
		return array ();
	}
	private function encuentraHijo($arbol, $categoria) {
		if ($arbol && is_array ( $arbol ) && count ( $arbol ) > 0) {
			if (isset ( $arbol [$categoria] )) {
				return array (
						$categoria => $arbol [$categoria] 
				);
			} else {
				foreach ( $arbol as $id => $v ) {
					if ($c = $this->encuentraHijo ( $v ["hijos"], $categoria )) {
						return $c;
					}
				}
			}
		}
		return false;
	}
	private function darPadres($id, &$contados) {
		$CI = &get_instance ();
		$CI->load->model ( "Categoria_model", "categoria" );
		$arbol = array ();
		do {
			
			if (! isset ( $contados [$id] )) {
				$this->db->where ( array (
						"id" => $id 
				) );
				$resp = $this->darUno ( "categoria" );
				if ($resp) {
					$cn = $CI->categoria->darCategoriaNombre ( $id, $this->idioma->language->id );
					$contados [$id] = array (
							"nombre" => $cn->nombre,
							"cantidad" => $resp->cantidad,
							"url" => $cn->url_amigable,
							"nivel" => $resp->nivel,
							"padre" => $resp->padre,
							"id" => $resp->id 
					);
					array_unshift ( $arbol, $contados [$id] );
					$id = $resp->padre;
				} else {
					$id = false;
				}
			} else {
				array_unshift ( $arbol, $contados [$id] );
				$id = $contados [$id] ["padre"];
			}
		} while ( $id );
		return $arbol;
	}
	private function darDatosCategorias($categorias = false) {
		$CI = &get_instance ();
		$CI->load->model ( "Categoria_model", "categoria" );
		$ret = array ();
		if ($categorias && is_array ( $categorias ) && count ( $categorias ) > 0) {
			$cs = array ();
			$cantidades = array ();
			foreach ( $categorias as $c ) {
				$cs [] = $c->categoria;
				$cantidades [$c->categoria] = $c->cantidad;
			}
			$this->db->where_in ( "id", $cs );
			$datos = $this->darTodos ( "categoria" );
			
			if ($datos) {
				foreach ( $datos as $d ) {
					$nc = $CI->categoria->darCategoriaNombre ( $d->id, $this->idioma->language->id );
					if ($nc) {
						$ret [$d->id] = array (
								"datos" => array (
										"url" => $nc->url_amigable,
										"nombre" => $nc->nombre,
										"cantidad" => (isset ( $cantidades [$d->id] ) ? $cantidades [$d->id] : 0),
										"nivel" => $d->nivel 
								),
								"hijos" => array () 
						);
					}
				}
				foreach ( $datos as $d ) {
					if (! isset ( $ret [$d->padre] )) {
						$padre = $CI->categoria->darCategoriasX ( array (
								"categoria.id" => $d->padre 
						) );
						if ($padre && is_array ( $padre ) && count ( $padre ) > 0) {
							$padre = $padre [0];
							$nc = $CI->categoria->darCategoriaNombre ( $padre->id, $this->idioma->language->id );
							if ($nc) {
								$x = $ret [$d->id];
								$x ["datos"] ["nivel"] = 2;
								unset ( $ret [$d->id] );
								$ret [$padre->id] = array (
										"datos" => array (
												"url" => $nc->url_amigable,
												"nombre" => $nc->nombre,
												"cantidad" => $x ["datos"] ["cantidad"],
												"nivel" => 1 
										),
										"hijos" => array (
												$d->id => $x 
										) 
								);
							}
						}
					} else {
						$ret [$padre->id] ["hijos"] [$d->id] = $ret [$d->id];
						$ret [$padre->id] ["datos"] ["cantidad"] += $ret [$d->id] ["datos"] ["cantidad"];
						unset ( $ret [$d->id] );
					}
				}
			}
		}
		return $ret;
	}
	private function darCategorias($categoria = false) {
		$CI = &get_instance ();
		$CI->load->model ( "Categoria_model", "categoria" );
		$ret = array ();
		if (! $categoria) {
			$datos = $CI->categoria->darCategoriasX ( array (
					"nivel" => 1,
					"activo" => 1,
					"cantidad >" => 0 
			) );
			if ($datos) {
				foreach ( $datos as $d ) {
					$nc = $CI->categoria->darCategoriaNombre ( $d->id, $this->idioma->language->id );
					if ($nc) {
						$ret [$d->id] = array (
								"datos" => array (
										"url" => $nc->url_amigable,
										"nombre" => $nc->nombre,
										"cantidad" => $d->cantidad,
										"nivel" => $d->nivel 
								),
								"hijos" => array () 
						);
					}
				}
			}
		} else {
			$datos = $CI->categoria->darCategoriasX ( array (
					"categoria.id" => $categoria,
					"activo" => 1,
					"cantidad >" => 0 
			) );
			if ($datos) {
				$d = $datos [0];
				$nc = $CI->categoria->darCategoriaNombre ( $d->id, $this->idioma->language->id );
				$x = $d;
				if ($nc) {
					$ret [$x->id] = array (
							"datos" => array (
									"url" => $nc->url_amigable,
									"nombre" => $nc->nombre,
									"cantidad" => $d->cantidad,
									"nivel" => 1 
							),
							"hijos" => array () 
					);
					$hijos = $CI->categoria->darCategoriasX ( array (
							"padre" => $categoria,
							"activo" => 1,
							"cantidad >" => 0 
					) );
					if ($hijos) {
						$hs = array ();
						foreach ( $hijos as $d ) {
							$nc = $CI->categoria->darCategoriaNombre ( $d->id, $this->idioma->language->id );
							if ($nc) {
								$hs [$d->id] = array (
										"datos" => array (
												"url" => $nc->url_amigable,
												"nombre" => $nc->nombre,
												"cantidad" => $d->cantidad,
												"nivel" => 2 
										),
										"hijos" => array () 
								);
							}
						}
						$ret [$x->id] ["hijos"] = $hs;
					}
				}
			}
		}
		
		return $ret;
	}
	function totalVentas($usuario) {
		$sql = "select sum(total) as  total from (
		(select count(total) as total from (SELECT count(id) as total  FROM `articulo` WHERE usuario='$usuario' and estado in('Sin gastos Envio','Sin Envio') group by paquete)as x)
		union
		(select count(total) as total from (SELECT count(transaccion.id) as total FROM `transaccion` inner join articulo on articulo.id=transaccion.articulo WHERE articulo.usuario='$usuario' and transaccion.estado in('Sin gastos Envio','Sin Envio') group by transaccion.paquete)as z)
		union
		(SELECT count(oferta.id)as total FROM `oferta` inner join articulo on articulo.id=oferta.articulo and  articulo.usuario='$usuario' and articulo.tipo='Fijo' and articulo.terminado=0 where oferta.estado='Pendiente')
		)as y";
		$r = $this->db->query ( $sql )->result ();
		if ($r && is_array ( $r ) && count ( $r ) > 0) {
			return $r [0]->total;
		}
		return 0;
	}
	function soloPendientes($usuario) {
		$sql = "select sum(total) as  total from (
		(select count(total) as total from (SELECT count(id) as total  FROM `articulo` WHERE usuario='$usuario' and estado in('Sin gastos Envio','Sin Envio') group by paquete)as x)
		union
		(select count(total) as total from (SELECT count(transaccion.id) as total FROM `transaccion` inner join articulo on articulo.id=transaccion.articulo WHERE articulo.usuario='$usuario' and transaccion.estado in('Sin gastos Envio','Sin Envio') group by transaccion.paquete)as z)
		)as y";
		$r = $this->db->query ( $sql )->result ();
		if ($r && is_array ( $r ) && count ( $r ) > 0) {
			return $r [0]->total;
		}
		return 0;
	}
	function totalMensajes($usuario) {
		// return count ( $this->cargarMensaje ( $usuario, "Pendiente" ) );
		$this->load->model ( "usuario_model", "usuarioM" );
		$usuarioM = $this->usuarioM->darUsuarioXId ( $usuario );
		
		$sql = "select sum(total)as total from ( (select count(id) as total from ((SELECT id FROM `mensaje` WHERE emisor='$usuario' and estado_receptor='Pendiente' and (visible=0 or visible='$usuario')  group by receptor)union(
		SELECT id FROM `mensaje` WHERE receptor='$usuario' and estado='Pendiente' and (visible=0 or visible='$usuario')   group by emisor
		))as x)
		union
		(select count(id) as total from ((SELECT id FROM `notificacion` WHERE isnull(receptor) and id not in(select notificacion from notificacion_leido where usuario='$usuario' and visible<>0) and fecha>='$usuarioM->registro')
		union
		(SELECT id FROM `notificacion` WHERE receptor='$usuario' and id not in(select notificacion from notificacion_leido where usuario='$usuario' and visible<>0) and fecha>='$usuarioM->registro')) as x)) as y";
		// print $sql;
		$r = $this->db->query ( $sql )->result ();
		if ($r && is_array ( $r ) && count ( $r ) > 0) {
			return $r [0]->total;
		}
		return 0;
	}
	function totalCompras($usuario) {
		$sql = "select count(total) as total from (

		select count(id) as total,paquete from(

		(SELECT id,paquete FROM `articulo` WHERE comprador='$usuario' and estado in('Sin Pago','Enviado'))
		union
		(SELECT id,paquete FROM `transaccion` WHERE comprador='$usuario' and estado in('Sin Pago','Enviado'))

		) as x group by paquete

		) as y;";
		// print $sql;
		$r = $this->db->query ( $sql )->result ();
		if ($r && is_array ( $r ) && count ( $r ) > 0) {
			return $r [0]->total;
		}
		return 0;
	}
	function totalSeguimientos($usuario) {
		$sql = "select count(id) as total from(select siguiendo.id,if(articulo.tipo='Subasta',articulo.duracion*86400-(unix_timestamp()-unix_timestamp(articulo.fecha_registro))," . $this->configuracion->variables ( "vencimientoOferta" ) . "*86400-(unix_timestamp()-unix_timestamp(articulo.fecha_registro))) as tiempo
		from siguiendo inner join articulo on siguiendo.articulo=articulo.id where siguiendo.usuario='$usuario' and articulo.terminado=0
		having tiempo<=" . (floatval ( $this->configuracion->variables ( "notificacionSeguimiento" ) ) * 3600) . " and tiempo>0) as x";
		$r = $this->db->query ( $sql )->result ();
		if ($r && is_array ( $r ) && count ( $r ) > 0) {
			return $r [0]->total;
		}
		return 0;
	}
	function totalCuentas($usuario) {
		$sql = "select count(id) as  total from factura where usuario='$usuario' and estado='Pendiente'";
		$r = $this->db->query ( $sql )->result ();
		if ($r && is_array ( $r ) && count ( $r ) > 0) {
			return $r [0]->total;
		}
		return 0;
	}
	function darTransaccion($trasaccion) {
		$r = $this->db->where ( array (
				"id" => $trasaccion 
		) )->get ( "transaccion" )->result ();
		if ($r && is_array ( $r ) && count ( $r ) > 0) {
			return $r [0];
		}
		return false;
	}
	function prepararArticulosXVendidosFecha($usuario, $pending = false) {
		if ($usuario) {
			$orderby = $this->input->get ( "orderby" );
			$asc = $this->input->get ( "asc" );
			$who = $this->input->get ( "who" );
			
			if ($who !== "selled") {
				$orderby = "time";
				$asc = "desc";
			}
			$wextra = "";
			if ($pending) {
				$wextra = " and estado in ('Sin gastos Envio','Sin Envio')";
			}
			
			$vencimientoOferta = intval ( $this->configuracion->variables ( "vencimientoOferta" ) ) * 86400;
			$sextra = ",if(isnull(articulo.precio_oferta),articulo.precio,articulo.precio_oferta) as precio";
			$sextra2 = ",articulo.precio";
			$fextra = "";
			switch ($asc) {
				case "asc" :
					$asc = "asc";
					break;
				default :
					$asc = "desc";
					break;
			}
			switch ($orderby) {
				case "charge" :
					// 1-,
					// 2-,3-
					$sextra .= ",if(articulo.estado='Sin Pago',1,if(articulo.estado='Sin Envio',2,3)) as aEstado";
					$sextra2 .= ",if(transaccion.estado='Sin Pago',1,if(transaccion.estado='Sin Envio',2,3)) as aEstado";
					$orderby = "aEstado $asc,paquete desc,fecha_terminado ";
					break;
				case "shipping" :
					// 1-,
					// 2-,3
					$sextra .= ",if(articulo.estado='Sin Envio',1,if(articulo.estado='Enviado',2,3)) as aEstado";
					$sextra2 .= ",if(transaccion.estado='Sin Envio',1,if(transaccion.estado='Enviado',2,3)) as aEstado";
					$orderby = "aEstado $asc,paquete desc,fecha_envio ";
					break;
				case "time" :
					$sextra .= ",ordernarFechaVendidos(articulo.paquete,'$asc',0) as fecha_conjunta";
					$sextra2 .= ",ordernarFechaVendidos(transaccion.paquete,'$asc',1) as fecha_conjunta";
					$orderby = "fecha_conjunta $asc,paquete asc,fecha_terminado ";
					break;
				case "price" :
					$sextra .= ",ordernarPrecioVendidos(articulo.paquete) as precio_total";
					$sextra2 .= ",ordernarPrecioVendidos(transaccion.paquete) as precio_total";
					$orderby = "precio_total $asc,paquete asc,precio_total ";
					break;
				default :
					$sextra .= ",if(articulo.estado='Sin gastos Envio',1,if(articulo.estado='Sin Pago',2,3)) as aEstado";
					$sextra2 .= ",if(transaccion.estado='Sin gastos Envio',1,if(transaccion.estado='Sin Pago',2,3)) as aEstado";
					$orderby = "aEstado $asc,paquete desc,gastos_envio ";
					break;
			}
			return "select * from ((SELECT
			null as transaccion,
			articulo.cantidad,
			articulo.id,
			articulo.titulo,
			articulo.tipo,
			articulo.fecha_registro,
			articulo.duracion,
			articulo.usuario,
			articulo.foto,
			(if(isnull(articulo.paquete),null,(select gastos_envio from paquete where paquete.id=articulo.paquete)))as gastos_envio,
			articulo.estado,
			articulo.comprador,
			articulo.fecha_terminado,
			articulo.paquete,
			articulo.pagos,
			(if(isnull(articulo.paquete),null,(select fecha_disputa1 from paquete where paquete.id=articulo.paquete)))as fecha_disputa1,
			(if(isnull(articulo.paquete),null,(select fecha_disputa2 from paquete where paquete.id=articulo.paquete)))as fecha_disputa2,
			(if(isnull(articulo.paquete),null,(select fecha_disputa3 from paquete where paquete.id=articulo.paquete)))as fecha_disputa3,
			(if(isnull(articulo.paquete),null,(select fecha_disputa4 from paquete where paquete.id=articulo.paquete)))as fecha_disputa4,
			(if(isnull(articulo.paquete),null,(select tipo_pago from paquete where paquete.id=articulo.paquete)))as tipo_pago,
			(if(isnull(articulo.paquete),null,(select fecha_pago from paquete where paquete.id=articulo.paquete)))as fecha_pago,
			(if(isnull(articulo.paquete),null,(select denuncia1 from paquete where paquete.id=articulo.paquete)))as denuncia1,
			(if(isnull(articulo.paquete),null,(select fecha from paquete where paquete.id=articulo.paquete)))as fecha_paquete,
			(if(isnull(articulo.paquete),null,(select denuncia2 from paquete where paquete.id=articulo.paquete)))as denuncia2 ,
			(if(isnull(articulo.paquete),null,(select denuncia3 from paquete where paquete.id=articulo.paquete)))as denuncia3,
			(if(isnull(articulo.paquete),null,(select denuncia4 from paquete where paquete.id=articulo.paquete)))as denuncia4,
			(if(isnull(articulo.paquete),null,(select fecha_envio from paquete where paquete.id=articulo.paquete)))as fecha_envio
			$sextra


			FROM articulo
			INNER JOIN usuario ON usuario.id=articulo.usuario and usuario.id='$usuario'

			WHERE terminado = 1 and articulo.estado<>'A la venta' and articulo.estado<>'Finalizado' ) union (
			select

			transaccion.id as transaccion,
			transaccion.cantidad,
			articulo.id,
			articulo.titulo,
			articulo.tipo,
			articulo.fecha_registro,
			articulo.duracion,
			articulo.usuario,
			articulo.foto,
			(if(isnull(transaccion.paquete),null,(select gastos_envio from paquete where paquete.id=transaccion.paquete)))as gastos_envio,
			transaccion.estado,
			transaccion.comprador,
			transaccion.fecha_terminado,
			transaccion.paquete,
			articulo.pagos,
			(if(isnull(transaccion.paquete),null,(select fecha_disputa1 from paquete where paquete.id=transaccion.paquete)))as fecha_disputa1,
			(if(isnull(transaccion.paquete),null,(select fecha_disputa2 from paquete where paquete.id=transaccion.paquete)))as fecha_disputa2,
			(if(isnull(transaccion.paquete),null,(select fecha_disputa3 from paquete where paquete.id=transaccion.paquete)))as fecha_disputa3,
			(if(isnull(transaccion.paquete),null,(select fecha_disputa4 from paquete where paquete.id=transaccion.paquete)))as fecha_disputa4,
			(if(isnull(transaccion.paquete),null,(select tipo_pago from paquete where paquete.id=transaccion.paquete)))as tipo_pago,
			(if(isnull(transaccion.paquete),null,(select fecha_pago from paquete where paquete.id=transaccion.paquete)))as fecha_pago,
			(if(isnull(transaccion.paquete),null,(select denuncia1 from paquete where paquete.id=transaccion.paquete)))as denuncia1,
			(if(isnull(transaccion.paquete),null,(select fecha from paquete where paquete.id=transaccion.paquete)))as fecha_paquete,
			(if(isnull(transaccion.paquete),null,(select denuncia2 from paquete where paquete.id=transaccion.paquete)))as denuncia2 ,
			(if(isnull(transaccion.paquete),null,(select denuncia3 from paquete where paquete.id=transaccion.paquete)))as denuncia3,
			(if(isnull(transaccion.paquete),null,(select denuncia4 from paquete where paquete.id=transaccion.paquete)))as denuncia4,
			(if(isnull(transaccion.paquete),null,(select fecha_envio from paquete where paquete.id=transaccion.paquete)))as fecha_envio
			$sextra2

			from transaccion
			inner join articulo on articulo.id=transaccion.articulo
			inner join usuario on articulo.usuario=usuario.id and usuario.id='$usuario'
			where transaccion.estado<>'Finalizado'
			))as x
			where 1 $wextra
			ORDER BY $orderby $asc ";
		}
		return false;
	}
	function adicionarVoto($usuario, $motivo, $cantidad = 1, $tipo = "positivo") {
		if ($tipo !== "positivo") {
			$tipo = "negativo";
		}
		$utipo = ucfirst ( $tipo );
		$tipo = strtolower ( $tipo );
		$CI = &get_instance ();
		$CI->load->model ( "Usuario_model", "usuario" );
		$usuario = $CI->usuario->darUsuarioXId ( $usuario );
		if ($usuario && $this->db->insert ( "voto", array (
				"usuario" => $usuario->id,
				"tipo" => $utipo,
				"asunto" => $motivo,
				"fecha" => date ( "Y-m-d H:i:s" ) 
		) )) {
			return $this->db->update ( "usuario", array (
					$tipo => intval ( $usuario->{$tipo} ) + $cantidad 
			), array (
					"id" => $usuario->id 
			) );
		}
		return false;
	}
	function confirmarRecepcion($tipo, $paquete) {
		if ($tipo == 3) {
			$this->db->where ( array (
					"id" => $paquete,
					"estado" => "Disputa" 
			) );
			
			$tipo = 1;
		} else {
			$this->db->where ( array (
					"id" => $paquete,
					"estado" => "Enviado" 
			) );
		}
		
		$res = $this->db->get ( "paquete" )->result ();
		
		if ($res && is_array ( $res ) && count ( $res ) > 0) {
			
			$res = $res [0];
			if ($tipo == 1) {
				$tipo = "Recibido";
				$this->adicionarVoto ( $res->vendedor, "Venta" );
				$this->adicionarVoto ( $res->comprador, "Compra" );
			} else {
				$tipo = "Disputa";
				$this->db->insert ( "reporte", array (
						"asunto" => "Unmatch",
						"paquete" => $paquete,
						"fecha" => date ( "Y-m-d H-i-s" ),
						"perfil" => $res->vendedor,
						"usuario" => $res->comprador,
						"estado" => "Pendiente" 
				) );
				
				$idreporte = $this->db->insert_id ();
				
				// mardar email aqui, caso: disputa 4
				$articulosdatos = $this->darPaquete ( $paquete );
				
				$this->load->model ( 'usuario_model', 'objusuario' );
				
				if ($articulosdatos->articulos) {
					foreach ( explode ( ",", $articulosdatos->articulos ) as $t ) {
						// $ar [] = $this->darArticulo ( $t );
						// $ar [] = $this->CI->articulo->darArticulo ( $t );
						
						$a = $this->darArticulo ( $t );
						if ($a) {
							$ar [] = array (
									"id" => $a->id,
									"titulo" => $a->titulo,
									"cantidad" => 1 
							);
						}
					}
				} else {
					foreach ( explode ( ",", $articulosdatos->transacciones ) as $t ) {
						// $tran = $this->darTransaccion ( $t );
						// $ar [] = $this->darArticulo ( $tran->articulo );
						
						$t = $this->darTransaccion ( $t );
						if ($t) {
							$a = $this->darArticulo ( $t->articulo );
							if ($a) {
								$ar [] = array (
										"id" => $a->id,
										"titulo" => $a->titulo,
										"cantidad" => $t->cantidad 
								);
							}
						}
					}
				}
				
				$vendedor = $this->objusuario->darUsuarioXId ( $res->vendedor );
				$comprador = $this->objusuario->darUsuarioXId ( $res->comprador );
				$xx = array (
						"vendedor" => $vendedor,
						"comprador" => $comprador,
						"articulo" => $ar,
						"idreporte" => $idreporte 
				);
				$yy = array (
						
						"vendedor" => $vendedor,
						"comprador" => $comprador,
						"articulo" => $ar,
						"idreporte" => $idreporte 
				);
				$this->load->library ( "Myemail" );
				$this->enviarMensajeDisputa ( $vendedor->id, "mail/disputa-articulo-diferente-vendedor", $xx );
				$this->myemail->enviarTemplate ( $vendedor->email, "Disputa $idreporte por artículo diferente a su descripción", "mail/disputa-articulo-diferente-vendedor", array (
						"articulo" => $ar,
						"vendedor" => $vendedor,
						"comprador" => $comprador,
						"idreporte" => $idreporte 
				) );
				$this->enviarMensajeDisputa ( $comprador->id, "mail/disputa-articulo-diferente-comprador", $xx );
				$this->myemail->enviarTemplate ( $comprador->email, "Disputa $idreporte por artículo diferente a su descripción", "mail/disputa-articulo-diferente-comprador", array (
						"articulo" => $ar,
						"vendedor" => $vendedor,
						"comprador" => $comprador,
						"idreporte" => $idreporte 
				) );
				// fin mardar email, caso: disputa 4
			}
			
			if ($tipo != "Disputa") {
				
				$paquetedatos = $this->darPaquete ( $paquete );
				
				if ($paquetedatos->denuncia4 == 1) {
					$this->db->update ( "reporte", array (
							"estado" => "Finalizado" 
					), array (
							"paquete" => $paquete,
							"asunto" => "Unmatch" 
					) );
					if ($paquetedatos->transacciones != '') {
						$this->db->update ( "transaccion", array (
								"estado" => "Recibido" 
						), array (
								"paquete" => $paquete 
						) );
					}
				}
				
				$this->db->update ( "paquete", array (
						"estado" => $tipo,
						"fecha_recibido" => date ( "Y-m-d H:i:s" ),
						"denuncia2" => 0,
						"fecha_disputa1" => null,
						"denuncia3" => 0,
						"fecha_disputa2" => null,
						"denuncia4" => 0,
						"fecha_denuncia4" => null,
						"fecha_disputa3" => null 
				), array (
						"id" => $paquete 
				) );
			} else {
				$this->db->update ( "paquete", array (
						"estado" => $tipo,
						"fecha_recibido" => date ( "Y-m-d H:i:s" ),
						"denuncia2" => 0,
						"fecha_disputa1" => null,
						"denuncia3" => 0,
						"fecha_disputa2" => null,
						"denuncia4" => 1,
						"fecha_denuncia4" => date ( "Y-m-d H:i:s" ),
						"fecha_disputa3" => null 
				), array (
						"id" => $paquete 
				) );
			}
			
			if (true) {
				if (trim ( $res->articulos ) !== "") {
					$articulos = explode ( ",", $res->articulos );
					foreach ( $articulos as $a ) {
						$this->db->update ( "articulo", array (
								"estado" => $tipo 
						), array (
								"id" => $a 
						) );
					}
				}
				if (trim ( $res->transacciones ) !== "") {
					$transacciones = explode ( ",", $res->transacciones );
					foreach ( $transacciones as $t ) {
						$this->db->update ( "transaccion", array (
								"estado" => $tipo 
						), array (
								"id" => $t 
						) );
					}
				}
				return true;
			}
		}
		return false;
	}
	function enviarMensajeDisputa($id, $template, $params) {
		$CI = &get_instance ();
		$mensaje = $CI->load->view ( $template, $params, true );
		$re = $CI->db->select ( "id" )->where ( array (
				"tipo" => "Administrador" 
		) )->get ( "usuario", 1, 0 )->result ();
		if ($re && is_array ( $re ) && count ( $re ) > 0) {
			$CI->db->insert ( "notificacion", array (
					"emisor" => $re [0]->id,
					"receptor" => $id,
					"mensaje" => $mensaje,
					"fecha" => date ( "Y-m-d H:i:s" ) 
			) );
		}
	}
	function listarArticulosEnviados($usuario) {
		$r = $this->db->query ( "select articulo.* from articulo inner join paquete on paquete.id=articulo.paquete and not isnull(paquete.fecha_envio) where usuario='$usuario'  " )->result ();
		if ($r && is_array ( $r ) && count ( $r ) > 0) {
			return $r;
		}
		return false;
	}
	function sumarArticulosEnVentaFijo($usuario) {
		$r = $this->db->query ( "select sum(if(tipo='Cantidad',precio*cantidad,precio)) as precio from articulo where terminado=0 and (tipo='Fijo' or tipo='Cantidad') and usuario='$usuario'" )->result ();
		if ($r && is_array ( $r ) && count ( $r ) > 0) {
			return $r [0]->precio;
		}
		return false;
	}
	function usuariosConVentas($mes, $anio) {
		return $this->db->query ( "select usuario.id,usuario.seudonimo,usuario.nueva_tarifa from usuario inner join paquete on usuario.id=paquete.vendedor and paquete.fecha_envio between '$anio-$mes-01 00:00:00' and '$anio-$mes-" . date ( "t", strtotime ( date ( "$anio-$mes-01" ) ) ) . " 23:59:59' group by usuario.id" )->result ();
	}
	function enviarNotificacionCambioTarifaPlana($usuario, $total, $pos) {
		if ($pos > 0) {
			$CI = &get_instance ();
			
			$CI->load->library ( "Myemail" );
			$CI->myemail->enviarTemplate ( $usuario->email, traducir ( "Su tarifa cambio" ), "mail/notificacion-cambio-tarifa-plana", $params = array (
					"total" => $total,
					"tarifa_anterior" => self::$tarifa ["Plana"] [$pos - 1] ["nombre"],
					"monto_anterior" => self::$tarifa ["Plana"] [$pos - 1] ["monto"],
					"final_anterior" => self::$tarifa ["Plana"] [$pos] ["inicio"],
					"tarifa_actual" => self::$tarifa ["Plana"] [$pos] ["nombre"],
					"monto_actual" => self::$tarifa ["Plana"] [$pos] ["monto"] 
			) );
		}
	}
	function confirmarEnvio($paquete) {
		if ($paquete) {
			$paquete = $this->darPaquete ( $paquete );
			if ($paquete) {
				$articulo = array ();
				if (trim ( $paquete->articulos ) !== "") {
					$articulo = explode ( ",", $paquete->articulos );
				}
				$transaccion = array ();
				if (trim ( $paquete->transacciones ) !== "") {
					$transaccion = explode ( ",", $paquete->transacciones );
				}
				$this->db->update ( "paquete", array (
						"estado" => "Enviado",
						"fecha_envio" => date ( "Y-m-d H:i:s" ),
						"denuncia3" => 0 
				), array (
						"id" => $paquete->id 
				) );
				
				$CI = &get_instance ();
				$vendedor = $CI->usuario->darUsuarioXId ( $paquete->vendedor );
				if ($vendedor && (count ( $articulo ) > 0 || count ( $transaccion ) > 0)) {
					if ($vendedor->tipo_tarifa == "Comision") {
						if (count ( $articulo ) > 0) {
							$articulos = $this->db->where_in ( "id", $articulo )->get ( "articulo" )->result ();
							
							foreach ( $articulos as $a ) {
								$tarifa = $this->calcularTarifa ( $a, $vendedor->tipo_tarifa );
								$this->db->insert ( "cuenta", array (
										"articulo" => $a->id,
										"paquete" => $a->paquete,
										"monto" => $tarifa,
										"fecha" => date ( "Y-m-d H:i:s" ),
										"usuario" => $vendedor->id 
								) );
							}
						}
						if (count ( $transaccion ) > 0) {
							$transacciones = $this->db->where_in ( "id", $transaccion )->get ( "transaccion" )->result ();
							foreach ( $transacciones as $t ) {
								$tarifa = $this->calcularTarifa ( $t, $vendedor->tipo_tarifa, false, true );
								$this->db->insert ( "cuenta", array (
										"articulo" => $t->articulo,
										"paquete" => $t->paquete,
										"monto" => $tarifa,
										"fecha" => date ( "Y-m-d H:i:s" ),
										"usuario" => $vendedor->id,
										"cantidad" => $t->cantidad 
								) );
							}
						}
					}
				}
				if ($articulo && is_array ( $articulo ) && count ( $articulo ) > 0) {
					foreach ( $articulo as $a ) {
						$this->db->update ( "articulo", array (
								"estado" => "Enviado" 
						), array (
								"id" => $a 
						) );
					}
				}
				if ($transaccion && is_array ( $transaccion ) && count ( $transaccion ) > 0) {
					foreach ( $transaccion as $t ) {
						$this->db->update ( "transaccion", array (
								"estado" => "Enviado" 
						), array (
								"id" => $t 
						) );
					}
				}
				return true;
			}
		}
		return false;
	}
	function calcularTarifa($a, $tipo_tarifa, $u = false, $transaccion = false) {
		$tarifa = self::$tarifa;
		
		$mtarifa = 0;
		$monto = 0;
		$acumulativo = 0;
		if (! isset ( $tarifa [$tipo_tarifa] )) {
			return 0;
		}
		if ($tipo_tarifa == "Comision") {
			if (! $transaccion) {
				$precio = floatval ( $a->precio_oferta ? $a->precio_oferta : $a->precio );
				$tipo = $a->tipo;
			} else {
				$precio = floatval ( $a->precio * $a->cantidad );
				$tipo = "Fijo";
			}
			
			foreach ( $tarifa [$tipo_tarifa] [$tipo] as $i => $t ) {
				if ($i < count ( $tarifa [$tipo_tarifa] [$tipo] ) - 1) {
					if ($precio < floatval ( $tarifa [$tipo_tarifa] [$tipo] [$i + 1] ["inicio"] ) && $precio >= floatval ( $tarifa [$tipo_tarifa] [$tipo] [$i] ["inicio"] )) {
						$monto += ($precio - $acumulativo) * floatval ( $tarifa [$tipo_tarifa] [$tipo] [$i] ["porcentaje"] ) / 100;
					} elseif ($precio >= floatval ( $tarifa [$tipo_tarifa] [$tipo] [$i + 1] ["inicio"] )) {
						$monto += (floatval ( $tarifa [$tipo_tarifa] [$tipo] [$i + 1] ["inicio"] ) - $acumulativo) * floatval ( $tarifa [$tipo_tarifa] [$tipo] [$i] ["porcentaje"] ) / 100;
					}
					$acumulativo = floatval ( $tarifa [$tipo_tarifa] [$tipo] [$i + 1] ["inicio"] );
				} else {
					if ($precio >= floatval ( $tarifa [$tipo_tarifa] [$tipo] [$i] ["inicio"] )) {
						$monto += ($precio - $acumulativo) * floatval ( $tarifa [$tipo_tarifa] [$tipo] [$i] ["porcentaje"] ) / 100;
					}
				}
			}
		} else {
			$precio = $this->sumarArticulosEnVentaFijo ( $u );
			$costo = 0;
			foreach ( $tarifa [$tipo_tarifa] as $i => $t ) {
				if ($i < count ( $tarifa [$tipo_tarifa] ) - 1) {
					if ($precio < floatval ( $tarifa [$tipo_tarifa] [$i + 1] ["inicio"] ) && $precio >= floatval ( $tarifa [$tipo_tarifa] [$i] ["inicio"] )) {
						$costo = $tarifa [$tipo_tarifa] [$i] ["monto"];
					} elseif ($precio >= floatval ( $tarifa [$tipo_tarifa] [$i + 1] ["inicio"] )) {
						$costo = $tarifa [$tipo_tarifa] [$i] ["monto"];
					}
				} else if ($precio >= floatval ( $tarifa [$tipo_tarifa] [$i] ["inicio"] )) {
					$costo = $tarifa [$tipo_tarifa] [$i] ["monto"];
				}
			}
			return $costo;
		}
		return $monto;
	}
	function prepararArticulosXCompradosFecha($usuario, $pending = false) {
		if ($usuario) {
			$orderby = $this->input->get ( "orderby" );
			$asc = $this->input->get ( "asc" );
			$who = $this->input->get ( "who" );
			
			if ($who !== "buyed") {
				$orderby = "time";
				$asc = "desc";
			}
			$wextra = "";
			if ($pending) {
				$wextra = " and estado in ('Sin Pago','Enviado')";
			}
			$vencimientoOferta = intval ( $this->configuracion->variables ( "vencimientoOferta" ) ) * 86400;
			$sextra = ",if(isnull(articulo.precio_oferta),articulo.precio,articulo.precio_oferta) as precio";
			$sextra2 = ",transaccion.precio";
			$fextra = "";
			switch ($asc) {
				case "asc" :
					$asc = "asc";
					break;
				default :
					$asc = "desc";
					break;
			}
			switch ($orderby) {
				case "charge" :
					// 1-,
					// 2-,3-
					$sextra .= ",if(articulo.estado='Sin Pago',1,if(articulo.estado='Sin Envio',2,3)) as aEstado";
					$sextra2 .= ",if(transaccion.estado='Sin Pago',1,if(transaccion.estado='Sin Envio',2,3)) as aEstado";
					$orderby = "aEstado $asc,paquete desc,fecha_terminado ";
					break;
				case "shipping" :
					// 1-,
					// 2-,3
					$sextra .= ",if(articulo.estado='Sin Envio',1,if(articulo.estado='Enviado',2,3)) as aEstado";
					$sextra2 .= ",if(transaccion.estado='Sin Envio',1,if(transaccion.estado='Enviado',2,3)) as aEstado";
					$orderby = "aEstado $asc,paquete desc,fecha_envio ";
					break;
				case "time" :
					$sextra .= ",ordernarFechaVendidos(articulo.paquete,'$asc',0) as fecha_conjunta";
					$sextra2 .= ",ordernarFechaVendidos(transaccion.paquete,'$asc',1) as fecha_conjunta";
					$orderby = "fecha_conjunta $asc,paquete asc,fecha_terminado ";
					break;
				case "price" :
					$sextra .= ",ordernarPrecioVendidos(articulo.paquete) as precio_total";
					$sextra2 .= ",ordernarPrecioVendidos(transaccion.paquete) as precio_total";
					$orderby = "precio_total $asc,paquete asc,precio_total ";
					break;
				default :
					$sextra .= ",if(articulo.estado='Sin gastos Envio',1,2) as aEstado";
					$sextra2 .= ",if(transaccion.estado='Sin gastos Envio',1,2) as aEstado";
					$orderby = "aEstado $asc,gastos_envio $asc ,paquete ";
					break;
			}
			
			return "select * from ((SELECT
			null as transaccion,
			articulo.cantidad,
			articulo.id,
			articulo.titulo,
			articulo.tipo,
			articulo.fecha_registro,
			articulo.duracion,
			articulo.usuario,
			articulo.foto,
			(if(isnull(articulo.paquete),null,(select gastos_envio from paquete where paquete.id=articulo.paquete)))as gastos_envio,
			articulo.estado,
			articulo.comprador,
			articulo.fecha_terminado,
			articulo.paquete,
			articulo.pagos,
			(if(isnull(articulo.paquete),null,(select fecha_disputa1 from paquete where paquete.id=articulo.paquete)))as fecha_disputa1,
			(if(isnull(articulo.paquete),null,(select fecha_disputa2 from paquete where paquete.id=articulo.paquete)))as fecha_disputa2,
			(if(isnull(articulo.paquete),null,(select fecha_disputa3 from paquete where paquete.id=articulo.paquete)))as fecha_disputa3,
			(if(isnull(articulo.paquete),null,(select fecha_disputa4 from paquete where paquete.id=articulo.paquete)))as fecha_disputa4,
			(if(isnull(articulo.paquete),null,(select tipo_pago from paquete where paquete.id=articulo.paquete)))as tipo_pago,
			(if(isnull(articulo.paquete),null,(select fecha_pago from paquete where paquete.id=articulo.paquete)))as fecha_pago,
			(if(isnull(articulo.paquete),null,(select denuncia1 from paquete where paquete.id=articulo.paquete)))as denuncia1 ,
			(if(isnull(articulo.paquete),null,(select fecha from paquete where paquete.id=articulo.paquete)))as fecha_paquete,
			(if(isnull(articulo.paquete),null,(select denuncia2 from paquete where paquete.id=articulo.paquete)))as denuncia2 ,
			(if(isnull(articulo.paquete),null,(select denuncia3 from paquete where paquete.id=articulo.paquete)))as denuncia3,
			(if(isnull(articulo.paquete),null,(select denuncia4 from paquete where paquete.id=articulo.paquete)))as denuncia4,
			(if(isnull(articulo.paquete),null,(select fecha_envio from paquete where paquete.id=articulo.paquete)))as fecha_envio
			$sextra

			FROM articulo
			INNER JOIN usuario ON usuario.id=articulo.comprador and usuario.id='$usuario'
			WHERE terminado = 1 and articulo.estado<>'A la venta' and articulo.estado<>'Finalizado')
			union
			(SELECT
			transaccion.id as transaccion,
			transaccion.cantidad,
			articulo.id,
			articulo.titulo,
			articulo.tipo,
			articulo.fecha_registro,
			articulo.duracion,
			articulo.usuario,
			articulo.foto,
			(if(isnull(transaccion.paquete),null,(select gastos_envio from paquete where paquete.id=transaccion.paquete)))as gastos_envio,
			transaccion.estado,
			transaccion.comprador,
			transaccion.fecha_terminado,
			transaccion.paquete,
			articulo.pagos,
			(if(isnull(transaccion.paquete),null,(select fecha_disputa1 from paquete where paquete.id=transaccion.paquete)))as fecha_disputa1,
			(if(isnull(transaccion.paquete),null,(select fecha_disputa2 from paquete where paquete.id=transaccion.paquete)))as fecha_disputa2,
			(if(isnull(transaccion.paquete),null,(select fecha_disputa3 from paquete where paquete.id=transaccion.paquete)))as fecha_disputa3,
			(if(isnull(transaccion.paquete),null,(select fecha_disputa4 from paquete where paquete.id=transaccion.paquete)))as fecha_disputa4,
			(if(isnull(transaccion.paquete),null,(select tipo_pago from paquete where paquete.id=transaccion.paquete)))as tipo_pago,
			(if(isnull(transaccion.paquete),null,(select fecha_pago from paquete where paquete.id=transaccion.paquete)))as fecha_pago,
			(if(isnull(transaccion.paquete),null,(select denuncia1 from paquete where paquete.id=transaccion.paquete)))as denuncia1 ,
			(if(isnull(transaccion.paquete),null,(select fecha from paquete where paquete.id=transaccion.paquete)))as fecha_paquete,
			(if(isnull(transaccion.paquete),null,(select denuncia2 from paquete where paquete.id=transaccion.paquete)))as denuncia2 ,
			(if(isnull(transaccion.paquete),null,(select denuncia3 from paquete where paquete.id=transaccion.paquete)))as denuncia3,
			(if(isnull(transaccion.paquete),null,(select denuncia4 from paquete where paquete.id=transaccion.paquete)))as denuncia4,
			(if(isnull(transaccion.paquete),null,(select fecha_envio from paquete where paquete.id=transaccion.paquete)))as fecha_envio
			$sextra2

			FROM transaccion
			inner join articulo on articulo.id=transaccion.articulo
			INNER JOIN usuario ON usuario.id=transaccion.comprador and usuario.id='$usuario'
			where transaccion.estado<>'Finalizado'
			)) as x
			where 1 $wextra
			ORDER BY $orderby $asc ";
		}
		return false;
	}
	function prepararArticulosXEnCompraFecha($usuario) {
		if ($usuario) {
			$orderby = $this->input->get ( "orderby" );
			$asc = $this->input->get ( "asc" );
			$who = $this->input->get ( "who" );
			
			if ($who !== "on-buy") {
				$orderby = "time";
				$asc = "desc";
			}
			
			$vencimientoOferta = intval ( $this->configuracion->variables ( "vencimientoOferta" ) ) * 86400;
			$sextra = ",articulo.precio";
			$fextra = "";
			switch ($orderby) {
				case "title" :
					$orderby = "articulo.titulo";
					break;
				case "status" :
					// 1-maximo pujador,
					// 2-sobrepujado,3-ofertaenviada,4-ofertarechazada
					$sextra .= ",if(articulo.tipo='Subasta',if((select usuario from oferta where oferta.articulo=articulo.id order by monto_automatico desc,fecha asc limit 0,1)='$usuario',1,2),if((select estado from oferta where oferta.articulo=articulo.id and oferta.usuario='$usuario' order by monto desc limit 0,1 )='Pendiente',3,4))as oEstado";
					$orderby = "oEstado";
					break;
				case "price" :
					$sextra = ",if(articulo.tipo='Fijo',articulo.precio,mayorPuja(articulo.id)) as precio";
					$orderby = "precio";
					break;
				default :
					$sextra .= ",if(articulo.tipo='Fijo',unix_timestamp(articulo.fecha_registro)+ $vencimientoOferta - unix_timestamp(),unix_timestamp(articulo.fecha_registro)+articulo.duracion*86400 - unix_timestamp())as tiempo";
					$orderby = "tiempo";
					break;
			}
			switch ($asc) {
				case "asc" :
					$asc = "asc";
					break;
				default :
					$asc = "desc";
					break;
			}
			$vencimientoOferta = intval ( $this->configuracion->variables ( "vencimientoOferta" ) ) * 86400;
			return "SELECT articulo.cantidad,articulo.id,articulo.titulo,articulo.tipo,articulo.fecha_registro,articulo.duracion,articulo.usuario,articulo.foto,(select usuario from oferta where oferta.articulo=articulo.id order by monto desc limit 0,1) as maximoPujador ,(select estado from oferta where oferta.articulo=articulo.id and oferta.usuario='$usuario' order by monto desc limit 0,1) as estadoOferta $sextra
			FROM articulo
			inner join oferta on oferta.articulo=articulo.id and oferta.estado<>'Aceptado' and oferta.usuario='$usuario'
			WHERE terminado = 0 and articulo.estado='A la venta'
			group by articulo.id
			ORDER by $orderby $asc ";
		}
		return false;
	}
	function prepararArticulosXEnVentaFecha($usuario, $new = false) {
		if ($usuario) {
			$orderby = $this->input->get ( "orderby" );
			$asc = $this->input->get ( "asc" );
			$who = $this->input->get ( "who" );
			
			if ($who !== "on-sell") {
				$orderby = "time";
				$asc = "desc";
			}
			
			$vencimientoOferta = intval ( $this->configuracion->variables ( "vencimientoOferta" ) ) * 86400;
			$sextra = "";
			$fextra = "";
			switch ($orderby) {
				case "follower" :
					$orderby = "seguidores";
					break;
				case "deals" :
					$orderby = "nOfertas";
					$sextra .= ",(select count(oferta.id) from oferta inner join usuario on usuario.id=oferta.usuario and usuario.estado<>'Baneado' where articulo.id=oferta.articulo) as nOfertas";
					break;
				case "price" :
					$sextra .= ",if(articulo.tipo='Fijo',articulo.precio,mayorPuja(articulo.id)) as precio";
					$orderby = "precio";
					break;
				default :
					$sextra .= ",if(articulo.tipo='Fijo',unix_timestamp(articulo.fecha_registro)+ $vencimientoOferta - unix_timestamp(),unix_timestamp(articulo.fecha_registro)+articulo.duracion*86400 - unix_timestamp())as tiempo";
					$orderby = "tiempo";
					break;
			}
			switch ($asc) {
				case "asc" :
					$asc = "asc";
					break;
				default :
					$asc = "desc";
					break;
			}
			if ($new) {
				$fextra = "INNER JOIN (select count(oferta.id) as cantidad,oferta.articulo from oferta inner join articulo on oferta.articulo=articulo.id inner join usuario on usuario.id=oferta.usuario and usuario.estado<>'Baneado' where oferta.estado='Pendiente' and articulo.tipo='Fijo' group by oferta.articulo ) as s on s.articulo=articulo.id and s.cantidad>0";
				$sextra .= ",s.cantidad as ofertasPendientes";
			} else {
				$sextra .= ",(select count(oferta.id) from oferta inner join usuario on usuario.id=oferta.usuario and usuario.estado<>'Baneado' where oferta.articulo=articulo.id and oferta.estado='Pendiente') as ofertasPendientes";
			}
			return "SELECT articulo.cantidad,articulo.id,articulo.titulo,articulo.tipo,articulo.precio,articulo.fecha_registro,articulo.duracion,articulo.usuario,articulo.foto,(select count(siguiendo.id) from siguiendo where siguiendo.articulo=articulo.id) as seguidores $sextra
			FROM articulo
			INNER JOIN usuario ON usuario.id=articulo.usuario and usuario.id='$usuario'
			$fextra
			WHERE terminado = 0 and articulo.estado='A la venta'
			ORDER by $orderby $asc ";
		}
		return false;
	}
	function prepararArticulosXNoCompradosFecha($usuario) {
		if ($usuario) {
			$orderby = $this->input->get ( "orderby" );
			$asc = $this->input->get ( "asc" );
			$who = $this->input->get ( "who" );
			
			if ($who !== "no-buy") {
				$orderby = "time";
				$asc = "desc";
			}
			
			$vencimientoOferta = intval ( $this->configuracion->variables ( "vencimientoOferta" ) ) * 86400;
			$sextra = ",articulo.precio";
			$fextra = "";
			switch ($orderby) {
				case "title" :
					$orderby = "articulo.titulo";
					break;
				case "price" :
					$sextra = ",if(articulo.tipo='Fijo',articulo.precio,mayorPuja(articulo.id)) as precio";
					$orderby = "precio";
					break;
				default :
					$orderby = "articulo.terminado";
					break;
			}
			switch ($asc) {
				case "asc" :
					$asc = "asc";
					break;
				default :
					$asc = "desc";
					break;
			}
			return "SELECT articulo.id,articulo.titulo,articulo.tipo,articulo.fecha_registro,articulo.duracion,articulo.usuario,articulo.foto,articulo.fecha_terminado $sextra
			FROM articulo
			inner join oferta on oferta.articulo=articulo.id and oferta.estado<>'Aceptado' and oferta.usuario='$usuario'
			WHERE terminado = 1 and (articulo.estado='Sin gastos Envio' or articulo.estado='A la venta' or articulo.estado='Sin Pago' or articulo.estado='Finalizado') and (articulo.comprador<>'$usuario' or isnull(articulo.comprador))
			group by articulo.id
			ORDER by $orderby $asc ";
		}
		return false;
	}
	function prepararArticulosXNoVendidosFecha($usuario) {
		if ($usuario) {
			$orderby = $this->input->get ( "orderby" );
			$asc = $this->input->get ( "asc" );
			$who = $this->input->get ( "who" );
			if ($who !== "no-sell") {
				$orderby = "time";
				$asc = "desc";
			}
			$sextra = "";
			switch ($orderby) {
				case "title" :
					$orderby = "articulo.titulo";
					break;
				case "type" :
					$orderby = "articulo.tipo";
					break;
				case "price" :
					$sextra .= ",if(articulo.tipo='Fijo',articulo.precio,mayorPuja(articulo.id)) as precio";
					$orderby = "precio";
					break;
				default :
					$orderby = "articulo.terminado";
					break;
			}
			switch ($asc) {
				case "asc" :
					$asc = "asc";
					break;
				default :
					$asc = "desc";
					break;
			}
			
			$sql = "SELECT articulo.cantidad,articulo.id,articulo.titulo,articulo.tipo,articulo.precio,articulo.fecha_registro,articulo.duracion,articulo.usuario,articulo.foto,articulo.fecha_terminado $sextra
			FROM articulo
			INNER JOIN usuario ON usuario.id=articulo.usuario and usuario.id='$usuario'
			WHERE terminado = 1 and (articulo.estado='A la venta' or articulo.estado='Finalizado') and (articulo.tipo<>'Cantidad' or (articulo.tipo='Cantidad' && (articulo.cantidad>0 or
			(articulo.cantidad=0
			and articulo.id not in(select articulo from transaccion)
			and isnull((select id from paquete where articulos like articulo.id or articulos like concat(articulo.id,'%') or articulos like concat(articulo.id,',%') or articulos like concat('%,',articulo.id) or articulos like concat('%,',articulo.id,',%')))))))
			ORDER by $orderby $asc ";
			return $sql;
		}
		return false;
	}
	public function listarArticulosXCompradosFecha($usuario, $inicio, $total, $pending = false) {
		$query = $this->prepararArticulosXCompradosFecha ( $usuario, $pending );
		$res = $this->db->query ( $query );
		if ($res) {
			$totalRes = $res->num_rows ();
			if ($inicio < $totalRes) {
				$total = ($totalRes < $inicio + $total) ? $totalRes - $inicio : $total;
				$datos = array ();
				$grupos = 0;
				$count = $inicio;
				
				do {
					$ay = $res->row ( $count );
					$datos [] = $ay;
					if ($ay) {
						if ($count < $totalRes - 1) {
							$ax = $res->row ( $count + 1 );
							if ($ax && $ax->usuario != $ay->usuario) {
								$grupos ++;
							}
						}
					}
					$count ++;
				} while ( $count < $totalRes && $grupos + 1 <= $total );
				return array (
						$totalRes,
						$datos 
				);
			}
		}
		return false;
	}
	public function listarArticulosXVendidosFecha($usuario, $inicio, $total, $pending = false) {
		$query = $this->prepararArticulosXVendidosFecha ( $usuario, $pending );
		$res = $this->db->query ( $query );
		if ($res) {
			$totalRes = $res->num_rows ();
			if ($inicio < $totalRes) {
				$total = ($totalRes < $inicio + $total) ? $totalRes - $inicio : $total;
				$datos = array ();
				$grupos = 0;
				$count = $inicio;
				
				do {
					$ay = $res->row ( $count );
					$datos [] = $ay;
					if ($ay) {
						if ($count < $totalRes - 1) {
							$ax = $res->row ( $count + 1 );
							if ($ax->paquete) {
								if ($ax && $ax->paquete != $ay->paquete) {
									$grupos ++;
								}
							} else {
								if ($ax && $ax->comprador != $ay->comprador) {
									$grupos ++;
								}
							}
						}
					}
					$count ++;
				} while ( $count < $totalRes && $grupos + 1 <= $total );
				return array (
						$totalRes,
						$datos 
				);
			}
		}
		return false;
	}
	public function listarArticulosXEnCompraFecha($usuario, $new = false, $inicio, $total) {
		$query = $this->prepararArticulosXEnCompraFecha ( $usuario, $new );
		$res = $this->db->query ( $query );
		if ($res) {
			$totalRes = $res->num_rows ();
			if ($inicio < $totalRes) {
				$total = ($totalRes < $inicio + $total) ? $totalRes - $inicio : $total;
				$datos = array ();
				for($i = $inicio; $i < $inicio + $total; $i ++) {
					$datos [] = $res->row ( $i );
				}
				return array (
						$totalRes,
						$datos 
				);
			}
		}
		return false;
	}
	public function listarArticulosXEnVentaFecha($usuario, $new = false, $inicio, $total) {
		$query = $this->prepararArticulosXEnVentaFecha ( $usuario, $new );
		$res = $this->db->query ( $query );
		if ($res) {
			$totalRes = $res->num_rows ();
			if ($inicio < $totalRes) {
				$total = ($totalRes < $inicio + $total) ? $totalRes - $inicio : $total;
				$datos = array ();
				for($i = $inicio; $i < $inicio + $total; $i ++) {
					$datos [] = $res->row ( $i );
				}
				return array (
						$totalRes,
						$datos 
				);
			}
		}
		return false;
	}
	public function listarArticulosXNoCompradosFecha($usuario, $inicio, $total) {
		$query = $this->prepararArticulosXNoCompradosFecha ( $usuario );
		$res = $this->db->query ( $query );
		if ($res) {
			$totalRes = $res->num_rows ();
			if ($inicio < $totalRes) {
				$total = ($totalRes < $inicio + $total) ? $totalRes - $inicio : $total;
				$datos = array ();
				for($i = $inicio; $i < $inicio + $total; $i ++) {
					$datos [] = $res->row ( $i );
				}
				return array (
						$totalRes,
						$datos 
				);
			}
		}
		return false;
	}
	public function listarArticulosXNoVendidosFecha($usuario, $inicio, $total) {
		$query = $this->prepararArticulosXNoVendidosFecha ( $usuario );
		$res = $this->db->query ( $query );
		if ($res) {
			$totalRes = $res->num_rows ();
			if ($inicio < $totalRes) {
				$total = ($totalRes < $inicio + $total) ? $totalRes - $inicio : $total;
				$datos = array ();
				for($i = $inicio; $i < $inicio + $total; $i ++) {
					$datos [] = $res->row ( $i );
				}
				return array (
						$totalRes,
						$datos 
				);
			}
		}
		return false;
	}
	public function leerArticulosVendidos($usuario, $inicio = false, $preview = true, $pending = false) {
		if ($preview) {
			$totalpagina = $this->configuracion->variables ( "cantidadVendidos" );
		} else {
			$totalpagina = $this->configuracion->variables ( "cantidadPaginacion" );
		}
		if (! $inicio) {
			$inicio = 0;
		}
		
		$data ["inicio"] = $inicio;
		$data ["totalpagina"] = $totalpagina;
		$data ["finalVendidos"] = 0;
		$x = $this->listarArticulosXVendidosFecha ( $usuario, $inicio, $totalpagina, $pending );
		if ($x) {
			list ( $data ["totalVendidos"], $data ["articulosVendidos"] ) = $x;
			$data ["finalVendidos"] = $inicio + count ( $data ["articulosVendidos"] );
			$this->procesarArticulos ( $data ["articulosVendidos"] );
		}
		return $data;
	}
	public function leerArticulosComprados($usuario, $inicio = false, $preview = true, $pending = false) {
		if ($preview) {
			$totalpagina = $this->configuracion->variables ( "cantidadVendidos" );
		} else {
			$totalpagina = $this->configuracion->variables ( "cantidadPaginacion" );
		}
		if (! $inicio) {
			$inicio = 0;
		}
		
		$data ["inicio"] = $inicio;
		$data ["totalpagina"] = $totalpagina;
		$data ["finalComprados"] = 0;
		$x = $this->listarArticulosXCompradosFecha ( $usuario, $inicio, $totalpagina, $pending );
		if ($x) {
			list ( $data ["totalComprados"], $data ["articulosComprados"] ) = $x;
			$data ["finalComprados"] = $inicio + count ( $data ["articulosComprados"] );
			$this->procesarArticulos ( $data ["articulosComprados"] );
		}
		return $data;
	}
	public function leerArticulosEnCompra($usuario, $inicio = false, $preview = true, $new = false) {
		if ($preview) {
			$totalpagina = $this->configuracion->variables ( "cantidadVendidos" );
		} else {
			$totalpagina = $this->configuracion->variables ( "cantidadPaginacion" );
		}
		if (! $inicio) {
			$inicio = 0;
		}
		
		$data ["inicio"] = $inicio;
		$data ["totalpagina"] = $totalpagina;
		$data ["finalEnCompra"] = 0;
		$x = $this->listarArticulosXEnCompraFecha ( $usuario, $new, $inicio, $totalpagina );
		if ($x) {
			
			list ( $data ["totalEnCompra"], $data ["articulosEnCompra"] ) = $x;
			$data ["countEnCompra"] = (count ( $data ["articulosEnCompra"] ));
			$data ["finalEnCompra"] = $inicio + count ( $data ["articulosEnCompra"] );
			$this->procesarArticulos ( $data ["articulosEnCompra"] );
		}
		return $data;
	}
	public function leerArticulosEnVenta($usuario, $inicio = false, $preview = true, $new = false) {
		if ($preview) {
			$totalpagina = $this->configuracion->variables ( "cantidadVendidos" );
		} else {
			$totalpagina = $this->configuracion->variables ( "cantidadPaginacion" );
		}
		if (! $inicio) {
			$inicio = 0;
		}
		
		$data ["inicio"] = $inicio;
		$data ["totalpagina"] = $totalpagina;
		$data ["finalEnVenta"] = 0;
		$x = $this->listarArticulosXEnVentaFecha ( $usuario, $new, $inicio, $totalpagina );
		$data ["ofertasPendientes"] = $this->contarOfertasPendientes ( $usuario );
		if ($x) {
			
			list ( $data ["totalEnVenta"], $data ["articulosEnVenta"] ) = $x;
			$data ["countEnVenta"] = (count ( $data ["articulosEnVenta"] ));
			$data ["finalEnVenta"] = $inicio + count ( $data ["articulosEnVenta"] );
			$this->procesarArticulos ( $data ["articulosEnVenta"] );
		}
		return $data;
	}
	function contarOfertasPendientes($usuario) {
		$this->db->select ( "count(oferta.id) as cantidad" );
		$this->db->where ( array (
				"oferta.estado" => "Pendiente" 
		) );
		$this->db->join ( "articulo", "articulo.id=oferta.articulo and articulo.usuario='$usuario' and articulo.terminado=0 and articulo.tipo='Fijo'", "inner" );
		$this->db->group_by ( "oferta.articulo" );
		$res = $this->darTodos ( "oferta" );
		if ($res) {
			return count ( $res );
		}
		return 0;
	}
	public function leerArticulosNoComprados($usuario, $inicio = false, $preview = true) {
		if ($preview) {
			$totalpagina = $this->configuracion->variables ( "cantidadVendidos" );
		} else {
			$totalpagina = $this->configuracion->variables ( "cantidadPaginacion" );
		}
		if (! $inicio) {
			$inicio = 0;
		}
		
		$data ["inicio"] = $inicio;
		$data ["totalpagina"] = $totalpagina;
		$data ["finalNoComprados"] = 0;
		$x = $this->listarArticulosXNoCompradosFecha ( $usuario, $inicio, $totalpagina );
		if ($x) {
			list ( $data ["totalNoComprados"], $data ["articulosNoComprados"] ) = $x;
			$data ["finalNoComprados"] = $inicio + count ( $data ["articulosNoComprados"] );
			$this->procesarArticulos ( $data ["articulosNoComprados"] );
		}
		return $data;
	}
	public function leerArticulosNoVendidos($usuario, $inicio = false, $preview = true) {
		if ($preview) {
			$totalpagina = $this->configuracion->variables ( "cantidadVendidos" );
		} else {
			$totalpagina = $this->configuracion->variables ( "cantidadPaginacion" );
		}
		if (! $inicio) {
			$inicio = 0;
		}
		
		$data ["inicio"] = $inicio;
		$data ["totalpagina"] = $totalpagina;
		$data ["finalNoVendidos"] = 0;
		$x = $this->listarArticulosXNoVendidosFecha ( $usuario, $inicio, $totalpagina );
		if ($x) {
			list ( $data ["totalNoVendidos"], $data ["articulosNoVendidos"] ) = $x;
			$data ["finalNoVendidos"] = $inicio + count ( $data ["articulosNoVendidos"] );
			$this->procesarArticulos ( $data ["articulosNoVendidos"] );
		}
		return $data;
	}
	public function leerArticulos($pagina = 1, $criterio = false, $section = false, $orden = false, $ubicacion = false, $categoria = false, $idioma, $usuario = false) {
		$totalpagina = $this->configuracion->variables ( "cantidadPaginacion" );
		$pagina = intval ( $pagina );
		$pagina = $pagina > 0 ? $pagina : 1;
		$inicio = ($pagina - 1) * $totalpagina;
		
		// $data ["categorias"] = $this->darCategorias ( $categoria );
		$data ["inicio"] = $inicio;
		$data ["pagSig"] = $pagina + 1;
		$data ["totalpagina"] = $totalpagina;
		$data ["criterio"] = $criterio;
		
		switch ($section) {
			case "item" :
				$tipo = "Fijo";
				break;
			case "auction" :
				$tipo = "Subasta";
				break;
			default :
				$tipo = false;
				break;
		}
		$x = $this->listarArticulosXCriterioFecha ( $criterio, $tipo, $orden, $ubicacion, $categoria, $idioma, $usuario, $inicio, $totalpagina );
		// print ($this->db->last_query ()) ;
		if ($x) {
			list ( $data ["total"], $data ["articulos"], $categorias ) = $x;
			
			if (trim ( $criterio ) !== "" || $usuario) {
				$data ["categorias"] = $this->darCategorias2 ( $categoria, $categorias, true );
			} else {
				$data ["categorias"] = $this->darCategorias ( $categoria );
			}
			$data ["categorias"] = $this->ordenarArbol ( $data ["categorias"] );
			
			$this->procesarArticulos ( $data ["articulos"] );
		}
		return $data;
	}
	public function listarArticulosPendientes($i = false, $t = false) {
		$this->db->select ( "*,if(tipo<>'Subasta',unix_timestamp ( fecha_registro ) + " . $this->configuracion->variables ( "vencimientoOferta" ) . " * 86400,unix_timestamp ( fecha_registro ) + duracion * 86400) as tiempo" );
		$this->db->having ( "tiempo<=unix_timestamp()" );
		$this->db->where ( array (
				"terminado" => 0 
		) );
		return $this->darTodos ( "articulo", $t, $i );
	}
	public function cantidadOfertasPendientes($articulo) {
		$this->db->where ( array (
				"articulo" => $articulo,
				"estado" => "Pendiente" 
		) );
		$this->db->from ( "oferta" );
		return $this->db->count_all_results ();
	}
	public function listarOfertas($nolist = array(), $articulo = false, $subasta = false) {
		$this->db->join ( "usuario", "oferta.usuario=usuario.id", "inner" );
		$this->db->join ( "articulo", "oferta.articulo=articulo.id and articulo.terminado=0", "inner" );
		$this->db->select ( "oferta.id as id, oferta.monto as monto, oferta.usuario as usuario_id, usuario.seudonimo as seudonimo, usuario.codigo_oculto as codigo,oferta.articulo as articulo_id,oferta.monto_automatico as monto_automatico" );
		if ($articulo) {
			$this->db->where ( array (
					"articulo" => $articulo 
			) );
		}
		if ($subasta) {
			$this->db->where ( array (
					"articulo.tipo" => "Subasta" 
			) );
		}
		if (is_array ( $nolist ) && count ( $nolist ) > 0) {
			$this->db->where_not_in ( "oferta.id", $nolist );
		}
		if ($subasta) {
			$this->db->order_by ( "monto_automatico desc,fecha asc" );
		} else {
			$this->db->order_by ( "fecha asc" );
		}
		return $this->darTodos ( "oferta" );
	}
	public function siguiendo($articulo, $usuario) {
		$this->db->where ( array (
				"articulo" => $articulo,
				"usuario" => $usuario 
		) );
		return $this->darUno ( "siguiendo" );
	}
	public function seguir($articulo, $usuario) {
		if (! $this->siguiendo ( $articulo, $usuario )) {
			return $this->db->insert ( "siguiendo", array (
					"usuario" => $usuario,
					"articulo" => $articulo,
					"fecha" => date ( "Y-m-d H:s:i" ) 
			) );
		}
		return false;
	}
	public function cantidadOfertas($articulo, $usuario = false) {
		$this->db->select ( "count(oferta.id) as cantidad" );
		$this->db->where ( array (
				"articulo" => $articulo 
		) );
		if ($usuario) {
			$this->db->where ( array (
					"usuario" => $usuario 
			) );
		}
		$this->db->join ( "usuario", "usuario.id=oferta.usuario and usuario.estado<>'Baneado'" );
		return $this->darUno ( "oferta" );
	}
	public function darOfertas($articulo = false, $usuario = false, $subasta = false) {
		$basequery = "select o.id as oferta_id,u.id as user_id,u.seudonimo as seudonimo,u.codigo_oculto as codigo,o.monto as monto,o.fecha as fecha,o.estado as estado,u.positivo as positivo,u.negativo as negativo,o.monto_automatico as monto_automatico  from oferta as o inner join usuario as u on u.id=o.usuario and u.estado<>'Baneado'";
		$baseorderby = "order by o.monto desc, o.id asc";
		if ($subasta) {
			$baseorderby = "order by o.monto_automatico desc, o.id asc";
		}
		$basewhere = $articulo ? " where o.articulo= $articulo" : "";
		$res = false;
		if ($usuario !== true) {
			$res = $this->db->query ( $basequery . " $basewhere $baseorderby " );
		}
		if ($usuario !== false) {
			$res = $this->db->query ( " $basequery and u.id='$usuario' $basewhere $baseorderby " );
		}
		if ($res) {
			if ($res) {
				$res = $res->result ();
				if ($res && is_array ( $res ) && count ( $res ) > 0) {
					return $res;
				}
			}
		}
		if ($articulo) {
			$this->db->where ( array (
					"articulo" => $articulo 
			) );
		}
		$this->db->join ( "usuario", "usuario.id=oferta.usuario and usuario.estado<>'Baneado'", "inner" );
		$orderby = "oferta.monto desc, oferta.id asc";
		if ($subasta) {
			$orderby = "oferta.monto_automatico desc, oferta.id asc";
		}
		$this->db->order_by ( $orderby );
		return $this->darTodos ( "oferta" );
	}
	public function maximaOferta($articulo, $usuario = false) {
		$this->db->select ( "max(monto) as cantidad" );
		$this->db->where ( array (
				"articulo" => $articulo 
		) );
		if ($usuario) {
			$this->db->where ( array (
					"usuario" => $usuario 
			) );
		}
		$this->db->join ( "usuario", "usuario.id=oferta.usuario and usuario.estado<>'Baneado'", "inner" );
		return $this->darUno ( "oferta" );
	}
	public function mayorOferta($articulo, $subasta = false) {
		$this->db->select ( "oferta.id,monto,usuario,monto_automatico" );
		$this->db->where ( array (
				"articulo" => $articulo 
		) );
		$this->db->join ( "usuario", "usuario.id=oferta.usuario && usuario.estado<>'Baneado'", "inner" );
		if (! $subasta) {
			$this->db->order_by ( "monto desc" );
		} else {
			$this->db->order_by ( "monto_automatico desc,oferta.id asc" );
		}
		$o = $this->darUno ( "oferta" );
		if ($o) {
			$c = $this->cantidadOfertas ( $articulo );
			if ($c->cantidad !== false) {
				$o->cantidad = $c->cantidad;
			}
			return $o;
		}
		return false;
	}
	public function ultimaOferta($articulo, $usuario) {
		$this->db->select ( "oferta.*" );
		$this->db->where ( array (
				"articulo" => $articulo,
				"usuario" => $usuario 
		) );
		$this->db->join ( "usuario", "usuario.id=oferta.usuario && usuario.estado<>'Baneado'", "inner" );
		$this->db->order_by ( "oferta.id desc" );
		$o = $this->darUno ( "oferta" );
		if ($o) {
			$c = $this->cantidadOfertas ( $articulo, $usuario );
			if ($c->cantidad !== false) {
				$o->cantidad = $c->cantidad;
			}
			return $o;
		}
		return false;
	}
	public function adicionarPaqueteSimilar($articulo) {
		if ($articulo) {
			$p = $this->paqueteSimilar ( $articulo );
			$CI = &get_instance ();
			$CI->load->model ( "Usuario_model", "usuario" );
			$articulo->usuario = $CI->usuario->darUsuarioXId ( $articulo->usuario );
			$articulo->usuario->pais = $articulo->usuario->darPais ( $articulo->usuario->pais );
			$articulo->comprador = $CI->usuario->darUsuarioXId ( $articulo->comprador );
			$articulo->comprador->pais = $articulo->comprador->darPais ( $articulo->comprador->pais );
			$articulos = array ();
			if ($p) {
				if (trim ( $p->articulos ) !== "") {
					$articulos = explode ( ",", $p->articulos );
				}
				$articulos [] = $articulo->id;
				$gastos = floatval ( $p->gastos_envio );
				$monto = floatval ( $p->monto ) + ($articulo->precio_oferta ? $articulo->precio_oferta : $articulo->precio);
				if ($articulo->usuario->pais == $articulo->comprador->pais) {
					$gastos += floatval ( $articulo->gastos_pais );
				} elseif ($articulo->usuario->pais->continente == $articulo->comprador->pais->continente) {
					$gastos += floatval ( $articulo->gastos_continente );
				} elseif ($articulo->envio_local) {
					$gastos += 0;
				} else {
					$gastos += floatval ( $articulo->gastos_todos );
				}
				$this->db->update ( "paquete", array (
						"articulos" => implode ( ",", $articulos ),
						"gastos_envio" => $gastos,
						"monto" => $monto 
				), array (
						"id" => $p->id 
				) );
				return $p->id;
			} else {
				$articulos [] = $articulo->id;
				$gastos = 0;
				$monto = 0 + ($articulo->precio_oferta ? $articulo->precio_oferta : $articulo->precio);
				if ($articulo->usuario->pais == $articulo->comprador->pais) {
					$gastos += floatval ( $articulo->gastos_pais );
				} elseif ($articulo->usuario->pais->continente == $articulo->comprador->pais->continente) {
					$gastos += floatval ( $articulo->gastos_continente );
				} elseif ($articulo->envio_local) {
					$gastos += 0;
				} else {
					$gastos += floatval ( $articulo->gastos_todos );
				}
				$this->db->insert ( "paquete", array (
						"vendedor" => $articulo->usuario->id,
						"comprador" => $articulo->comprador->id,
						"fecha" => date ( "Y-m-d H:i:s" ),
						"articulos" => implode ( ",", $articulos ),
						"gastos_envio" => $gastos,
						"monto" => $monto 
				) );
				return $this->db->insert_id ();
			}
		}
		return false;
	}
	public function adicionarPaqueteSimilarTransaccion($transaccion) {
		if ($transaccion) {
			$articulo = $this->darArticulo ( $transaccion->articulo );
			$p = $this->darPaquete ( $transaccion->paquete );
			if (! $p) {
				$articulo->comprador = $transaccion->comprador;
				$p = $this->paqueteSimilar ( $articulo );
			}
			$CI = &get_instance ();
			$transacciones = array ();
			$CI->load->model ( "Usuario_model", "usuario" );
			$articulo->usuario = $CI->usuario->darUsuarioXId ( $articulo->usuario );
			$articulo->usuario->pais = $articulo->usuario->darPais ( $articulo->usuario->pais );
			$transaccion->comprador = $CI->usuario->darUsuarioXId ( $transaccion->comprador );
			$transaccion->comprador->pais = $transaccion->comprador->darPais ( $transaccion->comprador->pais );
			$pid = false;
			if ($p) {
				if (trim ( $p->transacciones ) !== "") {
					$transacciones = explode ( ",", $p->transacciones );
				}
				$transacciones [] = $transaccion->id;
				
				$gastos = floatval ( $p->gastos_envio );
				$monto = floatval ( $p->monto ) + $transaccion->precio * $transaccion->cantidad;
				if ($articulo->usuario->pais == $transaccion->comprador->pais) {
					$gastos += floatval ( $articulo->gastos_pais * $transaccion->cantidad );
				} elseif ($articulo->usuario->pais->continente == $transaccion->comprador->pais->continente) {
					$gastos += floatval ( $articulo->gastos_continente * $transaccion->cantidad );
				} elseif ($articulo->envio_local) {
					$gastos += 0;
				} else {
					$gastos += floatval ( $articulo->gastos_todos * $transaccion->cantidad );
				}
				$this->db->update ( "paquete", array (
						"transacciones" => implode ( ",", $transacciones ),
						"gastos_envio" => $gastos,
						"monto" => $monto 
				), array (
						"id" => $p->id 
				) );
				$pid = $p->id;
			} else {
				$transacciones [] = $transaccion->id;
				$gastos = 0;
				$monto = 0 + $transaccion->precio * $transaccion->cantidad;
				if ($articulo->usuario->pais == $transaccion->comprador->pais) {
					$gastos += floatval ( $articulo->gastos_pais * $transaccion->cantidad );
				} elseif ($articulo->usuario->pais->continente == $transaccion->comprador->pais->continente) {
					$gastos += floatval ( $articulo->gastos_continente * $transaccion->cantidad );
				} elseif ($articulo->envio_local) {
					$gastos += 0;
				} else {
					$gastos += floatval ( $articulo->gastos_todos * $transaccion->cantidad );
				}
				$this->db->insert ( "paquete", array (
						"vendedor" => $articulo->usuario->id,
						"comprador" => $transaccion->comprador->id,
						"fecha" => date ( "Y-m-d H:i:s" ),
						"transacciones" => implode ( ",", $transacciones ),
						"gastos_envio" => $gastos,
						"monto" => $monto 
				) );
				$pid = $this->db->insert_id ();
			}
			if ($pid) {
				$this->db->update ( "transaccion", array (
						"paquete" => $pid 
				), array (
						"id" => $transaccion->id 
				) );
			}
			return $pid;
		}
		return false;
	}
	public function eliminarPaqueteSimilar($articulo) {
		$p = $this->paqueteSimilar ( $articulo );
		
		if ($p) {
			$articulos = explode ( ",", $p->articulos );
			foreach ( $articulos as $a ) {
				$this->db->update ( "articulo", array (
						"estado" => "Sin gastos Envio",
						"paquete" => null 
				), array (
						"id" => $a 
				) );
			}
			$transacciones = explode ( ",", $p->transacciones );
			foreach ( $transacciones as $t ) {
				$this->db->update ( "transaccion", array (
						"estado" => "Sin gastos Envio",
						"paquete" => null 
				), array (
						"id" => $t 
				) );
			}
			$this->db->delete ( "paquete", array (
					"id" => $p->id 
			) );
		}
	}
	public function paqueteSimilar($articulo) {
		if ($articulo) {
			if (is_object ( $articulo->comprador )) {
				$comprador = $articulo->comprador->id;
			} else {
				$comprador = $articulo->comprador;
				if (! $comprador) {
					return false;
				}
			}
			$sql = "SELECT paquete.monto,paquete.gastos_envio,paquete.id,paquete.articulos,paquete.transacciones FROM paquete inner join articulo on articulo.paquete=paquete.id and articulo.pagos='$articulo->pagos' WHERE paquete.estado='Sin pago' and paquete.vendedor='$articulo->usuario' and paquete.comprador='$comprador'  and (paquete.denuncia2=0 or isnull(paquete.denuncia2) ) group by paquete.id";
			$r = $this->db->query ( $sql )->result ();
			if ($r && is_array ( $r ) && count ( $r ) > 0) {
				return $r [0];
			}
			$sql = "SELECT paquete.monto,paquete.gastos_envio,paquete.id,paquete.articulos,paquete.transacciones
			FROM paquete
			inner join transaccion on transaccion.paquete=paquete.id
			inner join articulo on articulo.id=transaccion.articulo and articulo.pagos='$articulo->pagos'
			WHERE paquete.estado='Sin pago' and paquete.vendedor='$articulo->usuario' and paquete.comprador='$comprador'  and (paquete.denuncia2=0 or isnull(paquete.denuncia2)) group by paquete.id";
			$r = $this->db->query ( $sql )->result ();
			if ($r && is_array ( $r ) && count ( $r ) > 0) {
				return $r [0];
			}
		}
		return false;
	}
	public function articuloPaquete($id) {
		$r = $this->db->query ( "select * from paquete where articulos like '$id' or articulos like '%,$id' or articulos like '$id,%' or articulos like '%,$id ,%'" )->result ();
		return ($r && is_array ( $r ) && count ( $r ) > 0) ? $r [0] : false;
	}
	public function transaccionPaquete($id) {
		$r = $this->db->query ( "select * from paquete where transacciones like '$id' or transacciones like '%,$id' or transacciones like '$id ,%' or transacciones like '%,$id,%'" )->result ();
		return ($r && is_array ( $r ) && count ( $r ) > 0) ? $r [0] : false;
	}
	public function darPaquete($paquete) {
		$this->db->where ( array (
				"id" => $paquete 
		) );
		return $this->darUno ( "paquete" );
	}
	function prepararArticulosPorComprar($comprador, $vendedor, $paquete = false, $pagos = false) {
		if ($vendedor) {
			$jextra = "";
			$wextra = "";
			$jextra2 = "";
			$wextra2 = "";
			if ($paquete) {
				$wextra = " articulo.paquete='$paquete'";
				$wextra2 = " transaccion.paquete='$paquete'";
			}
			if (! $wextra) {
				$wextra = "terminado = 1 and articulo.estado<>'A la venta' and articulo.estado<>'Finalizado' and articulo.usuario='$vendedor' and isnull(articulo.paquete)";
				$wextra2 = "articulo.usuario='$vendedor' and isnull(transaccion.paquete) and transaccion.estado<>'Finalizado'";
				$jextra = "INNER JOIN usuario ON usuario.id=articulo.comprador and usuario.id='$comprador'";
				$jextra2 = "INNER JOIN usuario ON usuario.id=transaccion.comprador and usuario.id='$comprador'";
			}
			
			if ($pagos) {
				$wextra .= " and articulo.pagos='" . str_replace ( "-", ",", $pagos ) . "'";
				$wextra2 .= " and articulo.pagos='" . str_replace ( "-", ",", $pagos ) . "'";
			}
			
			return "select * from ((SELECT
			articulo.cantidad,
			articulo.id,
			articulo.titulo,
			articulo.tipo,
			if(isnull(articulo.precio_oferta),articulo.precio,articulo.precio_oferta) as precio,
			articulo.fecha_registro,
			articulo.duracion,
			articulo.usuario,
			articulo.foto,
			articulo.categoria as categoria,
			articulo.comprador,
			articulo.fecha_terminado,
			articulo.estado,
			(if(isnull(articulo.paquete),null,(select gastos_envio from paquete where paquete.id=articulo.paquete)))as gastos_envio,
			articulo.pagos,
			null as transaccion

			FROM articulo
			$jextra
			WHERE $wextra)
			union
			(SELECT
			transaccion.cantidad,
			transaccion.articulo,
			articulo.titulo,
			articulo.tipo,
			transaccion.precio*transaccion.cantidad,
			articulo.fecha_registro,
			articulo.duracion,
			articulo.usuario,
			articulo.foto,
			articulo.categoria as categoria,
			transaccion.comprador,
			transaccion.fecha_terminado,
			transaccion.estado,
			(if(isnull(transaccion.paquete),null,(select gastos_envio from paquete where paquete.id=transaccion.paquete)))as gastos_envio,
			articulo.pagos,
			transaccion.id as transaccion

			FROM transaccion
			INNER JOIN articulo ON transaccion.articulo=articulo.id
			$jextra2
			WHERE  $wextra2))as articulo
			ORDER BY articulo.titulo desc";
		}
		return false;
	}
	public function listarArticulosPorComprar($comprador, $vendedor, $paquete = false, $pagos = false) {
		$query = $this->prepararArticulosPorComprar ( $comprador, $vendedor, $paquete, $pagos );
		// print $query;
		$res = $this->db->query ( $query );
		$data = $this->darResuts ( $res );
		$this->procesarArticulos ( $data );
		return $data;
	}
	public function obtenerVendidosDeCantidad($articulo) {
		$this->db->where ( array (
				"articulo" => $articulo 
		) );
		$this->db->select ( "count(id) as cantidad" );
		$r = $this->db->get ( "transaccion" )->result ();
		return ($r && is_array ( $r ) && count ( $r ) > 0) ? $r [0]->cantidad : 0;
	}
	public function comprar($articulo, $usuario) {
		$a = $this->darArticulo ( $articulo );
		if ($a && $a->terminado !== 1 && $a->estado == "A la venta") {
			if ($a->tipo !== "Cantidad") {
				$res = $this->db->update ( "oferta", array (
						"estado" => "Rechazado" 
				), array (
						"articulo" => $articulo 
				) );
				if ($res) {
					$a->comprador = $usuario;
					$pid = $this->adicionarPaqueteSimilar ( $a );
					// $this->eliminarPaqueteSimilar ( $a );
					if ($this->db->update ( "articulo", array (
							"paquete" => $pid,
							"estado" => "Sin Pago",
							"fecha_terminado" => date ( "Y-m-d H:i:s" ),
							"terminado" => 1,
							"comprador" => $usuario 
					), array (
							"id" => $articulo 
					) )) {
						return $articulo;
					}
				}
			} else {
				$cantidad = $this->input->post ( "cantidad" );
				$cantidad = intval ( $cantidad );
				if ($cantidad > $a->cantidad) {
					$cantidad = $a->cantidad;
				}
				if ($cantidad <= 0) {
					return false;
				}
				$a->comprador = $usuario;
				$this->db->where ( array (
						"articulo" => $a->id,
						"comprador" => $usuario,
						"estado" => "Sin Pago" 
				) );
				$r = $this->db->get ( "transaccion" )->result ();
				$tid = false;
				if ($r && is_array ( $r ) && count ( $r ) > 0) {
					$t = $r [0];
					$this->db->update ( "transaccion", array (
							"fecha_terminado" => date ( "Y-m-d H:i:s" ),
							"cantidad" => $t->cantidad + $cantidad 
					), array (
							"id" => $t->id 
					) );
					$t->cantidad = $cantidad;
					$pid = $this->adicionarPaqueteSimilarTransaccion ( $t );
					$tid = $t->id;
				} else {
					$this->db->insert ( "transaccion", array (
							"articulo" => $articulo,
							"precio" => $a->precio,
							"moneda" => $a->moneda,
							"estado" => "Sin Pago",
							"fecha_terminado" => date ( "Y-m-d H:i:s" ),
							"comprador" => $usuario,
							"cantidad" => $cantidad 
					) );
					$tid = $this->db->insert_id ();
					$rr = $this->db->where ( array (
							"id" => $tid 
					) )->get ( "transaccion" )->result ();
					if ($rr && is_array ( $rr ) && count ( $rr ) > 0) {
						$pid = $this->adicionarPaqueteSimilarTransaccion ( $rr [0] );
					}
				}
				if ($a->cantidad - $cantidad > 0) {
					if ($this->db->update ( "articulo", array (
							"cantidad" => $a->cantidad - $cantidad 
					), array (
							"id" => $articulo 
					) )) {
						return $tid;
					}
				} else {
					if ($this->db->update ( "articulo", array (
							"cantidad" => 0,
							"terminado" => 1,
							"fecha_terminado" => date ( "Y-m-d H:i:S" ) 
					), array (
							"id" => $articulo 
					) )) {
						return $tid;
					}
				}
			}
		}
		return false;
	}
	public function datosOferta($articulo, $usuario) {
		$this->db->select ( "count(oferta.id) as cantidad,max(monto) as maximo" );
		$this->db->where ( array (
				"articulo" => $articulo,
				"usuario" => $usuario 
		) );
		$this->db->join ( "usuario", "usuario.id=oferta.usuario and usuario.estado<>'Baneado'", "inner" );
		return $this->darUno ( "oferta" );
	}
	public function darOfertaGanadora($articulo) {
		$this->db->select ( "monto,monto_automatico" );
		$this->db->where ( array (
				"articulo" => $articulo,
				"oferta.estado" => "Aceptado" 
		) );
		$this->db->join ( "usuario", "usuario.id=oferta.usuario and usuario.estado<>'Baneado'", "inner" );
		return $this->darUno ( "oferta" );
	}
	public function aceptarOferta($oferta, $articulo, $subasta = false) {
		$a = $this->darArticulo ( $articulo );
		if ($a && $a->terminado !== 1) {
			$res = $this->db->update ( "oferta", array (
					"estado" => "Rechazado" 
			), array (
					"articulo" => $articulo,
					"id !=" => $oferta 
			) );
			if ($res) {
				$res = $this->db->update ( "oferta", array (
						"estado" => "Aceptado" 
				), array (
						"id" => $oferta 
				) );
				if ($res) {
					$this->db->select ( "usuario,monto,monto_automatico" );
					$this->db->where ( array (
							"id" => $oferta 
					) );
					$oferta = $this->darUno ( "oferta" );
					
					// var_dump($expression);
					if ($oferta) {
						$CI = &get_instance ();
						$a->comprador = $oferta->usuario;
						$pid = $this->adicionarPaqueteSimilar ( $a );
						return $this->db->update ( "articulo", array (
								"paquete" => $pid,
								"estado" => "Sin Pago",
								"fecha_terminado" => date ( "Y-m-d H:i:s" ),
								"terminado" => 1,
								"comprador" => $oferta->usuario,
								"precio_oferta" => ($subasta ? $oferta->monto_automatico : $oferta->monto) 
						), array (
								"id" => $articulo 
						) );
					}
				}
			}
		}
		return false;
	}
	public function rechazarOferta($oferta, $articulo) {
		$a = $this->darArticulo ( $articulo );
		if ($a && $a->terminado !== 1) {
			return $this->db->update ( "oferta", array (
					"estado" => "Rechazado" 
			), array (
					"id =" => $oferta 
			) );
		}
		return false;
	}
	public function desactivarOfertasVistos($articulo) {
		/*
		 * if ($articulo) { $this->db->update ( "articulo", array (
		 * "ofertas_visto" => 0 ), array ( "id" => $articulo ) ); }
		 */
	}
	public function activarOfertasVistos($articulo) {
		/*
		 * if ($articulo) { $this->db->update ( "articulo", array (
		 * "ofertas_visto" => 1 ), array ( "id" => $articulo ) ); }
		 */
	}
	public function ofertar($articulo, $usuario, $monto) {
		$a = $this->darArticulo ( $articulo );
		if ($a && $a->estado == "A la venta") {
			$monto = $this->parseDecimal ( $monto );
			$c = $this->datosOferta ( $articulo, $usuario );
			$m = $this->configuracion->variables ( "maximoCantidad" );
			if ($c && $c->cantidad < $m) {
				$uo = $this->ultimaOferta ( $articulo, $usuario );
				if (! $uo || ($uo && $uo->monto < $monto)) {
					$datos = array (
							"monto" => $monto,
							"usuario" => $usuario,
							"articulo" => $articulo,
							"fecha" => date ( "Y-m-d H:i:s" ) 
					);
					if ($a->precio_rechazo && $a->precio_rechazo > $monto) {
						$datos ["estado"] = "Rechazado";
					}
					if ($this->db->insert ( "oferta", $datos )) {
						$this->desactivarOfertasVistos ( $articulo );
						return array (
								1,
								$m - 1 - $c->cantidad,
								$a->precio_rechazo && $a->precio_rechazo > $monto 
						);
					}
				} else {
					return array (
							3,
							0,
							false 
					);
				}
			}
			return array (
					0,
					0,
					false 
			);
		}
		return array (
				2,
				0,
				false 
		);
	}
	private function enviarMailSobrepujados($articulo, $usuario) {
		$emails = $this->db->query ( "SELECT usuario.email as email FROM `oferta` inner join usuario on oferta.usuario=usuario.id WHERE oferta.articulo=$articulo->id and oferta.usuario<>$usuario group by oferta.usuario" );
		if ($emails) {
			$emails = $emails->result ();
			if ($emails && is_array ( $emails ) && count ( $emails ) > 0) {
				$this->load->library ( "myemail" );
				foreach ( $emails as $e ) {
					$this->myemail->enviarTemplate ( $e->email, "Te han sobrepujado", "mail/sobre-pujado", array (
							"url" => base_url () . "product/ $articulo->id - " . normalizarTexto ( $articulo->titulo ),
							"titulo" => $articulo->titulo 
					) );
				}
			}
		}
	}
	public function pujar($articulo, $usuario, $monto, $oferta = true) {
		$a = $this->darArticulo ( $articulo );
		if ($a && $a->estado == "A la venta") {
			$monto = $this->parseDecimal ( $monto );
			$c = $this->mayorOferta ( $articulo, true );
			$m = $this->maximaOferta ( $articulo, $usuario );
			$monto_automatico = $a->precio;
			$minimoMonto = $a->precio;
			$ganador = false;
			$mismoMonto = false;
			if ($c) {
				if ($m->cantidad >= $monto) {
					return 3; // tu oferta no puede ser menor a la que ofertaste
						          // antes;
				}
				
				if ($c->cantidad > 0) {
					if ($c->monto_automatico >= $monto) {
						return 0; // no se alcanzo el minimo
					} else {
						if ($c->monto > $monto) {
							$datos = array (
									"monto" => $c->monto,
									"usuario" => $c->usuario,
									"articulo" => $articulo,
									"fecha" => date ( "Y-m-d H:i:s" ),
									"tipo" => "Subasta",
									"monto_automatico" => $monto + 0.5 
							);
							$this->db->insert ( "oferta", $datos );
							$monto_automatico = $monto;
						} elseif ($c->monto == $monto) {
							$datos = array (
									"monto" => $c->monto,
									"usuario" => $c->usuario,
									"articulo" => $articulo,
									"fecha" => date ( "Y-m-d H:i:s" ),
									"tipo" => "Subasta",
									"monto_automatico" => $monto 
							);
							$this->db->insert ( "oferta", $datos );
							$monto_automatico = $monto;
						} else {
							if ($c->usuario == $usuario) {
								$monto_automatico = $c->monto_automatico;
								$mismoMonto = true;
							} else {
								$monto_automatico = $c->monto + 0.5;
								$ganador = true;
							}
						}
					}
				}
			} else {
				if ($minimoMonto > $monto) {
					return 0;
				}
			}
			if (! $mismoMonto) {
				$datos = array (
						"monto" => $monto,
						"usuario" => $usuario,
						"articulo" => $articulo,
						"fecha" => date ( "Y-m-d H:i:s" ),
						"tipo" => "Subasta",
						"monto_automatico" => $monto_automatico 
				);
				$exito = false;
				if ($this->db->insert ( "oferta", $datos )) {
					if ($c && $ganador) {
						$this->load->model ( "usuario_model", "umodel" );
						$u = $this->umodel->darUsuarioXId ( $c->usuario );
						if ($u) {
							$this->load->library ( "myemail" );
							$this->myemail->enviarTemplate ( $u->email, "Te han sobrepujado", "mail/sobre-pujado", array (
									"url" => base_url () . "product/ $a->id - " . normalizarTexto ( $a->titulo ),
									"titulo" => $a->titulo 
							) );
						}
					}
					$exito = true;
				}
			} else {
				if ($this->db->update ( "oferta", array (
						"monto" => $monto 
				), array (
						"id" => $c->id 
				) )) {
					$exito = true;
				}
			}
			if ($exito) {
				$this->desactivarOfertasVistos ( $articulo );
				return 1;
			}
		}
		return 2;
	}
	public function finalizar($articulo, $estado = false, $transaccion = false) {
		$this->resetTemporal ();
		if (! $transaccion) {
			$a = $this->darArticulo ( $articulo );
			if ($a) {
				if ($a->terminado == 0) {
					$this->quitarCantidad ( $a->categoria );
					$x = array (
							"terminado" => 1,
							"fecha_terminado" => date ( "Y-m-d H:i:s" ) 
					);
					if ($estado) {
						$x ["estado"] = $estado;
					}
					$x = $this->db->update ( "articulo", $x, array (
							"id" => $articulo 
					) );
					if ($x && $a->estado = "A la venta" && ! $estado) {
						$this->db->update ( "oferta", array (
								"estado" => "Rechazado" 
						), array (
								"articulo" => $a->id 
						) );
					}
					return $x;
				} else {
					$this->db->update ( "articulo", array (
							"terminado" => 1,
							"fecha_terminado" => date ( "Y-m-d H:i:s" ),
							"estado" => $estado 
					), array (
							"id" => $articulo 
					) );
				}
			}
		} else {
			$this->db->update ( "transaccion", array (
					"fecha_terminado" => date ( "Y-m-d H:i:s" ),
					"estado" => $estado 
			), array (
					"id" => $articulo 
			) );
		}
		return false;
	}
	public function comenzar($articulo) {
		$a = $this->darArticulo ( $articulo );
		if ($a && $a->terminado == 1) {
			$this->adicionarCantidad ( $a->categoria );
			if ($this->db->update ( "articulo", array (
					"terminado" => 0,
					"fecha_registro" => date ( "Y-m-d H:i:s" ) 
			), array (
					"id" => $articulo 
			) )) {
				$this->db->delete ( "oferta", array (
						"articulo" => $a->id 
				) );
				return $a;
			}
		}
		return false;
	}
	public function parseDecimal($n) {
		if ($n !== null) {
			return str_replace ( ",", ".", "" . $n );
		}
		return $n;
	}
	public function registrar($modificar = false) {
		$this->precio = $this->parseDecimal ( $this->precio );
		$this->precio_rechazo = $this->parseDecimal ( $this->precio_rechazo );
		$this->gastos_pais = $this->parseDecimal ( $this->gastos_pais );
		$this->gastos_continente = $this->parseDecimal ( $this->gastos_continente );
		$this->gastos_todos = $this->parseDecimal ( $this->gastos_todos );
		$datos = array (
				"usuario" => $this->usuario,
				"titulo" => $this->titulo,
				"descripcion" => strip_not_allowed ( $this->descripcion, "form,marquee,script,input,textarea,button" ),
				"categoria" => $this->categoria,
				"foto" => $this->foto,
				"tipo" => $this->tipo,
				"precio" => $this->precio,
				"precio_rechazo" => $this->precio_rechazo,
				"moneda" => $this->moneda,
				"duracion" => $this->duracion,
				"pagos" => $this->pagos,
				"cantidad" => $this->cantidad,
				"cantidad_original" => $this->cantidad_original,
				"gastos_pais" => $this->gastos_pais,
				"gastos_continente" => $this->gastos_continente,
				"gastos_todos" => $this->gastos_todos,
				"envio_local" => $this->envio_local 
		);
		if ($modificar) {
			$this->db->where ( array (
					"id" => $this->id 
			) );
			$this->db->select ( "categoria" );
			$c = $this->darUno ( "articulo" );
			if ($c) {
				if ($this->categoria !== $c->categoria) {
					$this->quitarCantidad ( $c->categoria );
					$this->adicionarCantidad ( $this->categoria );
				}
			}
			$this->resetTemporal ();
			return $this->db->update ( "articulo", $datos, array (
					"id" => $this->id 
			) );
		} else {
			$datos ["fecha_registro"] = $this->fecha_registro;
			$datos ["estado"] = $this->estado;
			$datos ["fecha_alta"] = $this->fecha_alta;
			if ($this->categoria) {
				$this->adicionarCantidad ( $this->categoria );
			}
			if ($this->db->insert ( "articulo", $datos )) {
				$this->id = $this->db->insert_id ();
				$this->resetTemporal ();
				return $this->id;
			}
		}
		
		return false;
	}
	public function darArticulo($id) {
		$this->db->where ( array (
				"id" => $id 
		) );
		return $this->darUno ( "articulo" );
	}
	function listarArticulosXUsuarioXNoventa($usuario, $categoria = false, $inicio = 0, $total = 10) {
		$this->db->where ( array (
				"usuario" => $usuario,
				"terminado" => 1,
				"articulo.estado" => "A la venta" 
		) );
		return $this->listarArticulos ( $categoria, $inicio, $total );
	}
	function listarArticulosXUsuario($usuario, $categoria = false, $inicio = 0, $total = 10) {
		$this->db->where ( array (
				"usuario" => $usuario,
				"terminado" => 0 
		) );
		return $this->listarArticulos ( $categoria, $inicio, $total );
	}
	public function listarArticulos($categoria = false, $inicio = 0, $total = 10) {
		if ($categoria) {
			$this->db->where ( array (
					"categoria" => $categoria 
			) );
		}
		$this->db->select ( "articulo.*,pais.nombre as pais_nombre" );
		$this->db->join ( "usuario", "usuario.id=articulo.usuario", "inner" );
		$this->db->join ( "pais", "pais.codigo3=usuario.pais", "inner" );
		$this->db->order_by ( "articulo.id", "desc" );
		return $this->darTodos ( "articulo", $total, $inicio );
	}
	// no temporal mas relevante
	public function listarArticulosXCriterioRelevante($criterio = false, $tipo = false, $categoria = false, $inicio = 0, $total = 10) {
		$criterio = explode ( " ", trim ( preg_replace ( "/(\s+)/im", " ", $criterio ) ) );
		if ($criterio && is_array ( $criterio ) && count ( $criterio ) > 0) {
			$datos = array ();
			$ands = array_merge ( array (), $criterio );
			$ors = array ();
			$notin = array ();
			$extra = "";
			$initialAnds = count ( $ands );
			do {
				if (count ( $ands ) > 0 || count ( $ors ) > 0) {
					$extra = "and (";
					if (count ( $ands ) > 0) {
						$extra .= "(";
						$tands = array ();
						foreach ( $ands as $a ) {
							$tands [] = "titulo like '%$a%'";
						}
						$extra .= implode ( " and ", $tands );
						$extra .= ")";
					}
					if (count ( $ands ) == 1 && count ( $ors ) > 0) {
						$extra .= " or ";
						$extra .= "(";
						$tors = array ();
						foreach ( $ors as $o ) {
							$tors [] = "titulo like '%$o%'";
						}
						$extra .= implode ( " or ", $tors );
						$extra .= ")";
					}
					$extra .= ")";
				}
				if ($tipo) {
					$extra .= " and tipo='$tipo'";
				}
				if (count ( $notin ) > 0) {
					$extra .= " and articulo.id not in(" . implode ( ",", $notin ) . ")";
				}
				$query = "SELECT articulo.*, pais.nombre as pais_nombre
				FROM (articulo)
				INNER JOIN usuario ON usuario.id=articulo.usuario
				INNER JOIN pais ON pais.codigo3=usuario.pais
				WHERE terminado = 0
				$extra
				ORDER BY fecha_registro desc
				LIMIT $total ";
				$res = $this->db->query ( $query );
				$res = $this->darResuts ( $res );
				$cantidad = $res ? count ( $res ) : 0;
				array_push ( $ors, array_pop ( $ands ) );
				if ($cantidad > 0) {
					if ($cantidad < $total) {
						foreach ( $res as $r ) {
							$notin [] = $r->id;
						}
					}
					$datos = array_merge ( $datos, $res );
				}
				$total = $total - $cantidad;
			} while ( $cantidad < $total && count ( $ors ) < $initialAnds );
			return $datos;
		}
		return false;
	}
	public function leer25Articulos() {
		return $this->leerNArticulos ( 25 );
	}
	public function leer10Articulos() {
		return $this->leerNArticulos ( 10 );
	}
	public function leerNArticulos($n) {
		$precio = "articulo.precio";
		$sql = "SELECT articulo.id,articulo.titulo,articulo.tipo,$precio ,articulo.fecha_registro,articulo.duracion,articulo.usuario,articulo.foto
		FROM (articulo)
		INNER JOIN usuario ON usuario.id=articulo.usuario and usuario.estado<>'Baneado'
		WHERE terminado = 0 and articulo.estado<>'Baneado'
		ORDER BY fecha_registro desc
		limit 0,$n
		";
		$res = $this->db->query ( $sql )->result ();
		$this->procesarArticulos ( $res );
		return $res;
	}
	public function prepararConsultaArticuloXCriterioFecha($criterio = false, $tipo = false, $orden = false, $ubicacion = false, $categoria = false, $idioma = false, $usuario = false) {
		$tiempo = time ();
		if (trim ( $orden ) === "" && $usuario) {
			$orden = "finaliza";
		}
		$criterio = explode ( " ", trim ( preg_replace ( "/(\s+)/im", " ", $criterio ) ) );
		$wextra = "";
		if ($criterio && is_array ( $criterio ) && count ( $criterio ) > 0) {
			$ors = array_merge ( array (), $criterio );
			if (count ( $ors ) > 0) {
				$wextra = " (";
				if (count ( $ors ) > 0) {
					$wextra .= "(";
					$tors = array ();
					foreach ( $ors as $o ) {
						$tors [] = "titulo like '%" . str_replace ( "'", "\\'", $o ) . "%'";
					}
					$wextra .= implode ( " and ", $tors );
					$wextra .= ")";
				}
				$wextra .= ")";
			}
		}
		if ($ubicacion) {
			$ubicacion = explode ( "-", $ubicacion );
			if (count ( $ubicacion ) > 1) {
				$wextra = $wextra ? $wextra . " and " : "";
				switch ($ubicacion [0]) {
					case "P" :
						$wextra .= "codigo3='" . $ubicacion [1] . "' ";
						break;
					
					default :
						$wextra .= "continente='" . $ubicacion [1] . "' ";
						break;
				}
			}
		}
		if ($tipo) {
			$wextra = $wextra ? $wextra . " and " : "";
			if ($tipo !== "Fijo") {
				$wextra .= " tipo='$tipo'";
			} else {
				$wextra .= " (tipo='$tipo' or tipo='Cantidad')";
			}
		}
		if ($usuario) {
			$wextra = $wextra ? $wextra . " and " : "";
			$wextra .= " usuario='$usuario'";
		}
		if ($categoria) {
			$wextra = $wextra ? $wextra . " and " : "";
			$this->load->model ( "categoria_model", "categoria_model" );
			$ids = $this->categoria_model->darArbolHijos ( $categoria );
			if ($ids && is_array ( $ids ) && count ( $ids ) > 0) {
				$wextra .= "categoria in(" . implode ( ",", $ids ) . ")";
			}
		}
		$wextra = $wextra ? "where $wextra" : "";
		$nameBD = $this->crearTemporal ( $orden );
		$query = "select * from $nameBD $wextra";
		return $query;
	}
	public function crearTemporal($orden) {
		$orden = $orden ? $orden : "ultimos";
		$nameBD = "tmp_busqueda_" . normalizarTexto ( $orden );
		$res = @$this->db->query ( "select 1 from $nameBD limit 1" );
		if (! $res) {
			
			$precio = "articulo.precio";
			$orderby = "ORDER BY fecha_registro desc";
			$adicionalSelect = "";
			$extra = "";
			
			$adicionaInsert = "";
			$adicionaCreate = "";
			switch ($orden) {
				case "finaliza" :
					$orderby = "ORDER BY tiempo asc";
					$vencimientoOferta = intval ( $this->configuracion->variables ( "vencimientoOferta" ) ) * 86400;
					$adicionalSelect = ",if(articulo.tipo='Fijo' or articulo.tipo='Cantidad',unix_timestamp(articulo.fecha_registro-now())+$vencimientoOferta ,unix_timestamp(articulo.fecha_registro- unix_timestamp())+articulo.duracion*86400 )as tiempo";
					$adicionaCreate = "tiempo bigint null,";
					$adicionaInsert = ",tiempo";
					break;
				case "mas-alto" :
					$precio = "if(articulo.tipo='Fijo',articulo.precio,mayorPuja(articulo.id)) as precio";
					$orderby = "ORDER BY precio desc";
					break;
				case "mas-bajo" :
					$precio = "if(articulo.tipo='Fijo',articulo.precio,mayorPuja(articulo.id)) as precio";
					$orderby = "ORDER BY precio asc";
					break;
			}
			$query = "SELECT articulo.cantidad,articulo.id,articulo.titulo,articulo.tipo,$precio ,articulo.fecha_registro,articulo.duracion,articulo.usuario,articulo.foto, pais.nombre as pais_nombre, ciudad.nombre as ciudad_nombre, articulo.categoria as categoria,pais.codigo3,pais.continente $adicionalSelect
			FROM (articulo)
			INNER JOIN usuario ON usuario.id=articulo.usuario and usuario.estado<>'Baneado'
			INNER JOIN pais ON pais.codigo3=usuario.pais
			INNER JOIN ciudad ON ciudad.id=usuario.ciudad
			WHERE terminado = 0 and articulo.estado<>'Baneado'
			$extra
			$orderby";
			
			$res = $this->db->query ( "CREATE  TABLE $nameBD(
					cantidad int(10) NOT NULL,
					id int(10) unsigned not null,
					titulo varchar(120) not null,
					tipo enum('Fijo', 'Subasta', 'Cantidad') not null,
					precio decimal(10,2) unsigned not null,
					fecha_registro datetime not null,
					duracion int(10) unsigned NULL,
					usuario int(10) unsigned not null,
					foto text not null,
					pais_nombre char(52) not null,
					ciudad_nombre char(35) not null,
					categoria int(11) not null,
					codigo3 varchar(3) null,
					continente varchar(50) null,
					$adicionaCreate
					PRIMARY KEY (`id`),
					UNIQUE KEY `id` (`id`)
					) CHARSET=utf8 COLLATE=utf8_general_ci;" );
			
			$res = $this->db->query ( "insert into $nameBD(cantidad,id,titulo,tipo,precio,fecha_registro,duracion,usuario,foto,pais_nombre,ciudad_nombre,categoria,codigo3,continente $adicionaInsert)($query);" );
		}
		return $nameBD;
	}
	
	// mas fecha menos relevancia
	public function listarArticulosXCriterioFecha($criterio = false, $tipo = false, $orden = false, $ubicacion = false, $categoria = false, $idioma = false, $usuario = false, $inicio = 0, $total = 10) {
		$tiempo = time ();
		$query = $this->prepararConsultaArticuloXCriterioFecha ( $criterio, $tipo, $orden, $ubicacion, $categoria, $idioma, $usuario );
		$lquery = "$query limit $inicio," . ($inicio + $total);
		$cquery = "select count(id) as total from ($query) as x;";
		$res = $this->db->query ( $lquery );
		if (is_object ( $res )) {
			$cres = $this->db->query ( $cquery );
			$totalRes = 0;
			if (is_object ( $cres )) {
				$cres = $cres->result ();
				$totalRes = $cres [0]->total;
			}
			if ($inicio < $totalRes) {
				$total = ($totalRes < $inicio + $total) ? $totalRes - $inicio : $total;
				$datos = array ();
				$datos = $res->result ();
				/*
				 * for($i = $inicio; $i < $inicio + $total; $i ++) { $datos [] =
				 * $res->row ( $i ); }
				 */
				$query = "select categoria,count(categoria)as cantidad from ($query ) as s group by categoria";
				$res = $this->db->query ( $query );
				$categorias = $this->darResuts ( $res );
				return array (
						$totalRes,
						$datos,
						$categorias 
				);
			}
		}
		return false;
	}
	public function listarArticulosXCriterio($criterio = false, $tipo = false, $categoria = false, $inicio = 0, $total = 10) {
		if ($criterio) {
			$this->db->like ( array (
					"titulo" => $criterio 
			) );
		}
		if ($tipo) {
			$this->db->like ( array (
					"tipo" => $tipo 
			) );
		}
		return $this->listarArticulosXFecha ( $categoria, $inicio, $total );
	}
	public function listarArticulosXFecha($categoria = false, $inicio = 0, $total = 10) {
		if ($categoria) {
			$this->db->where ( array (
					"categoria" => $categoria 
			) );
		}
		$this->db->where ( array (
				"terminado" => 0 
		) );
		$this->db->select ( "articulo.*,pais.nombre as pais_nombre" );
		$this->db->join ( "usuario", "usuario.id=articulo.usuario", "inner" );
		$this->db->join ( "pais", "pais.codigo3=usuario.pais", "inner" );
		$this->db->order_by ( "fecha_registro", "desc" );
		return $this->darTodos ( "articulo", $total, $inicio );
	}
	public function procesarArticulos(&$lista) {
		if ($lista && is_array ( $lista ) && count ( $lista ) > 0) {
			$usuarios = array ();
			$this->load->model ( "Usuario_model", "usuarioM" );
			foreach ( $lista as $articulo ) {
				$u = $articulo->usuario;
				if (is_object ( $u )) {
					$u = $u->id;
				}
				if (! isset ( $usuarios [$u] )) {
					$usuarios [$u] = $this->usuarioM->darUsuarioXId ( $u );
				}
				$articulo->usuario = $usuarios [$u];
				if ($articulo->tipo == "Subasta") {
					$c = $this->mayorOferta ( $articulo->id, true );
					if ($c) {
						$articulo->mayorPuja = $c->monto_automatico;
						$articulo->cantidadPujas = $c->cantidad;
					} else {
						$articulo->mayorPuja = $articulo->precio;
						$articulo->cantidadPujas = 0;
					}
				} else {
					$c = $this->mayorOferta ( $articulo->id );
					if ($c) {
						$articulo->mayorOferta = $c->monto_automatico;
						$articulo->cantidadOfertas = $c->cantidad;
					} else {
						$articulo->mayorOferta = 0;
						$articulo->cantidadOfertas = 0;
					}
				}
				if (isset ( $articulo->comprador ) && $articulo->comprador) {
					$u = $articulo->comprador;
					if (is_object ( $u )) {
						$u = $u->id;
					}
					if (! isset ( $usuarios [$u] )) {
						$usuarios [$u] = $this->usuarioM->darUsuarioXId ( $u );
					}
					$articulo->comprador = $usuarios [$u];
				}
			}
		}
	}
	public function contarArticulosXFecha($categoria = false) {
		if ($categoria) {
			$this->db->where ( array (
					"categoria" => $categoria 
			) );
		}
		$this->db->where ( array (
				"terminado" => 0 
		) );
		$this->db->select ( "articulo.*,pais.nombre as pais_nombre" );
		$this->db->join ( "usuario", "usuario.id=articulo.usuario", "inner" );
		$this->db->join ( "pais", "pais.codigo3=usuario.pais", "inner" );
		$this->db->order_by ( "fecha_registro", "desc" );
		$this->db->from ( "articulo" );
		return $this->db->count_all_results ();
	}
	public function adicionarCantidad($categoria) {
		$this->db->where ( array (
				"id" => $categoria 
		) );
		$this->db->select ( "cantidad,padre" );
		$c = $this->darUno ( "categoria" );
		if ($c) {
			if ($this->db->update ( "categoria", array (
					"cantidad" => intval ( $c->cantidad ) + 1 
			), array (
					"id" => $categoria 
			) )) {
				if ($c->padre) {
					return $this->adicionarCantidad ( $c->padre );
				}
				return true;
			}
		}
		return false;
	}
	public function quitarCantidad($categoria) {
		$this->db->where ( array (
				"id" => $categoria 
		) );
		$this->db->select ( "cantidad,padre" );
		$c = $this->darUno ( "categoria" );
		if ($c) {
			if ($this->db->update ( "categoria", array (
					"cantidad" => intval ( $c->cantidad ) - 1 
			), array (
					"id" => $categoria 
			) )) {
				if ($c->padre) {
					return $this->quitarCantidad ( $c->padre );
				}
				return true;
			}
		}
		return false;
	}
	public function listarNotas($articulo) {
		$this->db->where ( array (
				"articulo" => $articulo 
		) );
		return $this->darTodos ( "aclaracion" );
	}
	public function adicionarNota($articulo, $nota) {
		return $this->db->insert ( "aclaracion", array (
				"articulo" => $articulo,
				"texto" => strip_tags ( $nota ),
				"fecha" => date ( "Y-m-d H:i:s" ) 
		) );
	}
	public function adicionarVisita($articulo, $usuario) {
		$this->db->where ( array (
				"id" => $articulo 
		) );
		if ($usuario) {
			$this->db->where ( array (
					"usuario !=" => $usuario->id 
			) );
		}
		$this->db->select ( "visita" );
		$v = $this->darUno ( "articulo" );
		if ($v) {
			return $this->db->update ( "articulo", array (
					"visita" => intval ( $v->visita ) + 1 
			), array (
					"id" => $articulo 
			) );
		}
		return false;
	}
	private function darUno($tabla) {
		$res = $this->db->get ( $tabla )->result ();
		if ($res && is_array ( $res ) && count ( $res ) > 0) {
			return $res [0];
		}
		return false;
	}
	private function darResuts($res) {
		if ($res) {
			$res = $res->result ();
			if ($res && is_array ( $res ) && count ( $res ) > 0) {
				return $res;
			}
		}
		return false;
	}
	private function darTodos($tabla, $inicio = false, $total = false) {
		$res = $this->db->get ( $tabla, $inicio, $total );
		return $this->darResuts ( $res );
	}
	public function numArticulosXCategoriaXUser($user) {
		return $this->numArticulosXCategoria ( $user );
	}
	public function numArticulosXCategoria($user = false) {
		$res = $this->db->query ( "SELECT count(a.id) as cantidad,a.categoria as categoria FROM articulo as a where " . ($user ? "a.usuario= $user and" : "") . " a.terminado=0 group by a.categoria" );
		return $this->darResuts ( $res );
	}
	public function leerMensajes($usuario, $pagina = 1) {
		$sql = "select m.*,ue.seudonimo emisor_seudonimo,ue.imagen,ur.seudonimo receptor_seudonimo,ur.imagen
		from mensaje m
		inner join usuario ur on ur.id=m.receptor
		inner join usuario ue on ue.id=m.emisor
		where  m.id in (
		select id from mensaje
		where receptor='$usuario'
		group by  emisor
		order by fecha desc)
		or m.id in (
		select id from mensaje
		where emisor='$usuario'
		group by receptor
		order by fecha desc)";
		// var_dump($sql);
		return array ();
	}
	public function articulosSeguidos($pagina = 1, $criterio = false, $section = false, $orden = false, $ubicacion = false, $categoria = false, $usuario = false, $limite = false) {
		$totalpagina = $this->configuracion->variables ( "cantidadPaginacion" );
		$pagina = intval ( $pagina );
		$pagina = $pagina > 0 ? $pagina : 1;
		$inicio = ($pagina - 1) * $totalpagina;
		
		// $data ["categorias"] = $this->darCategorias ( $categoria );
		$data ["inicio"] = $inicio;
		$data ["totalpagina"] = $totalpagina;
		$data ["criterio"] = $criterio;
		switch ($section) {
			case "item" :
				$tipo = "Fijo";
				break;
			case "auction" :
				$tipo = "Subasta";
				break;
			default :
				$tipo = false;
				break;
		}
		
		$x = $this->listarArticulosXCriterioFecha2 ( $criterio, $tipo, $orden, $ubicacion, $categoria, false, $usuario, $inicio, $totalpagina, $limite );
		// print ($this->db->last_query ()) ;
		if ($x) {
			list ( $data ["total"], $data ["articulos"], $categorias ) = $x;
			
			if (trim ( $criterio ) !== "" || $usuario) {
				$data ["categorias"] = $this->darCategorias2 ( $categoria, $categorias, true );
			} else {
				$data ["categorias"] = $this->darCategorias ( $categoria );
			}
			$data ["categorias"] = $this->ordenarArbol ( $data ["categorias"] );
			$this->procesarArticulos ( $data ["articulos"] );
		}
		return $data;
	}
	public function listarArticulosXCriterioFecha2($criterio = false, $tipo = false, $orden = false, $ubicacion = false, $categoria = false, $idioma = false, $usuario = false, $inicio = 0, $total = 10, $limite = false) {
		$query = $this->prepararConsultaArticuloXCriterioFecha2 ( $criterio, $tipo, $orden, $ubicacion, $categoria, $idioma, $usuario, $limite );
		
		$res = $this->db->query ( $query );
		if ($res) {
			$totalRes = $res->num_rows ();
			if ($inicio < $totalRes) {
				$total = ($totalRes < $inicio + $total) ? $totalRes - $inicio : $total;
				$datos = array ();
				for($i = $inicio; $i < $inicio + $total; $i ++) {
					$datos [] = $res->row ( $i );
				}
				$query = "select categoria,count(categoria)as cantidad from ($query ) as s group by categoria";
				$res = $this->db->query ( $query );
				$categorias = $this->darResuts ( $res );
				return array (
						$totalRes,
						$datos,
						$categorias 
				);
			}
		}
		return false;
	}
	public function prepararConsultaArticuloXCriterioFecha2($criterio = false, $tipo = false, $orden = false, $ubicacion = false, $categoria = false, $idioma = false, $usuario = false, $limite = false) {
		if (trim ( $orden ) === "" && $usuario) {
			$orden = "finaliza";
		}
		$criterio = explode ( " ", trim ( preg_replace ( "/(\s+)/im", " ", $criterio ) ) );
		if ($criterio && is_array ( $criterio ) && count ( $criterio ) > 0) {
			$precio = "articulo.precio";
			$orderby = "ORDER BY fecha_registro desc";
			$adicionalSelect = "";
			$extra = "";
			if ($ubicacion) {
				$ubicacion = explode ( "-", $ubicacion );
				if (count ( $ubicacion ) > 1) {
					switch ($ubicacion [0]) {
						case "P" :
							$extra .= " and pais.codigo3='" . $ubicacion [1] . "' ";
							break;
						
						default :
							$extra .= " and pais.continente='" . $ubicacion [1] . "' ";
							break;
					}
				}
			}
			switch ($orden) {
				
				case "mas-alto" :
					$precio = "if(articulo.tipo='Fijo',articulo.precio,mayorPuja(articulo.id)) as precio";
					$orderby = "ORDER BY precio desc";
					break;
				case "mas-bajo" :
					$precio = "if(articulo.tipo='Fijo',articulo.precio,mayorPuja(articulo.id)) as precio";
					$orderby = "ORDER BY precio asc";
					break;
			}
			$ors = array_merge ( array (), $criterio );
			if (count ( $ors ) > 0) {
				$extra .= "and (";
				if (count ( $ors ) > 0) {
					$extra .= "(";
					$tors = array ();
					foreach ( $ors as $o ) {
						$tors [] = "titulo like '%" . str_replace ( "'", "\\'", $o ) . "%'";
					}
					$extra .= implode ( " and ", $tors );
					$extra .= ")";
				}
				$extra .= ")";
			}
			if ($tipo) {
				if ($tipo !== "Fijo") {
					$extra .= " and articulo.tipo='$tipo'";
				} else {
					$extra .= " and (articulo.tipo='$tipo' or articulo.tipo='Cantidad')";
				}
			}
			if ($usuario) {
				$extra .= " and siguiendo.usuario='$usuario'";
			}
			if ($categoria) {
				$this->load->model ( "categoria_model", "categoria_model" );
				$ids = $this->categoria_model->darArbolHijos ( $categoria );
				if ($ids && is_array ( $ids ) && count ( $ids ) > 0) {
					$extra .= " and categoria in(" . implode ( ",", $ids ) . ")";
				}
			}
			
			$listado = "";
			if ($limite != false) {
				$totalpagina = $this->configuracion->variables ( "cantidadPaginacion" );
				$listado = "limit $limite, $totalpagina ";
			}
			
			$query = "SELECT articulo.terminado, articulo.cantidad,siguiendo.id as idseguimiento, siguiendo.usuario as usuarioseguimiento,articulo.id,articulo.titulo,articulo.tipo,$precio ,articulo.fecha_registro,articulo.duracion,articulo.usuario,articulo.foto, articulo.categoria as categoria $adicionalSelect
			FROM (articulo,siguiendo)
			WHERE articulo.id=siguiendo.articulo and siguiendo.usuario=$usuario
			$orderby $listado ";
			return $query;
		}
		return false;
	}
	public function darreporteXpaquete($paquete, $estado) {
		$this->db->where ( array (
				"paquete" => $paquete,
				"asunto" => $estado 
		) );
		return $this->darUno ( "reporte" );
	}
	public function cambiarXreporte($id, $estado) {
		$this->db->update ( "reporte", array (
				"estado" => "$estado" 
		), array (
				"id" => $id 
		) );
	}
	
	// probando contador//
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
								
								$data ['mensaje'] = $row->mensaje; // 0
							} else {
								
								$data ['mensaje'] = ($row->mensaje); // 0
							}
							
							$cadenadireccion = '';
							$cadenaseu = '';
							
							if ($emisor == false) {
								if ($row->emisor == $id) {
									$data ['receptor'] = $row->emisor;
									$cadenaseu = $row->emisor;
									$data ['emisor'] = $row->receptor;
									$cadenadireccion = $row->receptor;
									$data ['estado'] = $row->estado_receptor; // 1
									
									$data ['estadousuario'] = $row->estadousuario2;
								} else {
									$data ['emisor'] = $row->emisor; // 13
									$cadenadireccion = $row->emisor;
									$data ['receptor'] = $row->receptor;
									$cadenaseu = $row->receptor;
									$data ['seudonimo'] = $row->seudonimo; // 2
									$data ['estado'] = $row->estado; // 1
									
									$data ['estadousuario'] = $row->estadousuario;
								}
							} else {
								$data ['emisor'] = $row->emisor; // 13
								$cadenadireccion = $row->emisor;
								$data ['receptor'] = $row->receptor;
								$cadenaseu = $row->receptor;
								$data ['seudonimo'] = $row->seudonimo; // 2
								$data ['estado'] = $row->estado; // 1
								
								$data ['estadousuario'] = $row->estadousuario;
							}
							
							$data ['id'] = $row->id; // 14
							
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
							
							$data ['mensaje'] = ($row->mensaje); // 0
							
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
						
						$data ['mensaje'] = $row->mensaje; // 0
						
						$data ['receptor'] = '';
						$data ['emisor'] = '';
						$data ['seudonimo'] = $row->seudonimo;
						
						$data ['id'] = $row->id;
						
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
	public function resetTemporal() {
		$this->db->query ( "drop table tmp_busqueda_finaliza" );
		$this->db->query ( "drop table tmp_busqueda_mas_alto" );
		$this->db->query ( "drop table tmp_busqueda_mas_bajo" );
		$this->db->query ( "drop table tmp_busqueda_ultimos" );
	}
	public function llenarTemporal() {
		$this->crearTemporal ( "finaliza" );
		$this->crearTemporal ( "mas_alto" );
		$this->crearTemporal ( "mas_bajo" );
		$this->crearTemporal ( "ultimos" );
	}
}
?>