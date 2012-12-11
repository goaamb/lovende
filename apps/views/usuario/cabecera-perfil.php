<?php
$seccionPerfil = isset ( $seccionPerfil ) ? $seccionPerfil : "";
$seccion = (isset ( $seccion ) ? $seccion : "");
$totalVentas = isset ( $totalVentas ) ? $totalVentas : 0;
$totalCompras = isset ( $totalCompras ) ? $totalCompras : 0;
$totalSeguimientos = isset ( $totalSeguimientos ) ? $totalSeguimientos : 0;
$totalMensajes = isset ( $totalMensajes ) ? $totalMensajes : 0;
$totalCuentas = isset ( $totalCuentas ) ? $totalCuentas : 0;
?><div id="cabeceraPerfil"><?php
if (isset ( $seccion ) && isset ( $usuario ) && $usuario) {
	$imagen = imagenPerfil ( $usuario );
	$ver = false;
	switch ($seccion) {
		case "perfil" :
			$ver = true;
			?><script type="text/javascript">
			usuarioID="<?=$usuario->id;?>";
			</script><?php
			break;
		case "articulo" :
			$c = $this->articulo->cantidadOfertas ( $articulo->id, $usuario->id );
			$nofertas = $this->articulo->cantidadOfertas ( $articulo->id );
			$nofertas = $nofertas ? $nofertas->cantidad : 0;
			$ofertante = $c && $c->cantidad > 0;
			$comprador = $usuario->id === $articulo->comprador;
			if ($transaccion) {
				$comprador = $usuario->id === $transaccion->comprador->id;
			}
			$ver = isset ( $articulo ) && ($usuario->id == $articulo->usuario->id || $ofertante || $comprador || ($this->myuser && $articulo->terminado == 1));
			break;
	}
	if ($ver) {
		?><div class="user-data clearfix">
		<div class="image">
			<div style="background:white url(<?=$imagen."?rand=".rand();?>) center center no-repeat scroll;width:150px;height:150px;"></div>
		</div>
		<section>
			<header style="margin-bottom: 0px;">
				<h1>
					<span
						<?php
		if ($usuario->estado == "Baneado") {
			print "class='baneado'";
		}
		?>><?=ucfirst($usuario->seudonimo)?></span> <a
						href="home/modal/votos/votos/<?=$usuario->id?>" class="nmodal"><span
						class="green">+<?=$usuario->positivo?></span>
					<?php if($usuario->negativo>0){?><span class="red">-<?=$usuario->negativo?></span><?php }?></a>
					<span class="localization"><?=($usuario->pais?$usuario->pais->nombre:"")?></span>
				</h1>
			<?php if($seccion=="perfil" && $this->myuser && $this->myuser->id == $usuario->id && $this->myuser->estado!=="Baneado"){?>
			<p class="actions">
					<a href="edit" title="editar">Editar</a>
				</p>
			<?php }?>
		</header><?php
		$baneado = false;
		switch ($seccion) {
			case "perfil" :
				if ($this->myuser && $this->myuser->id == $usuario->id) {
					if ($this->myuser->estado !== "Baneado") {
						$listaSecciones = array (
								array (
										"url" => "store/" . $usuario->seudonimo,
										"seccion" => "Tienda" 
								),
								array (
										"url" => "store/" . $usuario->seudonimo . "/sell",
										"seccion" => "Ventas" . ($totalVentas > 0 ? " ($totalVentas)" : "") 
								),
								array (
										"url" => "store/" . $usuario->seudonimo . "/self",
										"seccion" => "Compras" . ($totalCompras > 0 ? " ($totalCompras)" : "") 
								),
								array (
										"url" => "store/" . $usuario->seudonimo . "/following",
										"seccion" => "Seguimientos" . ($totalSeguimientos > 0 ? " ($totalSeguimientos)" : "") 
								),
								array (
										"url" => "store/" . $usuario->seudonimo . "/messages",
										"seccion" => "Mensajes" . ($totalMensajes > 0 ? " ($totalMensajes)" : "") 
								),
								array (
										"url" => "store/" . $usuario->seudonimo . "/billing",
										"seccion" => "Cuentas" . ($totalCuentas > 0 ? " ($totalCuentas)" : "") 
								) 
						);
					} else {
						$baneado = true;
					}
				} else {
					if ($usuario->estado == "Baneado") {
						$baneado = true;
					} else {
						$link1 = "home/modal/enviar-mensaje-privado/mensaje/$usuario->id";
						$link2 = "home/modal/denunciar/usuario/$usuario->id";
						$class = "nmodal";
						if ($usuarioExterno && ! $this->myuser) {
							$link1 = "login/" . str_replace ( "=", "", base64_encode ( uri_string () ) );
							$link2 = $link1;
							$class = "";
						}
						$listaSecciones = array (
								array (
										"url" => "store/" . $usuario->seudonimo,
										"seccion" => "Tienda" 
								),
								array (
										"url" => $link1,
										"seccion" => "Enviar mensaje privado",
										"class" => $class 
								),
								array (
										"url" => $link2,
										"seccion" => "Denunciar",
										"class" => $class 
								) 
						);
					}
				}
				
				if ($baneado) {
					if ($this->myuser && $this->myuser->id == $usuario->id) {
						?><div class="justify red">
				<p>Tu cuenta fue suspendida por uno de los siguientes motivos:</p>
				<ul class="list-dot">
					<li><strong>Retraso en el pago de facturas:</strong> realiza el
						pago de tus facturas con retraso para reactivar tu cuenta y tus
						anuncios.</li>
					<li><strong>Violación de los términos de uso:</strong> para
						consultas <a href="home/modal/enviar-mail/mail/3" class="nmodal">envíanos
							un e-mail</a>.</li>
				</ul>
			</div><?php
					} else {
						?><div class="justify red">
				<p>La cuenta de este usuario ha sido suspendida.</p>
			</div><?php
					}
				} else {
					?>
		<p class="actions" style="margin-bottom: 15px;"><?php
					$uri = uri_string ();
					if (array_search ( $section, array (
							"auction",
							"item" 
					) ) !== false) {
						$uri = "store/" . $usuario->seudonimo;
					}
					foreach ( $listaSecciones as $i => $secciones ) {
						if ($uri == $secciones ["url"]) {
							print traducir ( $secciones ["seccion"] );
						} else {
							?><a href="<?=$secciones["url"]?>"
					<?=(isset($secciones["class"])?"class='".$secciones["class"]."'":"");?>
					title="<?=traducir($secciones["seccion"])?>"><?=traducir($secciones["seccion"])?></a><?php
						}
						if ($i < count ( $listaSecciones ) - 1) {
							print " | ";
						}
					}
					?></p>
			<div class="justify">
				<p><?=(trim($usuario->descripcion)!==""?parse_text_html( $usuario->descripcion):"Para añadir la foto de perfil o texto aquí utiliza el link editar en la esquina superior derecha.")?></p>
			</div>
		<?php
				}
				break;
			
			case "articulo" :
				if (! $articulo->terminado) {
					if (! $ofertante) {
						if ($nofertas > 0) {
							if ($articulo->tipo == "Fijo") {
								$tituloCabecera = "<p>Tienes nuevas ofertas: <a class='nmodal' href='articulo/modal/ofertas/ofertas/$articulo->id'>revisar ofertas pendientes.</a></p>";
							} else {
								$tituloCabecera = "<p>Tu artículo se venderá.</p>";
							}
							$menuCabecera = array (
							
									array (
											"link" => "articulo/modal/nota-aclarativa/articulo/$articulo->id",
											"descripcion" => "Añadir aclaración en la descripción.",
											"class" => "nmodal" 
									),
									array (
											"link" => "product/nuevo/$articulo->id",
											"descripcion" => "Poner en venta un	artículo similar.",
											"class" => "" 
									),
									array (
											"link" => "articulo/modal/finalizar-anuncio/ofertas/$articulo->id",
											"descripcion" => "Finalizar el anuncio.",
											"class" => "nmodal" 
									) 
							);
						} else {
							$nvendidos = 0;
							if ($articulo->tipo == "Cantidad") {
								$nvendidos = $this->articulo->obtenerVendidosDeCantidad ( $articulo->id );
							}
							if ($nvendidos == 0) {
								if ($this->myuser && $this->myuser->id == $articulo->usuario->id) {
									$tituloCabecera = "<p>Has publicado el anuncio correctamente. Ahora puedes:</p>";
									$menuCabecera = array (
											array (
													"link" => "product/edit/$articulo->id",
													"descripcion" => "Editar este anuncio.",
													"class" => "" 
											),
											array (
													"link" => "product/nuevo/$articulo->id",
													"descripcion" => "Poner en venta un	artículo similar.",
													"class" => "" 
											),
											array (
													"link" => "articulo/modal/finalizar-anuncio/ofertas/$articulo->id",
													"descripcion" => "Finalizar el anuncio.",
													"class" => "nmodal" 
											) 
									);
								}
							} else {
								if ($transaccion) {
									if ($articulo->usuario->id == $this->myuser->id) {
										$tituloCabecera = "<p><span class='green' style='font-weight:bold;'>¡Felicidades!</span> Has vendido <strong>" . ($transaccion->cantidad > 1 ? "$transaccion->cantidad unidades" : "$transaccion->cantidad unidad") . "</strong> de este artículo al usuario <a href='store/{$transaccion->comprador->seudonimo}' title='Ir al perfil {$transaccion->comprador->seudonimo}'>{$transaccion->comprador->seudonimo}</a> por <strong>" . formato_moneda ( $articulo->precio * $transaccion->cantidad ) . " &euro;</strong></p>";
										$menuCabecera = array (
												array (
														"link" => "store/{$usuario->seudonimo}/sell/1/detail",
														"descripcion" => "Ir a mis artículos vendidos.",
														"class" => "" 
												),
												array (
														"link" => "home/modal/modificar-cantidad/articulo/$articulo->id",
														"descripcion" => "Modificar cantidad disponible.",
														"class" => "nmodal" 
												)
												);
									} else if ($transaccion->comprador->id == $this->myuser->id) {
										$tituloCabecera = "<p><span class='green' style='font-weight:bold;'>¡Felicidades!</span> Has comprado <strong>" . ($transaccion->cantidad > 1 ? "$transaccion->cantidad unidades" : "$transaccion->cantidad unidad") . "</strong> de este artículo por <strong>" . formato_moneda ( $articulo->precio * $transaccion->cantidad ) . " &euro;</strong></p>";
										$tituloCabecera .= "<p>Ahora debes realizar el pago en tus <a href='store/{$usuario->seudonimo}/self'>artículos comprados.</a>";
										$menuCabecera = array ();
									}
								} else {
									$tituloCabecera = "<p>Todavía tienes unidades disponibles de este artículo.<p>";
									$menuCabecera = array (
											
											array (
													"link" => "home/modal/modificar-cantidad/articulo/$articulo->id",
													"descripcion" => "Modificar cantidad disponible.",
													"class" => "nmodal" 
											),
											array (
													"link" => "articulo/modal/nota-aclarativa/articulo/$articulo->id",
													"descripcion" => "Añadir aclaración en la descripción.",
													"class" => "nmodal" 
											),
											array (
													"link" => "articulo/modal/finalizar-anuncio/ofertas/$articulo->id",
													"descripcion" => "Finalizar el anuncio.",
													"class" => "nmodal" 
											) 
									);
								}
							}
						}
					} else {
						$maxof = $this->configuracion->variables ( "maximoCantidad" );
						$uo = $this->articulo->ultimaOferta ( $articulo->id, $usuario->id );
						$nof = $maxof - $uo->cantidad;
						$mo = $this->articulo->mayorOferta ( $articulo->id, $articulo->tipo !== "Fijo" );
						if ($uo) {
							if ($articulo->tipo == "Fijo") {
								$ofertaError = "";
								switch ($uo->estado) {
									case "Rechazado" :
										$ofertaError = "<p><span class='red' style='font-weight:bold;'>Lo sentimos</span>, tu oferta ha sido rechazada.</p>";
										break;
									case "Pendiente" :
										$ofertaError = "<p>Tu oferta ha sido enviada al vendedor.</p>";
										break;
								}
								$ofertaError .= "<p>Tu última oferta ha sido de " . formato_moneda ( $uo->monto ) . " &euro;.</p>";
								if ($nof > 0) {
									$ofertaError .= "<p>Puedes hacer $nof ofertas más.</p>";
								} else {
									$ofertaError .= "<p>No puedes hacer más ofertas.</p>";
								}
							} elseif ($mo) {
								$ofertaError = "";
								if ($uo->id == $mo->id) {
									$ofertaError = "<p><span class='green' style='font-weight:bold;'>¡Felicidades!</span> eres el máximo pujador.</p>";
									$ofertaError .= "<p>Tu puja máxima son " . formato_moneda ( $uo->monto ) . " &euro;.</p>";
									$ofertaError .= "<p>Puedes aumentar tu puja máxima para evitar que te sobrepujen en el último momento.</p>";
								} else {
									$ofertaError = "<p><span class='red' style='font-weight:bold;'>Lo sentimos</span>, te han sobrepujado.</p>";
									$ofertaError .= "<p>Tu puja máxima son " . formato_moneda ( $uo->monto ) . " &euro;.</p>";
									$ofertaError .= "<p>Aumenta tu puja para poder ser el máximo pujador.</p>";
								}
							}
						}
						$tituloCabecera = isset ( $ofertaError ) ? $ofertaError : "";
						$menuCabecera = array ();
					}
				} else if ($articulo->estado == "A la venta") {
					if ($articulo->tipo !== "Cantidad") {
						if ($articulo->usuario->id == $this->myuser->id) {
							$tituloCabecera = "<p>Tu artículo ha finalizado sin venderse.</p>";
							$menuCabecera = array (
									array (
											"link" => "product/begin/$articulo->id",
											"descripcion" => "Volver a poner en venta.",
											"class" => "" 
									),
									array (
											"link" => "store/{$usuario->seudonimo}/sell/3/detail",
											"descripcion" => "Ir a mis artículos no vendidos.",
											"class" => "" 
									) 
							);
						} else {
							$tituloCabecera = "<p><span class='red' style='font-weight:bold;'>Lo sentimos</span>, el vendedor finalizó el anuncio anticipadamente.";
							$menuCabecera = array ();
						}
					} else {
						if ($transaccion) {
							if ($articulo->usuario->id == $this->myuser->id) {
								$tituloCabecera = "<p><span class='green' style='font-weight:bold;'>¡Felicidades!</span> Has vendido <strong>" . ($transaccion->cantidad > 1 ? "$transaccion->cantidad unidades" : "$transaccion->cantidad unidad") . "</strong> de este artículo al usuario <a href='store/{$transaccion->comprador->seudonimo}' title='Ir al perfil {$transaccion->comprador->seudonimo}'>{$transaccion->comprador->seudonimo}</a> por <strong>" . formato_moneda ( $articulo->precio * $transaccion->cantidad ) . " &euro;</strong></p>";
								$menuCabecera = array (
										array (
												"link" => "store/{$usuario->seudonimo}/sell/1/detail",
												"descripcion" => "Ir a mis artículos vendidos.",
												"class" => "" 
										),
										array (
												"link" => "home/modal/modificar-cantidad/articulo/$articulo->id",
												"descripcion" => "Modificar cantidad disponible.",
												"class" => "nmodal" 
										),
										
										
								);
							} else if ($transaccion->comprador->id == $this->myuser->id) {
								$tituloCabecera = "<p><span class='green' style='font-weight:bold;'>¡Felicidades!</span> Has comprado <strong>" . ($transaccion->cantidad > 1 ? "$transaccion->cantidad unidades" : "$transaccion->cantidad unidad") . "</strong> de este artículo por <strong>" . formato_moneda ( $articulo->precio * $transaccion->cantidad ) . " &euro;</strong></p>";
								$tituloCabecera .= "<p>Ahora debes realizar el pago en tus <a href='store/{$usuario->seudonimo}/self'>artículos comprados.</a>";
								$menuCabecera = array ();
							}
						} elseif ($articulo->usuario->id == $this->myuser->id) {
							if ($articulo->cantidad == 0) {
								$tituloCabecera = "<p>Tu artículo ha quedado sin stock.</p>";
							} else {
								$tituloCabecera = "<p>Tu artículo ha finalizado.</p>";
							}
							$linkx = "product/begin/$articulo->id";
							$clasx = "";
							if ($articulo->tipo == "Cantidad") {
								if ($this->articulo->cantidad == 0) {
									$linkx = "home/modal/modificar-cantidad/articulo/$articulo->id";
									$clasx = "nmodal";
								}
							}
							
							$menuCabecera = array (
									array (
											"link" => $linkx,
											"descripcion" => "Volver a poner en venta.",
											"class" => $clasx 
									),
									array (
											"link" => "product/nuevo/$articulo->id",
											"descripcion" => "Poner en venta un	artículo similar.",
											"class" => "" 
									) 
							);
						} else {
							$tituloCabecera = "<p><span class='red' style='font-weight:bold;'>Lo sentimos</span>, este anuncio ha finalizado.";
							$menuCabecera = array ();
						}
					}
				} elseif ($articulo->comprador == $usuario->id) {
					$tituloCabecera = "<p><span class='green' style='font-weight:bold;'>¡Felicidades!</span> has comprado este artículo.</p>";
					$tituloCabecera .= "<p>Ahora debes realizar el pago en tus <a href='store/{$usuario->seudonimo}/self'>artículos comprados.</a>";
					$menuCabecera = array ();
				} elseif ($articulo->usuario->id !== $usuario->id) {
					$tituloCabecera = "<p><span class='red' style='font-weight:bold;'>Lo sentimos</span>, este artículo ha sido vendido a otro usuario.";
					$menuCabecera = array ();
				} elseif ($articulo->comprador) {
					$og = $this->articulo->darOfertaGanadora ( $articulo->id );
					if (! $og) {
						$monto = $articulo->precio;
					} else {
						if ($articulo->tipo == "Fijo") {
							$monto = $og->monto;
						} else {
							$monto = $og->monto_automatico;
						}
					}
					$tituloCabecera = "<p><span class='green' style='font-weight:bold;'>¡Felicidades!</span> Tu artículo se ha vendido por " . formato_moneda ( $monto ) . " &euro;.</p>";
					$menuCabecera = array (
							array (
									"link" => "store/{$usuario->seudonimo}/sell",
									"descripcion" => "Ir a mis artículos vendidos.",
									"class" => "" 
							),
							array (
									"link" => "product/nuevo/$articulo->id",
									"descripcion" => "Poner en venta un	artículo similar.",
									"class" => "" 
							) 
					);
				}
				if (isset ( $menuCabecera ) && isset ( $tituloCabecera )) {
					$this->load->view ( "usuario/menu-cabecera-perfil", array (
							"titulo" => $tituloCabecera,
							"listaMenu" => $menuCabecera 
					) );
				}
				break;
		}
		?></section>
	</div><?php
	}
}
?></div>