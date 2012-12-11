<?php
$inicio = (isset ( $inicio ) ? intval ( $inicio ) + 1 : 0);
$totalpagina = (isset ( $totalpagina ) ? intval ( $totalpagina ) : 0);
$total = (isset ( $totalEnCompra ) ? intval ( $totalEnCompra ) : 0);
$final = (isset ( $finalEnCompra ) ? intval ( $finalEnCompra ) : 0);
$cantidadVendidos = ((isset ( $articulos ) && is_array ( $articulos ) && count ( $articulos ) > 0) ? count ( $articulos ) : 0);
$vencimientoOferta = intval ( $this->configuracion->variables ( "vencimientoOferta" ) ) * 86400;
?><header class="cont-cab" id="on-buy">
	<h1>Comprando</h1>
	<p>
		<?php
		if ($cantidadVendidos > 0) {
			if (! $preview) {
				?><a href="store/<?=$usuario->seudonimo?>/self"
			title="<?=traducir("Volver a resumen de compras")?>"><?=traducir("Volver a resumen de compras")?></a> | <?php
			}
			?>
			<?=$total?> artículos, mostrando del <strong><?=$cantidadVendidos>0?$inicio:0;?></strong>
		al <strong id="contadorFinal"><?=($inicio+$cantidadVendidos-1)?></strong>
			<?php
		} else {
			print traducir ( "Aquí se mostrarán los anuncios en los que estés pujando o mandando ofertas de compra." );
		}
		?>
		</p>
</header>
<?php
if ($cantidadVendidos > 0) {
	
	?>
<table class="tablesorter">
	<thead>
		<tr>
			<th class="pln orderby" data-who="on-buy" data-orderby="title"
				data-asc="asc"><?=traducir("Título");?></th>
			<th class="t151 t-r orderby" data-who="on-buy" data-orderby="status"
				data-asc="asc">Estado</th>
			<th class="t151 t-r orderby" data-who="on-buy" data-orderby="time"
				data-default="true" data-asc="desc">Tiempo restante</th>
			<th class="t151 t-r orderby" data-who="on-buy" data-orderby="price"
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
				title="<?=($articulo->titulo)?>"><?=($articulo->titulo)?></a></td>
			<td class="t-r"><?=($articulo->tipo=="Fijo"?($articulo->estadoOferta=="Pendiente"?traducir("Oferta enviada"):"<span class='red'>".traducir("Oferta rechazada")."</span>"):($articulo->maximoPujador==$usuario->id?"<span class='green'>".traducir("Máximo pujador")."</span>":"<span class='red'>".traducir("Sobrepujado")."</span>"))?></td>
			<td class="t-r"><?
				if ($articulo->tipo == "Fijo") {
					print calculaTiempoDiferencia ( date ( "Y-m-d H:i:s" ), strtotime ( $articulo->fecha_registro ) + $vencimientoOferta, true );
				} else {
					print calculaTiempoDiferencia ( date ( "Y-m-d H:i:s" ), strtotime ( $articulo->fecha_registro ) + $articulo->duracion * 86400, true );
				}
				?></td>
			<td class="t-r"><?=formato_moneda($articulo->tipo=="Fijo"?$articulo->precio:$articulo->mayorPuja)." €"?></td>
		</tr><?php
			}
		}
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
		?><a href="store/<?=$usuario->seudonimo?>/self/2/detail"
		title="<?=traducir("Ver Detalle")?>"><?=traducir("Ver más")?></a><?php
	} else {
		?><img src="assets/images/ico/ajax-loader-see-more.gif"
		alt="<?=traducir("Ver más")?>" style="display: none;" /><a href="#"
		title="<?=traducir("Ver más productos")?>"
		onclick="return verMasArticulosEnCompra('<?=$final?>','<?=$section?>');"><?=traducir("Ver más")?></a><a
		href="#" title="<?=traducir("Ir al primer artículo")?>"
		onclick="document.body.scrollTop=0; return false;"
		style="display: none;"><?=traducir("Ir al primer artículo")?></a><?php
	}
	?>
	</p><?php
}
?>