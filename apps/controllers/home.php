<?php
require_once 'basecontroller.php';
class Home extends BaseController {
	public function __construct() {
		parent::__construct ();
		$this->load->model ( "Categoria_model", "categoria" );
		$this->load->model ( "Articulo_model", "articulo" );
	}
	public function exportarDiccionario() {
		$res = $this->db->query ( "SELECT d.hash hash,de.traduccion esp,di.traduccion eng
			FROM (select hash from `diccionario` group by hash) d
			left join diccionario de on de.hash=d.hash and de.lenguaje=4
			left join diccionario di on di.hash=d.hash and di.lenguaje=2" )->result ();
		if (is_array ( $res ) && count ( $res ) > 0) {
			$this->load->library ( "PHPExcel" );
			$objPHPExcel = $this->phpexcel;
			
			$h = $objPHPExcel->setActiveSheetIndex ( 0 );
			$objPHPExcel->getActiveSheet ()->setTitle ( 'Traducciones' );
			$h->setCellValue ( "A1", "Hash" );
			$h->setCellValue ( "B1", "Español" );
			$h->setCellValue ( "C1", "Inglés" );
			foreach ( $res as $i => $diccionario ) {
				$h->setCellValue ( "A" . ($i + 2), $diccionario->hash );
				$h->setCellValue ( "B" . ($i + 2), $diccionario->esp );
				$h->setCellValue ( "C" . ($i + 2), $diccionario->eng );
			}
			$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel2007' );
			$base = "files/" . rand () . ".xlsx";
			$dir = BASEPATH . "../$base";
			$objWriter->save ( $dir );
			redirect ( $base, "refresh" );
			return;
		}
		redirect ( base_url (), "refresh" );
	}
	public function importarDiccionario() {
		$data = array ();
		$archivo = (isset ( $_FILES ) && isset ( $_FILES ["archivo"] )) ? $_FILES ["archivo"] : false;
		if ($archivo && is_file ( $archivo ["tmp_name"] )) {
			ini_set ( "max_execution_time", 3600 );
			ini_set ( "memory_limit", "256M" );
			require_once (BASEPATH . "../apps/libraries/PHPExcel/IOFactory.php");
			$objPHPExcel = PHPExcel_IOFactory::load ( $archivo ["tmp_name"] );
			$h = $objPHPExcel->getActiveSheet ();
			$r = 2;
			$cHash = 0;
			$cEsp = 1;
			$cEng = 2;
			$count = 0;
			$categorias = array ();
			do {
				$hash = $h->getCellByColumnAndRow ( $cHash, $r )->getValue ();
				if ($hash) {
					$esp = $h->getCellByColumnAndRow ( $cEsp, $r )->getValue ();
					$eng = $h->getCellByColumnAndRow ( $cEng, $r )->getValue ();
					$this->db->update ( "diccionario", array (
							"traduccion" => $esp,
							"traducido" => "Si" 
					), array (
							"hash" => $hash,
							"lenguaje" => 4 
					) );
					$this->db->update ( "diccionario", array (
							"traduccion" => $eng,
							"traducido" => "Si" 
					), array (
							"hash" => $hash,
							"lenguaje" => 2 
					) );
					$count ++;
				}
				$r ++;
			} while ( trim ( $hash ) !== "" );
			if ($count > 0) {
				$data ["Mensaje"] = "Se importaron $count Traducciones";
			} else {
				$data ["Error"] = "No se importo ninguna Traducción";
			}
		}
		$this->loadGUI ( "diccionario", $data );
	}
	public function sitemap() {
		$r = false;
		$url = array ();
		$c = 0;
		$limit = 40000;
		$cnames = 0;
		$anames = array ();
		do {
			unset ( $r );
			$r = $this->db->select ( "articulo.id,articulo.titulo,articulo.foto,articulo.usuario" )->where ( array (
					"terminado" => 0 
			) )->join ( "usuario", "usuario.id=articulo.usuario and usuario.estado not in('Inactivo','Baneado')", "inner" )->get ( "articulo", 1000, $c )->result ();
			if (($r && is_array ( $r ) && count ( $r ) > 0)) {
				foreach ( $r as $a ) {
					$arr = array ();
					$arr ["url"] = base_url () . "product/$a->id-" . normalizarTexto ( $a->titulo );
					
					$imgs = explode ( ",", $a->foto );
					if (count ( $imgs ) > 0) {
						$arr ["images"] = array ();
						foreach ( $imgs as $img ) 

						{
							$arr ["images"] [] = base_url () . "files/$a->usuario/$img";
						}
					}
					
					$url [] = $arr;
					if (count ( $url ) == $limit) {
						$cnames ++;
						$anames [] = $this->createSitemap ( $url, $cnames );
						unset ( $url );
						$url = array ();
					}
				}
			}
			$c += 1000;
		} while ( $r && is_array ( $r ) && count ( $r ) > 0 );
		
		$c = 0;
		do {
			unset ( $r );
			$r = $this->db->select ( "seudonimo,imagen,id" )->where_not_in ( "estado ", array (
					'Baneado',
					'Inactivo' 
			) )->get ( "usuario", 1000, $c )->result ();
			if (($r && is_array ( $r ) && count ( $r ) > 0)) {
				foreach ( $r as $u ) {
					$arr = array ();
					$arr ["url"] = base_url () . "store/" . normalizarTexto ( $u->seudonimo );
					
					if ($u->imagen) {
						$arr ["images"] = array (
								base_url () . "files/$u->id/$u->imagen" 
						);
					}
					
					$url [] = $arr;
					if (count ( $url ) == $limit) {
						$cnames ++;
						$anames [] = $this->createSitemap ( $url, $cnames );
						unset ( $url );
						$url = array ();
					}
				}
			}
			$c += 1000;
		} while ( $r && is_array ( $r ) && count ( $r ) > 0 );
		if (count ( $url ) > 0) {
			$cnames ++;
			$anames [] = $this->createSitemap ( $url, $cnames );
			unset ( $url );
			$url = array ();
		}
		$xml = $this->load->view ( "sitemap_index", array (
				"sitemaps" => $anames 
		), true );
		file_put_contents ( BASEPATH . "../sitemap_index.xml", $xml );
		$xmlgz = gzencode ( $xml, 9 );
		unset ( $xml );
		file_put_contents ( BASEPATH . "../sitemap_index.xml.gz", $xmlgz );
	}
	public function createSitemap($urls, $cname) {
		$xml = $this->load->view ( "sitemap", array (
				"urls" => $urls 
		), true );
		
		if ($cname > 1) {
			$cname = ".$cname";
		} else {
			$cname = "";
		}
		$fname = "sitemap$cname.xml";
		file_put_contents ( BASEPATH . "../$fname", $xml );
		$xmlgz = gzencode ( $xml, 9 );
		unset ( $xml );
		file_put_contents ( BASEPATH . "../$fname.gz", $xmlgz );
		return $fname;
	}
	public function contarCategorias() {
		$this->db->update ( "categoria", array (
				"cantidad" => 0 
		) );
		$r = $this->db->query ( "SELECT articulo.categoria,count(articulo.categoria) as cantidad
				FROM `articulo`
				inner join usuario on articulo.usuario=usuario.id and usuario.estado<>'Baneado'
				WHERE articulo.terminado=0 group by articulo.categoria" )->result ();
		if ($r && is_array ( $r ) && count ( $r ) > 0) {
			foreach ( $r as $c ) {
				$this->db->update ( "categoria", array (
						"cantidad" => $c->cantidad 
				), array (
						"id" => $c->categoria 
				) );
			}
			$r = $this->db->query ( "SELECT sum(cantidad) as cantidad,padre FROM `categoria` where nivel=3 and cantidad >0 group by padre" )->result ();
			if ($r && is_array ( $r ) && count ( $r ) > 0) {
				foreach ( $r as $c ) {
					$this->db->query ( "update categoria set cantidad=cantidad+$c->cantidad where id='$c->padre'" );
				}
			}
			$r = $this->db->query ( "SELECT sum(cantidad) as cantidad,padre FROM `categoria` where nivel=2 and cantidad >0 group by padre" )->result ();
			if ($r && is_array ( $r ) && count ( $r ) > 0) {
				foreach ( $r as $c ) {
					$this->db->query ( "update categoria set cantidad=cantidad+$c->cantidad where id='$c->padre'" );
				}
			}
		}
	}
	public function importarExcel() {
		$data = array ();
		$this->db->where ( array (
				"estado <>" => "Baneado" 
		) );
		$this->db->where ( array (
				"estado <>" => "Inactivo" 
		) );
		$data ["usuarios"] = $this->db->order_by ( "seudonimo" )->get ( "usuario" )->result ();
		$usuario = $this->input->post ( "usuario" );
		$archivo = (isset ( $_FILES ) && isset ( $_FILES ["archivo"] )) ? $_FILES ["archivo"] : false;
		if ($usuario && $archivo && is_file ( $archivo ["tmp_name"] )) {
			ini_set ( "max_execution_time", 3600 );
			ini_set ( "memory_limit", "256M" );
			$u = $this->usuario->darUsuarioXId ( $usuario );
			require_once (BASEPATH . "../apps/libraries/PHPExcel/IOFactory.php");
			$objPHPExcel = PHPExcel_IOFactory::load ( $archivo ["tmp_name"] );
			$h = $objPHPExcel->getActiveSheet ();
			$r = 2;
			$c = 0;
			$count = 0;
			$categorias = array ();
			do {
				$celda = $h->getCellByColumnAndRow ( $c, $r )->getValue ();
				if (trim ( $celda ) !== "") {
					$categoria = $h->getCellByColumnAndRow ( $c + 2, $r )->getValue ();
					if (! isset ( $categorias [$categoria] )) {
						$this->db->where ( array (
								"id" => $categoria 
						) );
						$re = $this->db->get ( "categoria" )->result ();
						if (! ($re && is_array ( $re ) && count ( $re ) > 0)) {
							unset ( $re );
							$r ++;
							continue;
						}
						unset ( $re );
						$categorias [$categoria] = true;
					}
					$articulo = new stdClass ();
					$articulo->categoria = $categoria;
					$tipo = strtolower ( $celda );
					switch ($tipo) {
						case "Precio Fijo" :
							$articulo->tipo = "Cantidad";
							break;
						case "Subasta" :
							$articulo->tipo = "Cantidad";
							break;
						default :
							$articulo->tipo = "Cantidad";
							break;
					}
					$articulo->titulo = $h->getCellByColumnAndRow ( $c + 1, $r )->getValue ();
					$articulo->cantidad = intval ( $h->getCellByColumnAndRow ( $c + 3, $r )->getValue () );
					$articulo->cantidad_original = $articulo->cantidad;
					if ($articulo->cantidad <= 0) {
						$articulo->cantidad = 0;
						$articulo->cantidad_original = 0;
						$articulo->terminado = 1;
						$articulo->fecha_terminado = date ( "Y-m-d H:i:s" );
					} else {
						$articulo->terminado = 0;
					}
					$moneda = $h->getCellByColumnAndRow ( $c + 4, $r )->getValue ();
					switch ($moneda) {
						default :
							$articulo->moneda = "1";
							break;
					}
					$articulo->precio = $h->getCellByColumnAndRow ( $c + 5, $r )->getValue ();
					$articulo->descripcion = urldecode ( $h->getCellByColumnAndRow ( $c + 6, $r )->getValue () );
					$tipo_pago = $h->getCellByColumnAndRow ( $c + 8, $r )->getValue ();
					$tipo_pago = str_replace ( ".", ",", $tipo_pago );
					$tipo_pago = explode ( ",", $tipo_pago );
					$tp = array ();
					foreach ( $tipo_pago as $i => $t ) {
						$v = intval ( trim ( $t ) );
						if ($v > 0 && $v < 5) {
							$tp [] = $v;
						}
					}
					sort ( $tp );
					$tipo_pago = implode ( ",", $tp );
					if (trim ( $tipo_pago ) == "") {
						$r ++;
						continue;
					}
					$articulo->pagos = $tipo_pago;
					
					if ($h->getCellByColumnAndRow ( $c + 7, $r )->getValue () != '') {
						$img = str_replace ( " ", "%20", $h->getCellByColumnAndRow ( $c + 7, $r )->getValue () );
						$ext = pathinfo ( $img, PATHINFO_EXTENSION );
						$basename = pathinfo ( $img, PATHINFO_BASENAME );
						$name = pathinfo ( $img, PATHINFO_FILENAME );
						$cont = file_get_contents ( $img );
						$base = "files/" . rand () . "." . $ext;
						$dir = BASEPATH . "../" . $base;
						file_put_contents ( $dir, $cont );
						$type = get_mime ( $dir );
						$_FILES ["imagen"] = array (
								"name" => $basename,
								"tmp_name" => $dir,
								"type" => $type,
								"error" => 0,
								"size" => filesize ( $dir ) 
						);
						$rx = (uploadImage ( false, $u ));
						@unlink ( $dir );
						if (isset ( $rx ["error"] )) {
							$r ++;
							continue;
						}
					} else {
						$ban = true;
					}
					
					$articulo->envio_local = trim ( $h->getCellByColumnAndRow ( $c + 9, $r )->getValue () );
					if (! $articulo->envio_local) {
						$articulo->envio_local = null;
					}
					$articulo->gastos_pais = trim ( $h->getCellByColumnAndRow ( $c + 10, $r )->getValue () );
					if ($articulo->gastos_pais === "") {
						$articulo->gastos_pais = null;
					} else {
						$articulo->gastos_pais = floatval ( $articulo->gastos_pais );
					}
					$articulo->gastos_continente = trim ( $h->getCellByColumnAndRow ( $c + 11, $r )->getValue () );
					if ($articulo->gastos_continente === "") {
						$articulo->gastos_continente = null;
					} else {
						$articulo->gastos_continente = floatval ( $articulo->gastos_continente );
					}
					$articulo->gastos_todos = trim ( $h->getCellByColumnAndRow ( $c + 12, $r )->getValue () );
					if ($articulo->gastos_todos === "") {
						$articulo->gastos_todos = null;
					} else {
						$articulo->gastos_todos = floatval ( $articulo->gastos_todos );
					}
					if ($ban != true) {
						$articulo->foto = $rx ["name"] . "." . $rx ["ext"];
					} else {
						$articulo->foto = "no-imagen.thumb.jpg";
						$ban = false;
					}
					$articulo->usuario = $u->id;
					$articulo->fecha_registro = date ( "Y-m-d H:i:s" );
					
					if ($this->db->insert ( "articulo", $articulo ) && $articulo->terminado != 1) {
						$this->articulo->adicionarCantidad ( $categoria );
					}
					$count ++;
				}
				$r ++;
			} while ( trim ( $celda ) !== "" );
			if ($count > 0) {
				$data ["Mensaje"] = "Se importaron $count Artículos";
			} else {
				$data ["Error"] = "No se importo ningun Artículo";
			}
		}
		$this->loadGUI ( "importar", $data );
	}
	public function cancelarEnviosPendientes() {
		$exito = false;
		$id = $this->input->post ( "id" );
		if ($id && intval ( $id ) > 0) {
			$exito = $this->db->delete ( "envio_correo", array (
					"correo_masivo" => $id,
					"estado" => "Pendiente" 
			) );
		}
		$this->output->set_output ( json_encode ( array (
				"exito" => $exito 
		) ) );
	}
	public function enviarMails() {
		$file = BASEPATH . "../envioMail.log";
		print $file;
		$contenido = "";
		if (is_file ( $file )) {
			$contenido = file_get_contents ( $file );
		}
		$contenido = trim ( $contenido );
		if ($contenido != "") {
			$lineas = explode ( "\n", $contenido );
		} else {
			$lineas = array ();
		}
		array_unshift ( $lineas, "[" . date ( "Y-m-d H:i:s" ) . "] Running" );
		$c = $this->configuracion->variables ( "cantidadEmails" );
		$r = $this->db->select ( "envio_correo.id,correo_masivo.asunto,correo_masivo.mensaje,envio_correo.destinatario,envio_correo.nombre" )->where ( array (
				"envio_correo.estado" => "Pendiente" 
		) )->join ( "correo_masivo", "correo_masivo.id=envio_correo.correo_masivo", "inner" )->get ( "envio_correo", $c, 0 )->result ();
		if ($r && is_array ( $r ) && count ( $r ) > 0) {
			$this->load->library ( "myemail" );
			foreach ( $r as $e ) {
				array_unshift ( $lineas, "[" . date ( "Y-m-d H:i:s" ) . "] Proceso id: $e->id" );
				if (trim ( $e->destinatario ) != "") {
					if ($this->myemail->enviarTemplate ( trim ( $e->destinatario ), str_ireplace ( "%nombre%", $e->nombre, $e->asunto ), "mail/mail-base", array (
							"mensaje" => str_ireplace ( "%nombre%", $e->nombre, $e->mensaje ) 
					) )) {
						$this->db->update ( "envio_correo", array (
								"estado" => "Enviado",
								"fecha" => date ( "Y-m-d H:i:s" ) 
						), array (
								"id" => $e->id 
						) );
						array_unshift ( $lineas, "[" . date ( "Y-m-d H:i:s" ) . "] Enviado a $e->destinatario." );
						sleep ( 1 ); // espera para no saturar el server
					}
				}
			}
			if (count ( $lineas ) > 1000) {
				array_splice ( $lineas, 1001 );
			}
		}
		$contenido = implode ( "\n", $lineas );
		file_put_contents ( $file, $contenido );
	}
	public function newsletter() {
		if ($this->myuser) {
			if ($this->myuser->tipo == "Administrador") {
				$data = $this->procesarNewsletter ();
				$data = array_merge ( $data, $this->obtenerEnvios () );
				$data ["vista"] = "newsletter";
				$this->loadGUI ( "administrador/newsletter", $data );
			} else {
				redirect ( "store/{$this->myuser->seudonimo}", "refresh" );
			}
		} else {
			redirect ( "login", "refresh" );
		}
	}
	public function obtenerEnvios() {
		return array (
				"envios" => $this->db->select ( "id,asunto,mensaje,fecha,obtenerPorcentajeEnvio(id) as porcentaje" )->order_by ( "fecha desc" )->get ( "correo_masivo" )->result () 
		);
	}
	private function procesarNewsletter() {
		$retorno = array (
				"errores" => array () 
		);
		if (isset ( $_POST ["asunto"] )) {
			$asunto = $this->input->post ( "asunto" );
			$mensaje = $this->input->post ( "mensaje" );
			$destino = $this->input->post ( "destino" );
			
			if ($asunto && $mensaje && $destino) {
				$mensaje = base64_decode ( $mensaje );
				$emails = array ();
				switch ($destino) {
					case "1" :
						$r = $this->db->select ( "email,seudonimo" )->where ( array (
								"notificaciones" => "1" 
						) )->get ( "usuario" )->result ();
						if ($r && is_array ( $r ) && count ( $r ) > 0) {
							foreach ( $r as $u ) {
								$emails [$u->email] = $u->seudonimo;
							}
							unset ( $r );
						}
						break;
					case "2" :
						if (isset ( $_FILES ) && isset ( $_FILES ["excel"] ))
							$emails = $this->importarEmails ( $_FILES ["excel"] );
						break;
				}
				/*
				 * $emails = array ( "goaamb@gmail.com" => "Alvaro Justo Michel
				 * Barrera" );
				 */
				if (count ( $emails ) > 0) {
					if ($this->db->insert ( "correo_masivo", array (
							"asunto" => $asunto,
							"mensaje" => $mensaje,
							"fecha" => date ( "Y-m-d H:i:s" ) 
					) )) {
						$eid = $this->db->insert_id ();
						$retorno ["exito"] = 1;
						foreach ( $emails as $e => $n ) {
							if (trim ( $e ) !== "") {
								$this->db->insert ( "envio_correo", array (
										"correo_masivo" => $eid,
										"destinatario" => $e,
										"nombre" => $n 
								) );
							}
						}
					}
				} else {
					$retorno ["errores"] [] = "general-No-Email";
				}
			} else {
				if (! $asunto)
					$retorno ["errores"] [] = "asunto";
				if (! $mensaje)
					$retorno ["errores"] [] = "mensaje";
				if (! $destino)
					$retorno ["errores"] [] = "destino";
			}
		}
		return $retorno;
	}
	private function importarEmails($files) {
		$emails = array ();
		if (isset ( $files ["tmp_name"] ) && is_file ( $files ["tmp_name"] )) {
			ini_set ( "max_execution_time", 3600 );
			ini_set ( "memory_limit", "256M" );
			require_once (BASEPATH . "../apps/libraries/PHPExcel/IOFactory.php");
			$objPHPExcel = PHPExcel_IOFactory::load ( $files ["tmp_name"] );
			$h = $objPHPExcel->getActiveSheet ();
			$r = 2;
			$c = 0;
			$count = 0;
			$categorias = array ();
			do {
				$nombre = $h->getCellByColumnAndRow ( $c, $r )->getValue ();
				$email = $h->getCellByColumnAndRow ( $c + 1, $r )->getValue ();
				if (trim ( $email ) !== "") {
					$p = "/[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/";
					if (preg_match ( $p, $email )) {
						$emails [$email] = $nombre;
						$count ++;
					}
				}
				$r ++;
			} while ( trim ( $email ) !== "" );
		}
		return $emails;
	}
	public function importarCategorias() {
		$data = array ();
		$archivo = (isset ( $_FILES ) && isset ( $_FILES ["archivo"] )) ? $_FILES ["archivo"] : false;
		if ($archivo && is_file ( $archivo ["tmp_name"] )) {
			require_once (BASEPATH . "../apps/libraries/PHPExcel/IOFactory.php");
			$objPHPExcel = PHPExcel_IOFactory::load ( $archivo ["tmp_name"] );
			$h = $objPHPExcel->getActiveSheet ();
			$r = 2;
			$c = 0;
			$count = 0;
			do {
				$celda = $h->getCellByColumnAndRow ( $c, $r )->getValue ();
				if (trim ( $celda ) !== "") {
					$padre = intval ( $celda );
					if ($padre !== 0) {
						$this->db->where ( array (
								"id" => $padre 
						) );
						$rc = $this->db->get ( "categoria" )->result ();
						if (! ($rc && is_array ( $rc ) && count ( $rc ) > 0)) {
							continue;
						}
					} else {
						$obj = new stdClass ();
						$obj->id = 0;
						$obj->nivel = 0;
						$obj->activo = 1;
						$rc = array (
								$obj 
						);
					}
					$espanol = $h->getCellByColumnAndRow ( $c + 1, $r )->getValue ();
					$ingles = $h->getCellByColumnAndRow ( $c + 2, $r )->getValue ();
					$categoria = new stdClass ();
					$categoria->padre = $rc [0]->id;
					$categoria->nivel = $rc [0]->nivel + 1;
					$categoria->activo = $rc [0]->activo;
					$categoria->cantidad = 0;
					if ($this->db->insert ( "categoria", $categoria )) {
						$cid = $this->db->insert_id ();
						$this->db->insert ( "nombrecategoria", array (
								"categoria" => $cid,
								"lenguaje" => 4,
								"nombre" => $espanol,
								"url_amigable" => normalizarTexto ( $espanol ) 
						) );
						$this->db->insert ( "nombrecategoria", array (
								"categoria" => $cid,
								"lenguaje" => 2,
								"nombre" => $ingles,
								"url_amigable" => normalizarTexto ( $ingles ) 
						) );
					}
					
					$count ++;
				}
				$r ++;
			} while ( trim ( $celda ) !== "" );
			if ($count > 0) {
				$data ["Mensaje"] = "Se importaron $count Categorías";
			} else {
				$data ["Error"] = "No se importo ninguna Categoría";
			}
		}
		$this->loadGUI ( "importar_categorias", $data );
	}
	private function obtenerCategorias($padre) {
		$this->db->select ( "categoria.id as id,nombrecategoria.nombre as nombre" );
		$this->db->where ( array (
				"padre" => $padre,
				"activo" => 1 
		) );
		$this->db->order_by ( "nombre asc" );
		$retorno = array ();
		$this->db->join ( "nombrecategoria", "nombrecategoria.categoria=categoria.id and nombrecategoria.lenguaje=4" );
		$r = $this->db->get ( "categoria" )->result ();
		if ($r && is_array ( $r ) && count ( $r ) > 0) {
			foreach ( $r as $c ) {
				$retorno [$c->id] = array (
						"nombre" => $c->nombre,
						"hijos" => $this->obtenerCategorias ( $c->id ) 
				);
			}
		}
		return $retorno;
	}
	public function categorias() {
		$c = $this->obtenerCategorias ( 0 );
		$this->load->library ( "PHPExcel" );
		$objPHPExcel = $this->phpexcel;
		
		$h = $objPHPExcel->setActiveSheetIndex ( 0 );
		$objPHPExcel->getActiveSheet ()->setTitle ( 'Categorias' );
		$v = 0;
		$cc = "A";
		$this->imprimirCategorias ( $c, $h, $v, $cc );
		
		$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel2007' );
		$base = "files/" . rand () . ".xlsx";
		$dir = BASEPATH . "../$base";
		$objWriter->save ( $dir );
		redirect ( $base, "refresh" );
	}
	private function imprimirCategorias($ca, $h, &$v, $cc) {
		foreach ( $ca as $id => $c ) {
			$v ++;
			if (count ( $c ["hijos"] ) > 0) {
				$h->setCellValue ( $cc . $v, $c ["nombre"] . "(" . $id . ")" );
				$h->mergeCells ( "$cc$v:" . chr ( ord ( $cc ) + 1 ) . "$v" )->getStyle ( "$cc$v:" . chr ( ord ( $cc ) + 1 ) . "$v" )->applyFromArray ( array (
						'font' => array (
								'bold' => true 
						),
						'alignment' => array (
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT 
						) 
				) );
				$this->imprimirCategorias ( $c ["hijos"], $h, $v, chr ( ord ( $cc ) + 1 ) );
			} else {
				$h->setCellValue ( $cc . $v, $c ["nombre"] );
				$h->setCellValue ( chr ( ord ( $cc ) + 1 ) . "$v", $id );
			}
		}
	}
	public function denunciar() {
		$exito = false;
		if ($this->myuser) {
			$articulo = $this->input->post ( "articulo" );
			$perfil = $this->input->post ( "usuario" );
			$asunto = $this->input->post ( "motivo" );
			$descripcion = $this->input->post ( "descripcion" );
			
			if ($asunto) {
				$exito = $this->db->insert ( "reporte", array (
						"asunto" => $asunto,
						"usuario" => $this->myuser->id,
						"perfil" => $perfil ? $perfil : null,
						"descripcion" => $descripcion,
						"fecha" => date ( "Y-m-d H:i:s" ),
						"articulo" => $articulo ? $articulo : null,
						"estado" => "Procesando" 
				) );
			}
		}
		$this->output->set_output ( json_encode ( array (
				"exito" => $exito 
		) ) );
	}
	public function integrarNombre() {
		$this->db->select ( "id,nombre,apellido" );
		$r = $this->db->get ( "usuario" )->result ();
		if ($r && is_array ( $r ) && count ( $r ) > 0) {
			foreach ( $r as $u ) {
				$this->db->update ( "usuario", array (
						"nombre" => $u->nombre . " " . $u->apellido,
						"apellido" => "" 
				), array (
						"id" => $u->id 
				) );
			}
		}
	}
	public function crearFacturas() {
		$anio = date ( "Y" );
		$mes = intval ( date ( "m" ) ) - 1;
		if ($mes <= 0) {
			$mes = 12 - $mes;
			$anio --;
		}
		
		if ($mes < 10) {
			$mes = "0" . $mes;
		}
		
		$this->load->model ( 'administrador_model', 'administrador' );
		$this->load->library ( "Myemail" );
		
		$us = $this->usuario->darUsuarios ();
		if ($us && is_array ( $us ) && count ( $us ) > 0) {
			foreach ( $us as $u ) {
				$this->crearCuentas ( $mes, $anio, $u->id );
				// var_dump($u);
				$cantfacturas = $this->administrador->contarfacturaXestadoXusuario ( $u->id, "Pendiente" );
				
				if ($cantfacturas >= 3) {
					// echo "<a href='".base_url()."terms"."'>terminos y
					// condiciones</a>";
					
					$this->administrador->banearcampos ( $u->id, "Baneado", "usuario" );
					
					$this->myemail->enviarTemplate ( $u->email, "Tu cuenta ha sido restringida", "mail/incumplimiento-pagos", array (
							"emailfrom" => $this->configuracion->variables ( "emailFrom" ) 
					) );
					
					print "Cantidad Facturas: " . $cantfacturas . " Usuario: " . $u->id . "<br/>";
				}
				$this->cambiarTipoTarifa ( $u );
			}
		}
	}
	private function cambiarTipoTarifa($usuario) {
		if ($usuario && $usuario->nueva_tarifa) {
			$this->db->update ( "usuario", array (
					"nueva_tarifa" => null,
					"tipo_tarifa" => $usuario->nueva_tarifa 
			), array (
					"id" => $usuario->id 
			) );
		}
	}
	private function crearCuentas($mes, $anio, $usuario) {
		$this->load->model ( "Usuario_model", "usuario" );
		$usuario = $this->usuario->darUsuarioXId ( $usuario );
		if ($usuario) {
			$x = $this->articulo->darDatosCuentas ( $mes, $anio, $usuario );
			if ($x) {
				
				$this->db->insert ( "factura", array (
						"codigo" => $x->codigo,
						"mes" => $x->mes,
						"usuario" => $x->usuario,
						"fecha" => $x->fecha,
						"articulos" => $x->articulos,
						"monto_venta" => $x->monto_venta,
						"monto_stock" => $x->monto_stock,
						"monto_tarifa" => $x->monto_tarifa,
						"iva" => $x->iva,
						"tipo_tarifa" => $usuario->tipo_tarifa 
				) );
				
				print "se creo con exito la factura del $mes-$anio y usuario: $usuario->id-$usuario->seudonimo<br/>";
			} else {
				print "No se creo la factura posiblemente no tenga datos o bien ya fue creada<br/>";
			}
		} else {
			print "No existe el usurio<br/>";
		}
	}
	public function enviarMail() {
		$asunto = trim ( $this->input->post ( "asunto" ) );
		$mensaje = trim ( $this->input->post ( "mensaje" ) );
		$nombre = trim ( $this->input->post ( "nombre" ) );
		$email = trim ( $this->input->post ( "email" ) );
		if ($asunto !== "" && $mensaje !== "" && $nombre !== "" && $email !== "") {
			$emailto = "info@lovende.com";
			// $emailto = "goaamb@gmail.com";
			$this->load->library ( "myemail" );
			var_dump ( $this->myemail->enviarTemplate ( $emailto, $asunto, "mail/enviar-mail", array (
					"asunto" => $asunto,
					"nombre" => $nombre,
					"mensaje" => nl2br ( $mensaje ),
					"email" => $email 
			) ) );
		}
	}
	public function estatica($view) {
		$this->loadGUI ( $view );
	}
	public function cambiarCarpetas() {
		$res = $this->db->get ( "usuario" );
		if ($res) {
			$res = $res->result ();
			if ($res) {
				foreach ( $res as $usuario ) {
					$dir = BASEPATH . "../files/$usuario->seudonimo";
					print $dir . " -> ";
					if (is_dir ( $dir )) {
						$dird = BASEPATH . "../files/$usuario->id";
						print $dird;
						rename ( $dir, $dird );
					}
					print "<br/>";
				}
			}
		}
	}
	public function modal($modal, $tipo = false, $id = false, $extra = false, $pagos = false) {
		$data = array ();
		if ($tipo == "mail") {
			$asuntos = array (
					1 => traducir ( "Consulta de tarifas" ),
					2 => traducir ( "Consulta de privacidad" ),
					3 => traducir ( "Consulta de cuenta suspendida" ) 
			);
			$id = array_search ( $id, array_keys ( $asuntos ) ) !== false ? $id : 1;
			$data ["asunto"] = $asuntos [$id];
		} else {
			if ($this->isLogged ()) {
				switch ($tipo) {
					case "mensaje" :
						$data ["receptor"] = $this->usuario->darUsuarioXId ( $id );
						break;
					case "articulo" :
						$data ["articulo"] = $this->articulo->darArticulo ( $id );
						break;
					case "usuario" :
						$data ["usuario"] = $this->usuario->darUsuarioXId ( $id );
						break;
					case "votos" :
						$data ["usuario"] = $this->usuario->darUsuarioXId ( $id );
						$data ["mes1"] = $this->usuario->darVotos ( $id, 1 );
						$data ["mes6"] = $this->usuario->darVotos ( $id, 6 );
						$data ["mes12"] = $this->usuario->darVotos ( $id, 12 );
						$data ["todos"] = $this->usuario->darVotos ( $id );
						break;
					case "myuser" :
						$data ["usuario"] = $this->usuario->darUsuarioXId ( $this->myuser->id );
						break;
					case "articulosComprados" :
						$this->load->model ( "Paypal_model", "paypal" );
						$data ["comprador"] = $this->usuario->darUsuarioXId ( $id );
						if ($data ["comprador"]) {
							$data ["comprador"]->pais = $data ["comprador"]->darPais ();
							$data ["comprador"]->ciudad = $data ["comprador"]->darCiudad ();
							$data ["articulos"] = $this->articulo->listarArticulosPorComprar ( $data ["comprador"]->id, $this->myuser->id, $extra, $pagos );
							$data ["paquete"] = $this->articulo->darPaquete ( $extra );
							if ($data ["paquete"]) {
								$data ["vendedor"] = $this->usuario->darUsuarioXId ( $data ["paquete"]->vendedor );
							}
						}
						break;
					case "articulosVendedor" :
						$this->load->model ( "Paypal_model", "paypal" );
						$data ["vendedor"] = $this->usuario->darUsuarioXId ( $id );
						if ($data ["vendedor"]) {
							$data ["vendedor"]->pais = $data ["vendedor"]->darPais ();
							$data ["vendedor"]->ciudad = $data ["vendedor"]->darCiudad ();
							$data ["articulos"] = $this->articulo->listarArticulosPorComprar ( $this->myuser->id, $data ["vendedor"]->id, $extra, $pagos );
							$data ["paquete"] = $this->articulo->darPaquete ( $extra );
							if ($data ["paquete"]) {
								$data ["comprador"] = $this->usuario->darUsuarioXId ( $data ["paquete"]->comprador );
							}
						}
						break;
					case "paquete" :
						$this->load->model ( "Paypal_model", "paypal" );
						$data ["comprador"] = $this->usuario->darUsuarioXId ( $id );
						if ($data ["comprador"]) {
							$data ["comprador"]->pais = $data ["comprador"]->darPais ();
							$data ["comprador"]->ciudad = $data ["comprador"]->darCiudad ();
						}
						$data ["disputa"] = $id;
						$data ["paquete"] = $this->articulo->darPaquete ( $extra );
						if ($data ["paquete"]) {
							$data ["vendedor"] = $this->usuario->darUsuarioXId ( $data ["paquete"]->vendedor );
						}
						break;
					case "comprador" :
						$data ["comprador"] = $this->usuario->darUsuarioXId ( $id );
						if ($data ["comprador"]) {
							$data ["comprador"]->pais = $data ["comprador"]->darPais ();
							$data ["comprador"]->ciudad = $data ["comprador"]->darCiudad ();
						}
						break;
					case "vendedor" :
						$data ["vendedor"] = $this->usuario->darUsuarioXId ( $id );
						if ($data ["vendedor"]) {
							$data ["vendedor"]->pais = $data ["vendedor"]->darPais ();
							$data ["vendedor"]->ciudad = $data ["vendedor"]->darCiudad ();
						}
						break;
					case "facturaDetalle" :
						$mes = date ( "m" );
						$anio = date ( "Y" );
						$bmes = "$mes-$anio";
						$data ["factura"] = false;
						$usuario = $this->myuser;
						if ($id == "x") {
							$usuario = $this->usuario->darUsuarioXId ( $extra );
							if (! $usuario) {
								$usuario = $this->myuser;
							}
							$data ["factura"] = ($this->articulo->darDatosCuentas ( $mes, $anio, $usuario ));
						} else {
							$data ["factura"] = $this->articulo->darFactura ( $id );
						}
						if ($data ["factura"]) {
							$data ["usuario"] = $this->usuario->darUsuarioXId ( $data ["factura"]->usuario );
							if ($data ["usuario"]) {
								if ($this->myuser->tipo == "Administrador" || $this->myuser->id == $data ["usuario"]->id) {
									$data ["usuario"]->pais = $this->usuario->darPais ( $data ["usuario"]->pais );
									if ($data ["usuario"]->tipo_tarifa == "Comision") {
										$data ["cuentas"] = $this->articulo->darCuentasPorArticulos ( $data ["factura"]->articulos );
									} else {
										$data ["cuentas"] = $this->articulo->darCuentasFakePorArticulos ( $data ["factura"]->articulos, $data ["usuario"] );
									}
								} else {
									// $modal = "redirect-login";
								}
							}
						}
						break;
					case "denunciamensaje" :
						if ($this->myuser) {
							$data ['reportador'] = $this->usuario->darUsuarioXId ( $pagos );
							$data ['idmensaje'] = $extra;
							$data ['reportado'] = $this->usuario->darUsuarioXId ( $id );
							break;
						} else {
							redirect ( "login", "refresh" );
							return;
						}
				}
			} else {
				$modal = "redirect-login";
			}
		}
		parent::modal ( $modal, $data );
	}
	public function leerDirectorio($path) {
		$directorio = dir ( $path );
		$files = array ();
		while ( $archivo = $directorio->read () ) {
			if ($archivo != "." && $archivo != "..") {
				$f = $path . $archivo;
				if (is_file ( $f )) {
					$files [] = $f;
				} else {
					$d = $f . "/";
					$files = array_merge ( $files, $this->leerDirectorio ( $d ) );
				}
			}
		}
		$directorio->close ();
		return $files;
	}
	public function removerImagenesInnescesarias() {
		ini_set ( "max_execution_time", 3600 );
		$this->db->select ( "imagen,id" );
		$res = $this->db->get ( "usuario" );
		$res = $res->result ();
		$imagenes = array ();
		
		foreach ( $res as $r ) {
			if (! isset ( $imagenes [$r->id] )) {
				$imagenes [$r->id] = array ();
			}
			$imagenes [$r->id] [] = pathinfo ( $r->imagen, PATHINFO_FILENAME );
		}
		$this->db->select ( "foto,usuario.id as id" );
		$this->db->join ( "usuario", "usuario.id=articulo.usuario" );
		$res = $this->db->get ( "articulo" );
		$res = $res->result ();
		foreach ( $res as $r ) {
			if (! isset ( $imagenes [$r->id] )) {
				$imagenes [$r->id] = array ();
			}
			$f = explode ( ",", $r->foto );
			foreach ( $f as $i ) {
				$imagenes [$r->id] [] = pathinfo ( $i, PATHINFO_FILENAME );
			}
		}
		
		$base = "files/";
		$dir = BASEPATH . "../$base";
		$fs = $this->leerDirectorio ( $dir );
		
		$e = false;
		foreach ( $fs as $f ) {
			foreach ( $imagenes as $id => $i ) {
				foreach ( $i as $img ) {
					if (trim ( $img ) !== "") {
						$fx = $id . "/" . $img . ".";
						if (strstr ( $f, $fx ) !== false) {
							$e = true;
							break (2);
						}
					}
				}
			}
			if (! $e) {
				print "D - $f<br/>";
				@unlink ( $f );
			}
		}
	}
	public function verMas() {
		$criterio = $this->input->post ( "criterio" );
		$pagina = $this->input->post ( "pagina" );
		$section = $this->input->post ( "section" );
		$orden = $this->input->post ( "orden" );
		$ubicacion = $this->input->post ( "ubicacion" );
		$categoria = $this->input->post ( "categoria" );
		$usuario = $this->input->post ( "usuario" );
		$data ["total"] = 0;
		$data ["final"] = 0;
		$data = array_merge ( $this->articulo->leerArticulos ( $pagina, $criterio, $section, $orden, $ubicacion, $categoria, $this->idioma->language->id, $usuario ), array (
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
						$x ["textoOferta"] = "<span class='italic'>" . traducir ( "¡Cómpralo" ) . "</span> <span class='red italic'>" . traducir ( "ya!" ) . "</span><br/><span class='oferta'>" . traducir ( "o Mejor oferta" ) . "</span>";
					} elseif ($x ["tipo"] == "Cantidad") {
						$x ["cC"] = $articulo->cantidad;
						$x ["textoOferta"] = "<span class='italic'>" . traducir ( "¡Cómpralo" ) . "</span> <span class='red italic'>" . traducir ( "ya!" ) . "</span>";
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
	public function index($noExiste = false) {
		parent::index ( true );
		$header = array ();
		$header ["extraMeta"] = "<meta property='og:title' content='Compra y vende en Lovende.'/>";
		$header ["extraMeta"] .= "<meta property='og:description' content='Lovende, web online de compra-venta en formato subasta y precio fijo.'/>";
		$header ["extraMeta"] .= "<meta property='og:image' content='" . base_url () . "assets/images/html/logofb.jpg' />";
		$header ["headerTitle"] = $this->configuracion->variables ( "defaultHeaderTitle" );
		$header ["headerDescripcion"] = $this->configuracion->variables ( "defaultHeaderDescription" );
		$header ["headerKeywords"] = $this->configuracion->variables ( "defaultHeaderKeywords" );
		if ($noExiste !== "404") {
			$uri = explode ( "/", uri_string () );
			$section = array_shift ( $uri );
			$action = array_shift ( $uri );
			$pagina = intval ( $this->input->get ( "pagina" ) );
			$pagina = $pagina >= 1 ? $pagina : 1;
			$data = array_merge ( $this->articulo->leerArticulos ( $pagina, $this->input->get ( "criterio" ), $action, $this->input->get ( "orden" ), $this->input->get ( "ubicacion" ), $this->input->get ( "categoria" ), $this->idioma->language->id ), array (
					"section" => $action,
					"orden" => $this->input->get ( "orden" ),
					"ubicacion" => $this->input->get ( "ubicacion" ),
					"categoria" => $this->input->get ( "categoria" ) 
			) );
			$this->loadGUI ( "home", $data, $header );
		} else {
			$cats = $this->categoria->darCategoriasXNivel ( 1 );
			$retcat = $this->parseCategories ( $cats );
			$this->loadGUI ( "no-existe", array (
					"categorias" => $retcat 
			), $header );
		}
	}
	private function parseCategories($cats) {
		$retcat = array ();
		if ($cats && is_array ( $cats )) {
			foreach ( $cats as $categoria ) {
				$nombrecat = $this->categoria->darCategoriaNombre ( $categoria->id, $this->idioma->language->id );
				if ($nombrecat) {
					$retcat [$categoria->id] = array (
							"url" => $nombrecat->url_amigable,
							"nombre" => $nombrecat->nombre,
							"nivel" => $categoria->nivel,
							"cantidad" => $categoria->cantidad 
					);
				}
			}
		}
		return $retcat;
	}
	public function process() {
		$data = array ();
		return array_merge ( parent::process (), $data );
	}
	public function info() {
		phpinfo ();
	}
}