<?php
$inicio = (isset ( $inicio ) ? intval ( $inicio ) + 1 : 0);
$totalpagina = (isset ( $totalpagina ) ? intval ( $totalpagina ) : 0);
$total = (isset ( $totalEnVenta ) ? intval ( $totalEnVenta ) : 0);
$final = (isset ( $finalEnVenta ) ? intval ( $finalEnVenta ) : 0);
$cantidadVendidos = ((isset ( $articulos ) && is_array ( $articulos ) && count ( $articulos ) > 0) ? count ( $articulos ) : 0);
$vencimientoOferta = intval ( $this->configuracion->variables ( "vencimientoOferta" ) ) * 86400;
?><header class="cont-cab" id="on-sell">
	<h1>En Venta</h1>
	<p>
		<?php
		if ($cantidadVendidos > 0 || $seccion_nuevo) {
			if (! $preview) {
				?><a href="store/<?=$usuario->seudonimo?>/sell"
			title="<?=traducir("Volver a resumen de ventas")?>"><?=traducir("Volver a resumen de ventas")?></a> | <?php
			}
			?>
			<?=$total?> artículos, mostrando del <strong><?=$cantidadVendidos>0?$inicio:0;?></strong>
		al <strong id="contadorFinal"><?=($inicio+$cantidadVendidos-1)?></strong>
			<?php
		} else {
			print traducir ( "Aquí se mostrarán los artículos que tengas en venta." );
		}
		?>
		</p>
</header>
<?php
if ($cantidadVendidos > 0 || $seccion_nuevo) {
	$listaSub = array (
			array (
					"url" => ($preview ? "store/$usuario->seudonimo/sell#on-sell" : "store/$usuario->seudonimo/sell/2/detail"),
					"texto" => "Ver todos",
					"title" => "Ver todos" 
			),
			array (
					"url" => ($preview ? "store/$usuario->seudonimo/sell/new#on-sell" : "store/$usuario->seudonimo/sell/2/new"),
					"texto" => "Sólo nuevas ofertas " . ($ofertasPendientes ? "($ofertasPendientes)" : "") . "",
					"title" => "ver nuevas ofertas" 
			) 
	);
	$uri = uri_string ();
	?>
<table class="tablesorter">
	<thead>
		<tr>
			<th class="pln">
			<?php
	foreach ( $listaSub as $i => $l ) {
		$la = explode ( "#", $l ["url"] );
		$la = array_shift ( $la );
		$uri = explode ( "/", $uri );
		if ($uri [count ( $uri ) - 1] == "pending") {
			array_pop ( $uri );
		}
		$uri = implode ( "/", $uri );
		if ($la == $uri) {
			print $l ["texto"];
		} else {
			?><a href="<?=$l["url"]?>" title="<?=$l["title"]?>"><?=$l["texto"]?></a><?php
		}
		if ($i < count ( $listaSub ) - 1) {
			?> <span class="dark-grey">|</span> <?php
		}
	}
	?>
			</th>
			<th class="t103 t-r orderby" data-who="on-sell"
				data-orderby="follower" data-asc="asc">Seguidores</th>
			<th class="t201 t-r orderby" data-who="on-sell" data-orderby="deals"
				data-asc="asc">Pujas / Ofertas</th>
			<th class="t151 t-r orderby" data-who="on-sell" data-orderby="time"
				data-default="true" data-asc="desc">Tiempo restante</th>
			<th class="t151 t-r orderby" data-who="on-sell" data-orderby="price"
				data-asc="asc">Precio</th>
		</tr>
	</thead>
	<tbody>
		<?php
	
	if (isset ( $articulos ) && is_array ( $articulos ) && count ( $articulos ) > 0) {
		$vw = intval ( $this->configuracion->variables ( "imagenArticuloMinimoAncho" ) );
		$vh = intval ( $this->configuracion->variables ( "imagenArticuloMinimoAlto" ) );
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
				?><tr
			class="<?php
				
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
				href="product/<?="$articulo->id-".normalizarTexto($articulo->titulo);?>"
				title="<?=($articulo->titulo)?>"><?=($articulo->titulo)?></a><?php
				if ($articulo->tipo == "Cantidad") {
					print "<br/>" . traducir ( "Cantidad" ) . ": " . $articulo->cantidad;
				}
				?></td>
			<td class="t-r"><?=$articulo->seguidores?></td>
			<td class="t-r"><?php
				if ($articulo->tipo !== "Cantidad") {
					if ($articulo->tipo == "Fijo") {
						print (isset ( $articulo->cantidadOfertas )) ? intval ( $articulo->cantidadOfertas ) . " " . traducir ( "ofertas" ) : "--";
					} else {
						print (isset ( $articulo->cantidadPujas )) ? intval ( $articulo->cantidadPujas ) . " " . traducir ( "pujas" ) : "--";
					}
					if ($articulo->ofertasPendientes > "0" && $articulo->tipo == "Fijo") {
						?><br /> <a
				href="articulo/modal/<?=($articulo->tipo == "Fijo"?"ofertas":"pujas")?>/ofertas/<?=$articulo->id?>"
				class="nmodal"><?=traducir("Nueva oferta")?></a><?php
					}
				} else {
					print "--";
				}
				?></td>
			<td class="t-r"><?
				if ($articulo->tipo == "Fijo" || $articulo->tipo == "Cantidad") {
					print calculaTiempoDiferencia ( date ( "Y-m-d H:i:s" ), strtotime ( $articulo->fecha_registro ) + $vencimientoOferta, true );
				} else {
					print calculaTiempoDiferencia ( date ( "Y-m-d H:i:s" ), strtotime ( $articulo->fecha_registro ) + $articulo->duracion * 86400, true );
				}
				?></td>
			<td class="t-r"><?=formato_moneda($articulo->tipo=="Fijo" || $articulo->tipo=="Cantidad"?$articulo->precio:$articulo->mayorPuja)." €"?></td>
		</tr><?php
			}
		}
	} else {
		?><tr>
			<td colspan="6" align="center"
				style="padding: 0 0 1px 0; border: none;"></td>
		</tr>
		<tr>
			<td colspan="6" align="center" style="padding: 30px;">No hay anuncios
				con nuevas ofertas.</td>
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
		?><a href="store/<?=$usuario->seudonimo?>/sell/2/detail"
		title="<?=traducir("Ver Detalle")?>"><?=traducir("Ver más")?></a><?php
	} else {
		?><img src="assets/images/ico/ajax-loader-see-more.gif"
		alt="<?=traducir("Ver más")?>" style="display: none;" /><a href="#"
		title="<?=traducir("Ver más productos")?>"
		onclick="return verMasArticulosEnVenta('<?=$final?>','<?=$section?>');"><?=traducir("Ver más")?></a><a
		href="#" title="<?=traducir("Ir al primer artículo")?>"
		onclick="document.body.scrollTop=0; return false;"
		style="display: none;"><?=traducir("Ir al primer artículo")?></a><?php
	}
	?>
	</p><?php
}
?>