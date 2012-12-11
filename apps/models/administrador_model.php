<?php
class Administrador_model extends CI_Model {
	public function __construct() {
		parent::__construct ();
	}
	public function datos($estado = false, $valor = false, $tipo = false, $limiteinicio, $limitcantidad) {
		$this->db->select ( 'reporte.id, reporte.asunto, reporte.estado, marcador.seudonimo as markby,
						u.id as denunciante,u.positivo as denunciantevotopos, u.negativo as denunciantevotoneg, u.seudonimo as denuncianteseudonimo, u.estado as estadou,
						reporte.paquete, paq.articulos as articulopaq, paq.transacciones, paq.estado as estadopaquete,
						reporte.estado, reporte.fecha, reporte.fecha_ultimo as datemark, 
						ar.id as idtitulo,ar.estado as estado_articulo, ar.estado_anterior as estadoarticuloanterior,
						u3.id as idusuarioarticulo, u3.positivo as posiusuarioarticulo, u3.negativo as negusuarioarticulo, u3.seudonimo as seuusuarioarticulo, u3.estado as estadou3,
						u2.id as idusuario, u2.positivo as posiusuario, u2.negativo as negusuario, u2.seudonimo as seuusuario, u2.estado as estadou2,
						reporte.descripcion, reporte.mensaje, mensj.denuncia' );
		$this->db->from ( 'reporte' );
		$this->db->join ( 'usuario as u', 'reporte.usuario = u.id', 'left' );
		$this->db->join ( 'usuario as u2', 'reporte.perfil = u2.id', 'left' );
		$this->db->join ( 'usuario as marcador', 'reporte.marcado_por = marcador.id', 'left' );
		$this->db->join ( 'articulo as ar', 'reporte.articulo = ar.id', 'left' );
		$this->db->join ( 'usuario as u3', 'u3.id = ar.usuario', 'left' );
		$this->db->join ( 'paquete as paq', 'paq.id = reporte.paquete', 'left' );
		$this->db->join ( 'mensaje as mensj', 'mensj.id = reporte.mensaje', 'left' );
		// LEFT JOIN usuario as u3 on u3.id = ar.usuario
		if ($estado != FALSE) {
			$this->db->where ( 'reporte.estado', $estado );
		}
		if ($valor != FALSE) {
			if ($tipo == 'usuario') {
				$this->db->like ( 'u.seudonimo', $valor );
				$this->db->or_like ( 'u2.seudonimo', $valor );
				$this->db->or_like ( 'u3.seudonimo', $valor );
			} else {
				if ($tipo == 'reporte') {
					$this->db->like ( 'reporte.id', $valor );
				} else {
					$this->db->like ( 'ar.id', $valor );
					$this->db->or_like ( 'u2.id', $valor );
				}
			}
		}
		$this->db->order_by ( 'reporte.fecha', 'DESC' );
		$this->db->limit ( $limitcantidad, $limiteinicio );
		$res = $this->db->get ();
		
		if ($res) {
			$res = $res->result ();
			if ($res && is_array ( $res ) && count ( $res ) > 0) {
				return $res;
			}
		}
		return false;
	}
	public function cambiarestado($id, $estado, $usuario) {
		$data = array (
				'estado' => $estado,
				'marcado_por' => $usuario,
				'fecha_ultimo' => strftime ( "%Y-%m-%d-%H-%M-%S", time () ) 
		);
		
		$this->db->where ( 'id', $id );
		$this->db->update ( 'reporte', $data );
	}
	public function contarreporte($estado = false) {
		$this->db->select ( 'reporte.id' );
		$this->db->from ( 'reporte' );
		$this->db->join ( 'usuario as u', 'reporte.usuario = u.id', 'left' );
		$this->db->join ( 'usuario as u2', 'reporte.perfil = u2.id', 'left' );
		$this->db->join ( 'articulo as ar', 'reporte.articulo = ar.id', 'left' );
		$this->db->join ( 'usuario as u3', 'u3.id = ar.usuario', 'left' );
		
		if ($estado != FALSE) {
			$this->db->where ( 'reporte.estado', $estado );
		}
		$res = $this->db->count_all_results ();
		
		return $res;
	}
	public function devolverdatousuario($id, $tipo, $asunto) {
		// $this->db->select('usu.seudonimo');
		$this->db->select_sum ( 'vot.cantidad' );
		$this->db->from ( 'voto as vot' );
		$this->db->join ( 'usuario as usu', 'usu.id = vot.usuario' );
		$this->db->where ( 'usu.id', $id );
		$this->db->where ( 'vot.tipo', $tipo );
		$this->db->where ( 'asunto', $asunto );
		
		$res = $this->db->get ();
		
		if ($res) {
			$res = $res->result ();
			if ($res && is_array ( $res ) && count ( $res ) > 0) {
				return $res;
			}
		}
		return false;
		
		/*
		 * select sum(vot.cantidad), usu.seudonimo from voto as vot left join
		 * usuario as usu on usu.id = vot.usuario where usu.id = 78 and
		 * tipo='positivo' and asunto = 'Venta'
		 */
	}
	public function guardarvoto($id, $posi, $neg, $votoposiventa, $votoposicompra, $votonegventa, $votonegcompra) {
		$data = array (
				'positivo' => $posi + $votoposiventa + $votoposicompra,
				'negativo' => $neg + $votonegventa + $votonegcompra 
		);
		
		$this->db->where ( 'id', $id );
		$this->db->update ( 'usuario', $data );
		
		if ($votoposiventa > 0) {
			$data1 = array (
					'usuario' => $id,
					'tipo' => 'Positivo',
					'asunto' => 'Venta',
					'fecha' => strftime ( "%Y-%m-%d-%H-%M-%S", time () ),
					'cantidad' => $votoposiventa 
			);
			
			$this->db->insert ( 'voto', $data1 );
		}
		
		if ($votoposicompra > 0) {
			$data2 = array (
					'usuario' => $id,
					'tipo' => 'Positivo',
					'asunto' => 'Compra',
					'fecha' => strftime ( "%Y-%m-%d-%H-%M-%S", time () ),
					'cantidad' => $votoposicompra 
			);
			$this->db->insert ( 'voto', $data2 );
		}
		
		if ($votonegventa > 0) {
			$data3 = array (
					'usuario' => $id,
					'tipo' => 'Negativo',
					'asunto' => 'Venta',
					'fecha' => strftime ( "%Y-%m-%d-%H-%M-%S", time () ),
					'cantidad' => $votonegventa 
			);
			$this->db->insert ( 'voto', $data3 );
		}
		
		if ($votonegcompra > 0) {
			$data4 = array (
					'usuario' => $id,
					'tipo' => 'Negativo',
					'asunto' => 'Compra',
					'fecha' => strftime ( "%Y-%m-%d-%H-%M-%S", time () ),
					'cantidad' => $votonegcompra 
			);
			$this->db->insert ( 'voto', $data4 );
		}
	}
	
	// factura
	public function devolverTodoFactura($inicio = false, $limite = false) {
		$this->db->select ( 'id, codigo, mes, usuario, fecha, articulos, monto_total, monto_tarifa, iva, estado, tipo_tarifa, paypal_id' );
		$this->db->from ( 'factura' );
		$this->db->limit ( $limite, $inicio );
		$res = $this->db->get ();
		
		if ($res) {
			$res = $res->result ();
			if ($res && is_array ( $res ) && count ( $res ) > 0) {
				return $res;
			}
		}
		return false;
	}
	public function devolverfactura($estado = false, $valor = false, $tipo = false, $mes) {
		$this->load->model ( "articulo_model", "articulo" );
		$this->load->model ( "usuario_model", "usuarioM" );
		$this->db->order_by ( "mes desc" );
		$extraJoin = "";
		if ($estado != FALSE) {
			if ($estado !== "En curso") {
				$this->db->where ( array (
						'factura.estado' => $estado 
				) );
			} else {
				$extraJoin = " and factura.estado<>'Pendiente' and factura.estado<>'Pagado'";
			}
		}
		$anio = date ( "Y" );
		if ($valor != FALSE) {
			if ($tipo == 'usuario') {
				$this->db->like ( 'usuario.seudonimo', $valor );
			} else {
				if ($tipo == 'factura') {
					$this->db->like ( 'factura.id', $valor );
				}
			}
		}
		$this->db->join ( "factura", "usuario.id =factura.usuario and factura.mes='$mes' $extraJoin", "left" );
		$this->db->select ( "factura.*,usuario.id as idusu,usuario.seudonimo,usuario.positivo,usuario.negativo" );
		$facturas = $this->db->get ( "usuario" )->result ();
		// print $this->db->last_query ();
		if ($facturas && is_array ( $facturas ) && count ( $facturas ) > 0) {
			$nc = 0;
			$r = $this->db->query ( "SELECT substr(codigo,1,length(codigo)-5) as nc from factura where substr(codigo,length(codigo)-3)='$anio' order by codigo desc limit 0,1" )->result ();
			if ($r && is_array ( $r ) && count ( $r ) > 0) {
				$nc = intval ( $r [0]->nc );
			}
			$nc ++;
			foreach ( $facturas as $i => $factura ) {
				if (! $factura->id) {
					$usuario = $this->usuarioM->darUsuarioXId ( $factura->idusu );
					$estemes = $this->articulo->darDatosCuentas ( $mes, $anio, $usuario );
					if ($estemes) {
						$factura->id = "x";
						$factura->codigo = "$nc/2012";
						$factura->mes = $estemes->mes;
						$factura->usuario = $estemes->usuario;
						$factura->fecha = $estemes->fecha;
						$factura->articulos = $estemes->articulos;
						$factura->monto_stock = $estemes->monto_stock;
						$factura->monto_venta = $estemes->monto_venta;
						$factura->monto_tarifa = $estemes->monto_tarifa;
						$factura->estado = "En Curso";
						$factura->tipo_tarifa = $usuario->tipo_tarifa;
						$factura->iva = $estemes->iva;
						$nc ++;
					}
				}
			}
			for($i = 0; $i < count ( $facturas ); $i ++) {
				if (! $facturas [$i]->id) {
					array_splice ( $facturas, $i, 1 );
					$i --;
				}
			}
			return $facturas;
		}
		
		return array ();
	}
	public function devuelvegrupofechas() {
		$this->db->select ( 'mes' );
		$this->db->from ( 'factura' );
		$this->db->group_by ( 'mes' );
		
		$res = $this->db->get ();
		
		if ($res) {
			$res = $res->result ();
			if ($res && is_array ( $res ) && count ( $res ) > 0) {
				return $res;
			}
		}
		return false;
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
		
		if ($usuario->tipo_tarifa == "Comision") {
			$this->db->where ( "fecha_envio between '" . date ( "$anio-$mes-01 00:00:00" ) . "' and '" . date ( "$anio-$mes-t 23:59:59" ) . "' and vendedor='$usuario->id'" );
			$r = $this->db->get ( "paquete" )->result ();
			if ($r && is_array ( $r ) && count ( $r ) > 0) {
				$paquetes = $r;
			}
			$articulos = array ();
			$transacciones = array ();
			
			if (isset ( $paquetes )) {
				foreach ( $paquetes as $p ) {
					$articulos = array_merge ( $articulos, explode ( ",", $p->articulos ) );
					$transacciones = array_merge ( $transacciones, explode ( ",", $p->transacciones ) );
				}
			}
			$as = array ();
			$ts = array ();
			
			foreach ( $articulos as $a ) {
				$a = $this->darArticulo ( $a );
				if ($a) {
					$total += floatval ( $a->precio_oferta ? $a->precio_oferta : $a->precio );
					$as [] = $a;
				}
			}
			foreach ( $transacciones as $t ) {
				$r = $this->db->where ( array (
						"id" => $t 
				) )->get ( "transacciones" )->result ();
				if ($r && is_array ( $r ) && count ( $r ) > 0) {
					$total += floatval ( $r [0]->precio * $r [0]->cantidad );
					$ts [] = $r [0];
				}
			}
			if (! isset ( $paquetes )) {
				// print "no hay facturas<br/>";
				return false;
			}
			
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
			
			$this->db->where ( array (
					"terminado" => 0,
					"usuario" => $usuario->id 
			) );
			$rs = $this->db->get ( "articulo" )->result ();
			$articulos = array ();
			foreach ( $rs as $a ) {
				$total += $a->precio;
				$articulos [] = $a->id;
				if ($a->tipo == "Subasta") {
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
				}
				$monto += $tarifa;
			}
			
			$monto += $this->calcularTarifa ( false, "Plana", $usuario->id );
		}
		$nc = 0;
		$r = $this->db->query ( "SELECT substr(codigo,1,length(codigo)-5) as nc from factura where substr(codigo,length(codigo)-3)='$anio' order by codigo desc limit 0,1" )->result ();
		if ($r && is_array ( $r ) && count ( $r ) > 0) {
			$nc = intval ( $r [0]->nc );
		}
		$nc ++;
		$iva = $monto * 0.18;
		$x = new stdClass ();
		$x->codigo = "$nc/$anio";
		$x->mes = "$mes-$anio";
		$x->usuario = $usuario->id;
		$x->fecha = date ( "Y-m-d H:i:s" );
		$x->articulos = isset ( $articulos ) ? implode ( ",", $articulos ) : null;
		$x->monto_total = $total;
		$x->monto_tarifa = $monto;
		$x->iva = $iva;
		return $x;
	}
	public function contarfactura($estado = false, $mes) {
		$this->db->select ( 'id' );
		$this->db->from ( 'factura' );
		
		if ($estado != FALSE) {
			$this->db->where ( 'estado', $estado );
		}
		$this->db->where ( "mes", $mes );
		$res = $this->db->count_all_results ();
		
		return $res;
	}
	public function contarfacturaXestadoXusuario($usuario, $estado) {
		$this->db->select ( 'id' );
		$this->db->from ( 'factura' );
		
		$this->db->where ( array (
				'estado' => $estado,
				'usuario' => $usuario 
		) );
		
		$res = $this->db->count_all_results ();
		
		return $res;
	}
	// admipm
	public function mensajeadmin($seudonimo = false) {
		$like = "";
		if ($seudonimo != false) {
			$like = " and usuario.seudonimo like '$seudonimo'";
		}
		$res = $this->db->query ( "select noti.id,usua.id as idusuario, usua.seudonimo, noti.mensaje, noti.fecha
					,TIMESTAMPDIFF(YEAR,TIMESTAMP(fecha),TIMESTAMP(now())) as anios
    				,TIMESTAMPDIFF(MONTH,TIMESTAMP(fecha),TIMESTAMP(now())) as meses
    				,TIMESTAMPDIFF(WEEK,TIMESTAMP(fecha),TIMESTAMP(now())) as semanas
    				,TIMESTAMPDIFF(DAY,TIMESTAMP(fecha),TIMESTAMP(now())) as dias
    				,TIMESTAMPDIFF(HOUR,TIMESTAMP(fecha),TIMESTAMP(now())) as horas
    				,TIMESTAMPDIFF(MINUTE,TIMESTAMP(fecha),TIMESTAMP(now())) as minutos
    				,TIMESTAMPDIFF(SECOND,TIMESTAMP(fecha),TIMESTAMP(now())) as segundos 
				from notificacion as noti 
				left join usuario as usua on usua.id = noti.emisor
				where isnull(noti.receptor) $like
				order by segundos asc
				" );
		if ($res) {
			$res = $res->result ();
			if ($res && is_array ( $res ) && count ( $res ) > 0) {
				return $res;
			}
		}
		return false;
	}
	public function guardarnotificacion($id, $mensaje) {
		$data = array (
				'emisor' => $id,
				'mensaje' => $mensaje,
				'fecha' => strftime ( "%Y-%m-%d %H:%M:%S", time () ) 
		);
		
		$this->db->insert ( 'notificacion', $data );
	}
	public function guardarnotificacion2($id, $mensaje) {
		$data = array (
				'emisor' => $id,
				'mensaje' => $mensaje,
				'fecha' => strftime ( "%Y-%m-%d %H:%M:%S", time () ) 
		);
		
		$this->db->insert ( 'notificacion', $data );
	}
	
	// mensajepm
	public function guardarmensajepm($id, $mensaje) {
		$data = array (
				'receptor' => $id,
				'mensaje' => $mensaje,
				'fecha' => strftime ( "%Y-%m-%d %H:%M:%S", time () ),
				'estado' => 'Pendiente',
				'visible' => 0,
				'tipo' => 'Admin' 
		);
		
		$this->db->insert ( 'mensaje', $data );
	}
	public function vermensajeXusuario($idusuario) {
		$this->db->select ( 'id' );
		$this->db->from ( 'mensaje' );
		$this->db->where ( "(receptor = $idusuario or emisor= $idusuario) and tipo='Admin'" );
		$this->db->where ( "(visible = 0 or visible=1)" );
		
		$res = $this->db->count_all_results ();
		
		return $res;
	}
	public function banearcampos($id, $tipo, $tabla) {
		$data = array (
				'estado' => $tipo 
		);
		
		$this->db->where ( 'id', $id );
		$this->db->update ( "usuario", $data );
	}
	public function devolverXrepoter($idreporte) {
		$this->db->select ( 'reporte.id, reporte.asunto, reporte.estado, marcador.seudonimo as markby,
						u.id as denunciante,u.positivo as denunciantevotopos, u.negativo as denunciantevotoneg, u.seudonimo as denuncianteseudonimo, u.estado as estadou,
						reporte.paquete, paq.articulos as articulopaq, paq.transacciones, paq.estado as estadopaquete,
						reporte.estado, reporte.fecha, reporte.fecha_ultimo as datemark, 
						ar.id as idtitulo,ar.estado as estado_articulo, ar.estado_anterior as estadoarticuloanterior,
						u3.id as idusuarioarticulo, u3.positivo as posiusuarioarticulo, u3.negativo as negusuarioarticulo, u3.seudonimo as seuusuarioarticulo, u3.estado as estadou3,
						u2.id as idusuario, u2.positivo as posiusuario, u2.negativo as negusuario, u2.seudonimo as seuusuario, u2.estado as estadou2,
						reporte.descripcion, reporte.mensaje, mensj.denuncia' );
		$this->db->from ( 'reporte' );
		$this->db->join ( 'usuario as u', 'reporte.usuario = u.id', 'left' );
		$this->db->join ( 'usuario as u2', 'reporte.perfil = u2.id', 'left' );
		$this->db->join ( 'usuario as marcador', 'reporte.marcado_por = marcador.id', 'left' );
		$this->db->join ( 'articulo as ar', 'reporte.articulo = ar.id', 'left' );
		$this->db->join ( 'usuario as u3', 'u3.id = ar.usuario', 'left' );
		$this->db->join ( 'paquete as paq', 'paq.id = reporte.paquete', 'left' );
		$this->db->join ( 'mensaje as mensj', 'mensj.id = reporte.mensaje', 'left' );
		// LEFT JOIN usuario as u3 on u3.id = ar.usuario
		
		$this->db->where ( 'reporte.id', $idreporte );
		
		$res = $this->db->get ();
		
		if ($res) {
			$res = $res->result ();
			if ($res && is_array ( $res ) && count ( $res ) > 0) {
				return $res;
			}
		}
		return false;
	}
	
	// reporte
	public function guardarreporte($reportador, $reportado, $idmensaje, $descripcion, $motivo) {
		$data = array (
				'asunto' => $motivo,
				'usuario' => $reportador,
				'fecha' => strftime ( "%Y-%m-%d %H:%M:%S", time () ),
				'perfil' => $reportado,
				'descripcion' => $descripcion,
				'mensaje' => $idmensaje,
				'estado' => 'Procesando' 
		);
		
		$this->db->insert ( 'reporte', $data );
		
		$data2 = array (
				
				'denuncia' => 'Denunciado' 
		);
		
		$this->db->where ( 'id', $idmensaje );
		$this->db->update ( 'mensaje', $data2 );
	}
	public function devolvermensajeX($pagina) {
		$this->db->select ( "mensaje.id, mensaje.receptor, mensaje.emisor, mensaje.mensaje, mensaje.fecha, mensaje.estado, mensaje.articulo, mensaje.estado_receptor, usuario.seudonimo, usuario.imagen,
				mensaje.tipo as 'tipomensaje'
				,TIMESTAMPDIFF(YEAR,TIMESTAMP(fecha),TIMESTAMP(now())) as anios
				,TIMESTAMPDIFF(MONTH,TIMESTAMP(fecha),TIMESTAMP(now())) as meses
				,TIMESTAMPDIFF(WEEK,TIMESTAMP(fecha),TIMESTAMP(now())) as semanas
				,TIMESTAMPDIFF(DAY,TIMESTAMP(fecha),TIMESTAMP(now())) as dias
				,TIMESTAMPDIFF(HOUR,TIMESTAMP(fecha),TIMESTAMP(now())) as horas
				,TIMESTAMPDIFF(MINUTE,TIMESTAMP(fecha),TIMESTAMP(now())) as minutos
				,TIMESTAMPDIFF(SECOND,TIMESTAMP(fecha),TIMESTAMP(now())) as segundos", false );
		
		$this->db->from ( 'mensaje' );
		$this->db->join ( 'usuario', 'usuario.id = mensaje.emisor' );
		
		$this->db->where ( 'mensaje.id', $pagina );
		
		$res = $this->db->get ();
		if ($res) {
			$res = $res->result ();
			if ($res && is_array ( $res ) && count ( $res ) > 0) {
				return $res;
			}
		}
		return false;
	}
	
	// pageid
	public function baneararticulo($id, $estado, $estadoanterior = false) {
		if ($estadoanterior == false) {
			$data = array (
					
					'estado' => $estado 
			);
		} else {
			$data = array (
					
					'estado' => $estado,
					'estado_anterior' => $estadoanterior 
			);
		}
		
		$this->db->where ( 'id', $id );
		$this->db->update ( 'articulo', $data );
	}
	public function banearmensaje($id, $accion) {
		$data = array (
				'denuncia' => $accion 
		);
		
		$this->db->where ( 'id', $id );
		$this->db->update ( 'mensaje', $data );
	}
	public function darXarticulo($id) {
		$this->db->select ( 'art.id, art.titulo, usu.nombre, usu.email, art.usuario' );
		$this->db->from ( 'articulo as art' );
		$this->db->join ( 'usuario as usu', 'usu.id = art.usuario', 'left' );
		$this->db->where ( 'art.id', $id );
		
		$res = $this->db->get ();
		
		if ($res) {
			$res = $res->result ();
			if ($res && is_array ( $res ) && count ( $res ) > 0) {
				return $res;
			}
		}
		return false;
	}
	public function darXreporte($id) {
		$this->db->select ( 'reporte.id, reporte.asunto,
						reporte.paquete, paq.articulos as articulopaq,
						ar.titulo as idtitulo, ar.id as idarticulo, ar.estado as estado_articulo,
						ar.estado_anterior as estadoarticuloanterior, u3.id as idusuarioarticulo, u3.email as seuusuarioarticulo,  u3.password as u3password,
						u3.seudonimo as seu3,
						u2.id as idusuario, u2.password as u2password,
						u2.email as seuusuario, u2.estado as estadou2,u2.seudonimo as seu2,
						reporte.descripcion, reporte.mensaje, mensj.denuncia ' );
		
		$this->db->from ( 'reporte' );
		$this->db->join ( 'usuario as u', 'reporte.usuario = u.id', 'left' );
		$this->db->join ( 'usuario as u2', 'reporte.perfil = u2.id', 'left' );
		$this->db->join ( 'usuario as marcador', 'reporte.marcado_por = marcador.id', 'left' );
		$this->db->join ( 'articulo as ar', 'reporte.articulo = ar.id', 'left' );
		$this->db->join ( 'usuario as u3', 'u3.id = ar.usuario', 'left' );
		$this->db->join ( 'paquete as paq', 'paq.id = reporte.paquete', 'left' );
		$this->db->join ( 'mensaje as mensj', 'mensj.id = reporte.mensaje', 'left' );
		
		$this->db->where ( 'reporte.id', $id );
		
		$res = $this->db->get ();
		
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