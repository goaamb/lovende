<?php
$inicio = (isset ( $inicio ) ? intval ( $inicio ) + 1 : 0);
$totalpagina = (isset ( $totalpagina ) ? intval ( $totalpagina ) : 0);
$total = (isset ( $totalComprados ) ? intval ( $totalComprados ) : 0);
$final = (isset ( $finalComprados ) ? intval ( $finalComprados ) : 0);
$cantidadVendidos = ((isset ( $articulos ) && is_array ( $articulos ) && count ( $articulos ) > 0) ? count ( $articulos ) : 0);
$tdenuncia1a = floatval ( $this->configuracion->variables ( "denuncia1a" ) );
$tdenuncia3a = floatval ( $this->configuracion->variables ( "denuncia3a" ) );
$tdenuncia4a = floatval ( $this->configuracion->variables ( "denuncia4a" ) );
?><header class="cont-cab" id="buyed">
	<h1>Comprados</h1>
	<p>
		<?php
		if ($cantidadVendidos > 0 || $seccion_pendiente) {
			if (! $preview) {
				?><a href="store/<?=$usuario->seudonimo?>/self"
			title="<?=traducir("Volver a resumen de compras")?>"><?=traducir("Volver a resumen de compras")?></a> | <?php
			}
			?>
			<?=$total?> artículos, mostrando del <strong><?=$cantidadVendidos>0?$inicio:0;?></strong>
		al <strong id="contadorFinal"><?=($inicio+$cantidadVendidos-1)?></strong>
			<?php
		} else {
			print traducir ( "Aquí se mostrarán los artículos que hayas comprado." );
		}
		?>
		</p>
