<?php
$inicio = (isset ( $inicio ) ? intval ( $inicio ) + 1 : 0);
$totalpagina = (isset ( $totalpagina ) ? intval ( $totalpagina ) : 0);
$total = (isset ( $totalVendidos ) ? intval ( $totalVendidos ) : 0);
$final = (isset ( $finalVendidos ) ? intval ( $finalVendidos ) : 0);
$soloPendientes = isset ( $soloPendientes ) ? $soloPendientes : 0;
$cantidadVendidos = ((isset ( $articulos ) && is_array ( $articulos ) && count ( $articulos ) > 0) ? count ( $articulos ) : 0);
$tdenuncia2a = floatval ( $this->configuracion->variables ( "denuncia2a" ) );
?><header class="cont-cab" id="selled">
	<h1>Vendidos</h1>
	<p>
		<?php
		if ($cantidadVendidos > 0 || $seccion_pendiente) {
			if (! $preview) {
				?><a href="store/<?=$usuario->seudonimo?>/sell"
			title="<?=traducir("Volver a resumen de ventas")?>"><?=traducir("Volver a resumen de ventas")?></a> | <?php
			}
			?>
			<?=$total?> artículos, mostrando del <strong><?=$cantidadVendidos>0?$inicio:0;?></strong>
		al <strong id="contadorFinal"><?=($inicio+$cantidadVendidos-1)?></strong>
			<?php
		} else {
			print traducir ( "Aquí se mostrarán los artículos que hayas vendido." );
		}
		?>
		</p>
