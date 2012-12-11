<?php
$gastos = ($paquete ? ($paquete->gastos_envio >= 0 ? $paquete->gastos_envio : 10) : 10);
$gastos = formato_moneda ( $gastos );
?><div id="popUp">
	<div class="formA">
		<form class="clearfix d-b" action="post" id="formGastos">
			<header>
				<h1>Editar gastos de envío</h1>
				<p>
					Comprador <a href="store/<?=$comprador->seudonimo?>"
						<?php if($comprador->estado=="Baneado"){print "class='baneado'";}?>
						title="Ver perfil de <?=$comprador->seudonimo?>"><strong><?=$comprador->seudonimo?></strong></a>
					<a href="home/modal/votos/votos/<?=$comprador->id?>"
						class="nmodal green">+<?=$comprador->positivo?></a> 
					<?php if($comprador->negativo>0){?>
					<a href="home/modal/votos/votos/<?=$comprador->id?>"
						class="nmodal red">-<?=$comprador->negativo?></a> <?php }?><span
						class="dark-grey">|</span> <a
						href="home/modal/enviar-mensaje-privado/mensaje/<?=$comprador->id?>"
						class="nmodal" title="Enviar mensaje privado">Enviar mensaje
						privado</a>
				</p>
			</header>
			<div class="wrap">
				<h2>Dirección de envío:</h2>
				<p><?=$comprador->nombre?><br /><?=$comprador->direccion?><br />
					<?=$comprador->ciudad->nombre?>, <?=$comprador->codigo_postal?><br /> 
					<?=$comprador->pais->nombre?>
				</p>
				<div class="clearfix mbl d-b">
					<h2 class="mtl">Gastos de envío:</h2>
					<div class="recuadro con-moneda recuadro-apaisado">
						<input type="text" value="<?=$gastos?>" name="gastosEnvioEntrada"
							class="t-r required decimal min-value"
							data-error-decimal="Añade un número decimal superior a 0"
							data-min-value="0"
							data-error-min-value="Añade un número decimal superior a 0"
							onblur="actualizarCosto.call(this.form)" /> <strong>€</strong>
					</div>
					<input type="button" value="Actualizar"
						onclick="actualizarCosto.call(this.form);"
						class="bt bt-apaisado bt-recuadro-apaisado" /> <span
						id="gastosEnvioEntradaError" class="red" style="padding: 10px 0;"></span>
				</div>
				<h2>Artículos e importe total de la factura:</h2>
				<table class="naked">
					<tbody>
					<?php
					$total = 0;
					$id_articulos = array ();
					$id_transacciones = array ();
					foreach ( $articulos as $articulo ) {
						if ($articulo->tipo != "Cantidad") {
							$id_articulos [] = $articulo->id;
						} else {
							$id_transacciones [] = $articulo->transaccion;
						}
						$precio = $articulo->precio;
						?><tr>
							<td class="wp80"><?php
						if ($articulo->tipo == "Cantidad") {
							print "$articulo->cantidad x ";
						} else {
							print "1 x ";
						}
						?><a
								href="product/<?=$articulo->id."-".normalizarTexto($articulo->titulo);?>"
								target="_blank" title="<?=traducir("ver artículo")?>"><?=$articulo->titulo?></a></td>
							<td class="t-r"><?=formato_moneda($precio)." €"?></td>
						</tr><?php
						$total += $precio;
					}
					?>
						<tr>
							<td class="tar">Subtotal:</td>
							<td class="t-r"><?=formato_moneda($total)." €"?></td>
						</tr>
						<tr>
							<td class="tar">Gastos de envío:</td>
							<td class="t-r"><span id="gastos_envio">+<?=formato_moneda($gastos)?></span>
								€</td>
						</tr>
						<tr>
							<td class="tar">TOTAL:</td>
							<td class="t-r"><strong id="total"><?=formato_moneda($total+$gastos)?></strong>
								<strong>€</strong></td>
						</tr>
					</tbody>
				</table>
			</div>
			<!--wrap-->
			<footer>
				<p class="actions">
				<?php
				if ($paquete) {
					?><input type="hidden" name="paquete" value="<?=$paquete->id?>" /><?php
				}
				?>
					<input type="hidden" name="total" value="<?=$total?>" /> <input
						type="hidden" name="articulos"
						value="<?=implode ( ",", $id_articulos )?>" /> <input
						type="hidden" name="transacciones"
						value="<?=implode ( ",", $id_transacciones )?>" /> <input
						type="hidden" name="gastos_envio" value="<?=$gastos?>" /> <input
						type="submit" class="bt"
						value="<?=($paquete?traducir("Editar gastos de envío"):traducir("Añadir gastos de envío"))?>" />
					<span class="mhm">o</span> <a class="nyroModalClose">cancelar</a>
				</p>
			</footer>
		</form>
	</div>
</div>
<script type="text/javascript">
	$(function() {
 	 	$('.nmodal').nyroModal();
 	 	$("#formGastos").on("submit",function(){var x=formItemSubmit.call(this);if(x){enviarGastosEnvio.call(this);}return false;});
 	 	reloadValidations();
	});
	var totalPrecio=<?=$total?>;
</script>