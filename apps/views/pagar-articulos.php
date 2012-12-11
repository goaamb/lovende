<?php
$gastos = ($paquete ? $paquete->gastos_envio : 10);
$pagos = array ();
if (count ( $articulos ) > 0) {
	$pagos = explode ( ",", $articulos [0]->pagos );
}
?><div id="popUp">
	<div class="formA">
		<form action="" method="post">
			<header>
				<h1>Pagar artículos y envío</h1>
				<p>
					Vendedor <a href="store/<?=$vendedor->seudonimo?>"
						title="Ver perfil de <?=$vendedor->seudonimo?>"><strong><?=$vendedor->seudonimo?></strong></a>
					<a href="home/modal/votos" class="nmodal green">+<?=$vendedor->positivo?></a> 
					<?php if($vendedor->negativo>0){?>
					<a href="home/modal/votos" class="nmodal red">-<?=$vendedor->negativo?></a> <?php }?><span
						class="dark-grey">|</span> <a
						href="home/modal/enviar-mensaje-privado/mensaje/<?=$vendedor->id?>"
						class="nmodal" title="Enviar mensaje privado">enviar mensaje
						privado</a>
				</p>
			</header>
			<div class="wrap">
				<h2>Dirección de envío:</h2>
				<p><?=$comprador->ciudad->nombre?><br /><?=$comprador->direccion?><br />
					<?=$comprador->pais->nombre?>
				</p>
				<h2 class="mtl">Artículos e importe total de la factura:</h2>
				<table class="naked mbl">
					<?php
					$total = 0;
					$id_articulos = array ();
					foreach ( $articulos as $articulo ) {
						$id_articulos [] = $articulo->id;
						$precio = $articulo->precio;
						?><tr>
						<td><a
							href="product/<?=$articulo->id."-".normalizarTexto($articulo->titulo);?>"
							target="_blank" title="<?=traducir("ver artículo")?>"><?=$articulo->titulo?></a></td>
						<td class="t-r"><?=formato_moneda($precio)." €"?></td>
					</tr><?php
						$total += $precio;
					}
					?>
						<tr>
						<td>Subtotal:</td>
						<td class="t-r"><?=formato_moneda($total)." €"?></td>
					</tr>
					<tr>
						<td>Gastos de envío:</td>
						<td class="t-r"><span id="gastos_envio">+<?=formato_moneda($gastos)?></span>
							€</td>
					</tr>
					<tr>
						<td>TOTAL:</td>
						<td class="t-r"><strong id="total"><?=formato_moneda($total+$gastos)?></strong>
							<strong>€</strong></td>
					</tr>
					</tbody>
				</table>
				<h2>Selecciona la forma de pago:</h2>
				<div class="mbl">
				<?php
				$paypal = false;
				if (count ( $pagos ) > 0) {
					foreach ( $pagos as $p ) {
						switch ($p) {
							case "1" :
								?><p>
						<input type="radio" name="forma-pago" value="1"
							onclick="cambiarFormaPago.call(this);" /> Otros (ver descripción)
					</p><?php
								break;
							case "2" :
								?><p>
						<input type="radio" name="forma-pago" value="2"
							onclick="cambiarFormaPago.call(this);" />Pago contra reembolso
					</p><?php
								break;
							case "3" :
								?><p>
						<input type="radio" name="forma-pago" value="3"
							onclick="cambiarFormaPago.call(this);" /> Transferencia bancaria
					</p><?php
								break;
							default :
								$paypal = true;
								?><p>
						<input type="radio" name="forma-pago" value="4"
							onclick="cambiarFormaPago.call(this);" /> <img
							src="assets/images/html/logo-paypal.png" alt="Paypal" class="v-m" />
					</p><?php
								break;
						}
					}
				}
				?>
				</div>
				<p>ATENCIÓN: Vas a seleccionar la forma de pago y esta acción no
					puede deshacerse; ¿estas seguro?</p>
			</div>
		</form>
		<footer>
			<form id="formNormalPago" method="post" style="display: none;"
				onsubmit="return enviarPago.call(this);">
				<p class="actions">
					<input name="formaPago" type="hidden" /> <input
						value="<?=$paquete->id?>" name="paquete" type="hidden" /> <input
						type="submit" class="bt" value="Marcar como pagado" /> <span
						class="mhm">o</span> <a class="nyroModalClose">cancelar</a>
				</p>
			</form>
		<?php
		if ($paypal) {
print "antes";
			$paykey = $this->paypal->getPayKey ( $paquete->id, $total + $gastos );
			print "despues";
			$this->paypal->formLight ( $paykey, "apdg" );
		}
		?>
		</footer>
	</div>
</div>
<script type="text/javascript">
	$(function() {
 	 	$('.nmodal').nyroModal();
	});
</script>