</header>
<?php if($cantidadVendidos>0|| $seccion_pendiente){?>
<table class="tablesorter">
	<thead>
		<tr>
			<th class="pln"><?php
	if (! $seccion_pendiente) {
		?>Ver todos<?php
	} else {
		?><a
				href="<?php
		if ($preview) {
			print "store/" . $this->myuser->seudonimo . "/self#selled";
		} else {
			print "store/" . $this->myuser->seudonimo . "/self/1/detail";
		}
		?>"
				title="ver todos los artículos">Ver todos</a><?php
	}
	?> <span class="dark-grey">|</span> 
			<?php
	if (! $seccion_pendiente) {
		?>
			<a
				href="<?php
		if ($preview) {
			print "store/" . $this->myuser->seudonimo . "/self/pending#selled";
		} else {
			print "store/" . $this->myuser->seudonimo . "/self/1/pending";
		}
		?>"
				title="ver productos pendientes">Sólo pendientes <?=($totalCompras>0?"($totalCompras)":"")?></a><?php
	} else {
		?>Sólo pendientes <?php
		print ($totalCompras > 0 ? "($totalCompras)" : "") ;
	}
	?></th>
			<th class="t103 t-r orderby" data-who="buyed" data-orderby="expenses"
				data-asc="desc">Gastos envío</th>
			<th class="t103 t-r orderby" data-who="buyed" data-orderby="pay"
				data-asc="desc">Pago</th>
			<th class="t103 t-r orderby" data-who="buyed" data-orderby="received"
				data-asc="desc">Recepción</th>
			<th class="t151 t-r orderby" data-who="buyed" data-orderby="time"
				data-default="true" data-asc="desc">Fecha / Vendedor</th>
			<th class="t137 t-r orderby" data-who="buyed" data-orderby="price"
				data-asc="desc">Precio + Envío</th>
		</tr>
	</thead>
	<tbody>
		<?php
	
	if (isset ( $articulos ) && is_array ( $articulos ) && count ( $articulos ) > 0) {
		$vw = intval ( $this->configuracion->variables ( "imagenArticuloMinimoAncho" ) );
		$vh = intval ( $this->configuracion->variables ( "imagenArticuloMinimoAlto" ) );
		$totalMonto = 0;
		$grupo = false;
		$ahora = time ();
		foreach ( $articulos as $i => $articulo ) {
			
			$imagen = array_shift ( explode ( ",", $articulo->foto ) );
			$imagen = imagenArticulo ( $articulo->usuario, $imagen, "thumb" );
			if ($imagen) {
				list ( $w, $h ) = getimagesize ( BASEPATH . "../$imagen" );
				$nw = intval ( $vw );
				$nh = ceil ( $nw * $h / $w );
				if ($nh > $vh) {
					$nh = intval ( $vh );
					$nw = ceil ( $nh * $w / $h );
				}
				$gastos = formato_moneda ( $articulo->gastos_envio ) . " €";
				$estado1 = ($articulo->estado == "Sin gastos Envio" ? traducir ( "Esperando" ) : (($articulo->estado == "Sin Pago") ? $gastos : (($articulo->fecha_disputa1) ? $gastos : "<span class='green'>$gastos</span>")));
				$dif = ($ahora - strtotime ( $articulo->fecha_terminado )) / 86400;
				$dif2 = ($ahora - strtotime ( $articulo->fecha_pago )) / 86400;
				$dif3 = ($ahora - strtotime ( $articulo->fecha_envio )) / 86400;
				?><tr
			class="<?php
				
				if ($i < count ( $articulos ) - 1) {
					if ($articulo->paquete) {
						if ($articulo->paquete == $articulos [$i + 1]->paquete) {
							print " no-border";
							if (! $grupo) {
								$grupo = true;
							}
						} else {
							$grupo = false;
						}
					} elseif ($articulo->usuario->id == $articulos [$i + 1]->usuario->id && $articulo->pagos == $articulos [$i + 1]->pagos) {
						print " no-border";
						if (! $grupo) {
							$grupo = true;
						}
					} else {
						$grupo = false;
					}
				}
				if ($articulo->estado == "Disputa" || $articulo->denuncia1 || $articulo->denuncia2 || $articulo->denuncia3 || $articulo->denuncia4) {
					print " remarc";
				}
				if ($i == count ( $articulos ) - 1) {
					print " last-child";
					if ($final >= $total) {
						print " border";
					}
				}
				
				?>">
			<td class="td-item"><div class="imagen td-imagen">
					<img src="<?="$imagen"?>" width="<?=$nw;?>" height="<?=$nh;?>"
						alt="<?=($articulo->titulo)?>" />
				</div> <a
				href="product/<?php
				print "$articulo->id-" . normalizarTexto ( $articulo->titulo );
				if ($articulo->transaccion) {
					print "/$articulo->transaccion";
				}
				?>"
				<?php if($articulo->usuario->estado=="Baneado"){print "class='baneado'";}?>
				title="<?=($articulo->titulo)?>"><?=($articulo->titulo)?></a><?php
				if ($articulo->tipo == "Cantidad") {
					?><br /> <span>Cantidad: <?=$articulo->cantidad?></span><?php
				}
				?></td>
			<td class="t-r"><?
				if ($i <= 0 || $articulo->paquete !== $articulos [$i - 1]->paquete || $articulo->pagos !== $articulos [$i - 1]->pagos || $articulo->usuario->id !== $articulos [$i - 1]->usuario->id) {
					print $estado1;
					$totalMonto = 0;
				}
				if ($articulo->tipo !== "Cantidad") {
					$totalMonto += $articulo->precio;
				} else {
					$totalMonto += $articulo->precio * $articulo->cantidad;
				}
				?></td>
			<td class="t-r"><?php
				if ($i <= 0 || $articulo->paquete !== $articulos [$i - 1]->paquete || $articulo->pagos !== $articulos [$i - 1]->pagos || $articulo->usuario->id !== $articulos [$i - 1]->usuario->id) {
					if (! $articulo->tipo_pago) {
						$articulo->tipo_pago = 1;
					}
					$lista = array (
							1 => "P. Otros",
							2 => "P. C. reembolso",
							3 => "P. Trans.",
							4 => "P. Paypal" 
					);
					
					switch ($articulo->estado) {
						case "Sin Pago" :
							if ($articulo->denuncia2) {
								?><a
				href="home/modal/retraso-pago/vendedor/<?=$articulo->usuario->id?>"
				class="nmodal red">Denunciado</a><br /><?php
							}
							if ($articulo->usuario->estado !== "Baneado") {
								print "<a href='home/modal/pagar-articulos/articulosComprados/{$articulo->comprador->id}/$articulo->paquete' title='" . traducir ( "Pagar" ) . "' class='nmodal'>" . traducir ( "Pagar" ) . "</a>";
							} else {
								print "Inhabilitado por baneo.";
							}
							break;
						case "Sin Envio" :
						case "Enviado" :
						case "Recibido" :
						case "Disputa" :
							if ($articulo->fecha_disputa1) {
								print "<span class='red'>Disputa</span><br/>";
								if ($articulo->usuario->estado !== "Baneado") {
									print "<a href='home/modal/pagar-articulos/articulosComprados/{$articulo->comprador->id}/$articulo->paquete' title='" . traducir ( "Pagar" ) . "' class='nmodal'>" . traducir ( "Pagar" ) . "</a>";
								} else {
									print "Inhabilitado por baneo.";
								}
							} else {
								print "<span class='green'>" . date ( "d-m-Y", strtotime ( $articulo->fecha_pago ) ) . "<br/>" . $lista [$articulo->tipo_pago] . "</span>";
							}
							break;
						default :
							print "--";
							break;
					}
				}
				?></td>
			<td class="t-r"><?php
				if ($i <= 0 || $articulo->paquete !== $articulos [$i - 1]->paquete || $articulo->pagos !== $articulos [$i - 1]->pagos || $articulo->usuario->id !== $articulos [$i - 1]->usuario->id) {
					switch ($articulo->estado) {
						case "Sin Envio" :
							if (! $articulo->denuncia3) {
								?><a
				href="home/modal/denuncia-envio/paquete/<?=$articulo->comprador->id."/".$articulo->paquete?>"
				class="nmodal">Denunciar</a><br /><?php
							}
							if ($dif2 >= $tdenuncia3a) {
								if ($articulo->denuncia3) {
									?><span class="red">Denunciado</span><br /><?php
								}
							}
							print "Esperando";
							break;
						case "Enviado" :
							if (! $articulo->denuncia4) {
								?><a
				href="home/modal/denuncia-recibido/paquete/<?=$articulo->comprador->id?>/<?=$articulo->paquete?>"
				class="nmodal">Denunciar</a><br /><?php
							}
							if ($dif3 >= $tdenuncia4a) {
								if ($articulo->denuncia4) {
									?><span class="red">Denunciado</span><br /><?php
								}
							}
							print date ( "d-m-Y" ) . "<br/>" . traducir ( "Enviado" ) . "<br/><a href='home/modal/confirmar-recepcion/paquete/0/{$articulo->paquete}' class='nmodal'>" . traducir ( "Recibido" ) . "</a>";
							break;
						case "Recibido" :
							print "<span class='green'>" . date ( "d-m-Y" ) . "<br/>" . traducir ( "Recibido" ) . "</span>";
							break;
						case "Disputa" :
							if ($articulo->fecha_disputa1) {
								print "--";
							} else {
								print "<span class='red'>Disputa</span><br/>";
								if ($articulo->fecha_disputa3) {
									print date ( "d-m-Y", strtotime ( $articulo->fecha_disputa3 ) ) . "<br/>" . traducir ( "Enviado" ) . "<br/><a href='home/modal/confirmar-recepcion/paquete/0/{$articulo->paquete}' class='nmodal'>" . traducir ( "Recibido" ) . "</a>";
								} elseif ($articulo->denuncia3) {
									print traducir ( "Esperando" );
								} else {
									if ($articulo->denuncia4) {
										?>
										
										<a
				href="home/modal/confirmar-recepcion/paquete/1/<?php echo $articulo->paquete;?>"
				class="nmodal"><?php echo traducir ( "Recibido" ); ?></a>		
									<?php
									} else {
										print traducir ( "Recibido" );
									}
								}
							}
							break;
						default :
							print "--";
							break;
					}
				}
				?></td>
			<td class="t-r"><?=date("d-m-Y",strtotime($articulo->fecha_terminado))?><br />
				<a href="store/<?=$articulo->usuario->seudonimo;?>"
				<?php if($articulo->usuario->estado=="Baneado"){print "class='baneado'";}?>
				title="<?=$articulo->usuario->seudonimo;?>"><?php
				if ($i <= 0 || $articulo->paquete !== $articulos [$i - 1]->paquete || $articulo->pagos !== $articulos [$i - 1]->pagos || $articulo->usuario->id !== $articulos [$i - 1]->usuario->id) {
					print $articulo->usuario->seudonimo;
				}
				?></a></td>
			<td class="t-r"><?=formato_moneda($articulo->tipo=="Fijo"?$articulo->precio:($articulo->tipo=="Cantidad"?$articulo->precio*$articulo->cantidad:$articulo->mayorPuja))." €"?><br />
				<?php
				if ((! $grupo || $i == count ( $articulos ) - 1) && $articulo->gastos_envio) {
					print "+" . formato_moneda ( $articulo->gastos_envio ) . " €<br/>";
					$totalMonto += $articulo->gastos_envio;
				}
				?><strong><?php
				
				if ((! $grupo || $i == count ( $articulos ) - 1) && $totalMonto != $articulo->precio) {
					print formato_moneda ( $totalMonto ) . " €";
				}
				?></strong></td>
		</tr><?php
			}
		}
	} else {
		?><tr>
			<td colspan="6" align="center"
				style="padding: 0 0 1px 0; border: none;"></td>
		</tr>
		<tr>
			<td colspan="6" align="center"
				style="padding: 30px; border-top: 1px dotted #A93029;">No hay
				anuncios pendientes.</td>
		</tr><?php
	}
	?>
		</tbody>
</table>
<?php
}
if ($final < $total) {
	?><p class="ver-mas">
	<?php
	if ($preview) {
		?><a href="store/<?=$usuario->seudonimo?>/self/1/detail"
		title="<?=traducir("Ver Detalle")?>"><?=traducir("Ver más")?></a><?php
	} else {
		?><img src="assets/images/ico/ajax-loader-see-more.gif"
		alt="<?=traducir("Ver más")?>" style="display: none;" /><a href="#"
		title="<?=traducir("Ver más productos")?>"
		onclick="return verMasArticulosComprados('<?=$final?>','<?=$section?>');"><?=traducir("Ver más")?></a><a
		href="#" title="<?=traducir("Ir al primer artículo")?>"
		onclick="document.body.scrollTop=0; return false;"
		style="display: none;"><?=traducir("Ir al primer artículo")?></a><?php
	}
	?>
	</p><?php
}
?>