</header>
<?php
if ($cantidadVendidos > 0 || $seccion_pendiente) {
	?>
<table class="tablesorter">
	<thead>
		<tr>
			<th
				class="pln<?php
	if ($cantidadVendidos <= 0) {
		print " no-border";
	}
	?>">
			<?php
	if (! $seccion_pendiente) {
		?>Ver todos<?php
	} else {
		?><a
				href="<?php
		if ($preview) {
			print "store/" . $this->myuser->seudonimo . "/sell#selled";
		} else {
			print "store/" . $this->myuser->seudonimo . "/sell/1/detail";
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
			print "store/" . $this->myuser->seudonimo . "/sell/pending#selled";
		} else {
			print "store/" . $this->myuser->seudonimo . "/sell/1/pending";
		}
		?>"
				title="ver productos pendientes">Sólo pendientes <?=($soloPendientes>0?"($soloPendientes)":"")?></a><?php
	} else {
		?>Sólo pendientes <?php
		print ($soloPendientes > 0 ? "($soloPendientes)" : "") ;
	}
	?></th>
			<th
				class="t103 t-r orderby<?php
	if ($cantidadVendidos <= 0) {
		print " no-border";
	}
	?>"
				data-who="selled" data-orderby="expenses" data-asc="asc">Gastos
				envío</th>
			<th
				class="t103 t-r orderby<?php
	if ($cantidadVendidos <= 0) {
		print " no-border";
	}
	?>"
				data-who="selled" data-orderby="charge" data-asc="asc">Cobro</th>
			<th
				class="t103 t-r orderby<?php
	if ($cantidadVendidos <= 0) {
		print " no-border";
	}
	?>"
				data-who="selled" data-orderby="shipping" data-asc="asc">Envío</th>
			<th
				class="t151 t-r orderby<?php
	if ($cantidadVendidos <= 0) {
		print " no-border";
	}
	?>"
				data-who="selled" data-orderby="time" data-default="true"
				data-asc="desc">Fecha / Comprador</th>
			<th
				class="t137 t-r orderby<?php
	if ($cantidadVendidos <= 0) {
		print " no-border";
	}
	?>"
				data-who="selled" data-orderby="price" data-asc="asc">Precio + Envío</th>
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
				$estado1 = ($articulo->estado == "Sin gastos Envio" ? '<a href="home/modal/anadir-gastos-envio/articulosComprados/' . $articulo->comprador->id . '/' . ($articulo->denuncia1 ? $articulo->paquete : 0) . '/' . str_replace ( ",", "-", $articulo->pagos ) . '" title="' . traducir ( "Añadir" ) . '" class="nmodal">' . traducir ( "Añadir" ) . '</a>' : (($articulo->estado == "Sin Pago") ? $gastos . "<br/>" . '<a href="home/modal/anadir-gastos-envio/articulosComprados/' . $articulo->comprador->id . "/$articulo->paquete" . '" title="' . traducir ( "Editar" ) . '" class="nmodal">' . traducir ( "Editar" ) . '</a>' : (($articulo->fecha_disputa1) ? $gastos . "<br/>" . '<a href="home/modal/anadir-gastos-envio/articulosComprados/' . $articulo->comprador->id . "/$articulo->paquete" . '" title="' . traducir ( "Editar" ) . '" class="nmodal">' . traducir ( "Editar" ) . '</a>' : "<span class='green'>$gastos</span>")));
				$dif = ($ahora - strtotime ( $articulo->fecha_paquete )) / 86400;
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
					} elseif ($articulo->comprador->id == $articulos [$i + 1]->comprador->id && $articulo->pagos == $articulos [$i + 1]->pagos) {
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
				if ($articulo->tipo == "Cantidad") {
					print "/$articulo->transaccion";
				}
				?>"
				title="<?=($articulo->titulo)?>"><?=($articulo->titulo)?></a><?php
				if ($articulo->tipo == "Cantidad") {
					?><br /> <span>Cantidad: <?=$articulo->cantidad?></span><?php
				}
				?></td>
			<td class="t-r"><?
				
				if ($i <= 0 || $articulo->paquete !== $articulos [$i - 1]->paquete || $articulo->pagos !== $articulos [$i - 1]->pagos || $articulo->comprador->id !== $articulos [$i - 1]->comprador->id) {
					if ($articulo->denuncia1) {
						?><a
				href="home/modal/retraso-gastos-envio/comprador/<?=$articulo->comprador->id?>"
				class="nmodal red">Denunciado</a><br /><?php
					}
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
				if ($i <= 0 || $articulo->paquete !== $articulos [$i - 1]->paquete || $articulo->pagos !== $articulos [$i - 1]->pagos || $articulo->comprador->id !== $articulos [$i - 1]->comprador->id) {
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
							?><a
				href="home/modal/denuncia-pago/paquete/<?=$articulo->comprador->id?>/<?=$articulo->paquete?>"
				class="nmodal">Denunciar</a><br /><?php
							if ($dif >= $tdenuncia2a) {
								if ($articulo->denuncia2) {
									?><span class="red">Denunciado</span><br /><?php
								}
							}
							print "Esperando";
							break;
							break;
						case "Enviado" :
						case "Sin Envio" :
						case "Recibido" :
							print "<span class='green'>" . date ( "d-m-Y", strtotime ( $articulo->fecha_pago ) ) . "<br/>" . $lista [$articulo->tipo_pago] . "</span>";
							break;
						case "Disputa" :
							if ($articulo->fecha_disputa1) {
								?><span class="red">Disputa</span><br /><?php
								print "Esperando";
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
				if ($i <= 0 || $articulo->paquete !== $articulos [$i - 1]->paquete || $articulo->pagos !== $articulos [$i - 1]->pagos || $articulo->comprador->id !== $articulos [$i - 1]->comprador->id) {
					switch ($articulo->estado) {
						case "Sin Envio" :
							if ($articulo->denuncia3) {
								?><a
				href="home/modal/retraso-envio/paquete/<?=$articulo->comprador->id."/".$articulo->paquete;?>"
				class="nmodal red">Denunciado</a><br /><?php
							}
							print "<a href='home/modal/confirmar-envio/paquete/{$articulo->comprador->id}/$articulo->paquete' title='" . traducir ( "Enviar" ) . "' class='nmodal'>" . traducir ( "Enviar" ) . "</a>";
							break;
						case "Enviado" :
							if ($articulo->denuncia4) {
								?><a
				href="home/modal/retraso-recibido/paquete/<?=$articulo->comprador->id."/".$articulo->paquete;?>"
				class="nmodal red">Denunciado</a><br /><?php
							}
							print date ( "d-m-Y" ) . "<br/>" . traducir ( "Enviado" ) . "<br/>" . traducir ( "Esperando" );
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
									print date ( "d-m-Y", strtotime ( $articulo->fecha_disputa3 ) ) . "<br/>" . traducir ( "Esperando" );
								} else if ($articulo->denuncia3) {
									print "<a href='home/modal/confirmar-envio/paquete/{$articulo->comprador->id}/$articulo->paquete' title='" . traducir ( "Enviar" ) . "' class='nmodal'>" . traducir ( "Enviar" ) . "</a>";
								} else {
									print traducir ( "Recibido" );
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
				<a href="store/<?=$articulo->comprador->seudonimo;?>"
				title="<?=$articulo->comprador->seudonimo;?>"
				<?php
				if ($articulo->comprador->estado == "Baneado") {
					print "class='baneado'";
				}
				?>><?php
				if ($i <= 0 || $articulo->paquete !== $articulos [$i - 1]->paquete || $articulo->pagos !== $articulos [$i - 1]->pagos || $articulo->comprador->id !== $articulos [$i - 1]->comprador->id) {
					print $articulo->comprador->seudonimo;
				}
				?></a></td>
			<td class="t-r"><?=formato_moneda($articulo->tipo=="Fijo"?$articulo->precio:($articulo->tipo=="Cantidad"?$articulo->precio*$articulo->cantidad:$articulo->mayorPuja))." €"?><br />
			<?php
				if ((! $grupo || $i == count ( $articulos ) - 1) && $articulo->gastos_envio) {
					print "+" . formato_moneda ( $articulo->gastos_envio ) . " €<br/>";
					$totalMonto += $articulo->gastos_envio;
				}
				?>
				<strong><?php
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
		?><a href="store/<?=$usuario->seudonimo?>/sell/1/detail"
		title="<?=traducir("Ver Detalle")?>"><?=traducir("Ver más")?></a><?php
	} else {
		?><img src="assets/images/ico/ajax-loader-see-more.gif"
		alt="<?=traducir("Ver más")?>" style="display: none;" /><a href="#"
		title="<?=traducir("Ver más productos")?>"
		onclick="return verMasArticulosVendidos('<?=$final?>','<?=$section?>');"><?=traducir("Ver más")?></a><a
		href="#" title="<?=traducir("Ir al primer artículo")?>"
		onclick="document.body.scrollTop=0; return false;"
		style="display: none;"><?=traducir("Ir al primer artículo")?></a><?php
	}
	?>
	</p><?php
}
?>