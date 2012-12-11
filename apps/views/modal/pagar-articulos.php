<?php
$gastos = ($paquete ? $paquete->gastos_envio : 10);
$pagos = array ();
if (count ( $articulos ) > 0) {
	$pagos = explode ( ",", $articulos [0]->pagos );
}
if ($paquete->estado == "Sin Pago" || $paquete->estado == "Disputa") {
	$max = max ( $pagos );
	?><div id="popUp">
	<div class="formA">
		<form action="" method="post">
			<header>
				<h1>Pagar artículos y envío</h1>
				<p>
					Vendedor <a href="store/<?=$vendedor->seudonimo?>"
						<?php if($vendedor->estado=="Baneado"){print "class='baneado'";}?>
						title="Ver perfil de <?=$vendedor->seudonimo?>"><strong><?=$vendedor->seudonimo?></strong></a>
					<a href="home/modal/votos/votos/<?=$vendedor->id?>"
						class="nmodal green">+<?=$vendedor->positivo?></a> 
					<?php if($vendedor->negativo>0){?>
					<a href="home/modal/votos/votos/<?=$vendedor->id?>"
						class="nmodal red">-<?=$vendedor->negativo?></a> <?php }?><span
						class="dark-grey">|</span> <a
						href="home/modal/enviar-mensaje-privado/mensaje/<?=$vendedor->id?>"
						class="nmodal" title="Enviar mensaje privado">Enviar mensaje
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
							onclick="cambiarFormaPago.call(this);"
							<?=($max=="1"?"checked='checked'":"")?> /> Otros (ver
						descripción)
					</p><?php
					break;
				case "2" :
					?><p>
						<input type="radio" name="forma-pago" value="2"
							onclick="cambiarFormaPago.call(this);"
							<?=($max=="2"?"checked='checked'":"")?> /> Pagaré contra
						reembolso
					</p><?php
					break;
				case "3" :
					?><p>
						<input type="radio" name="forma-pago" value="3"
							onclick="cambiarFormaPago.call(this);"
							<?=($max=="3"?"checked='checked'":"")?> /> Ya he pagado por
						transferencia bancaria
					</p><?php
					break;
				default :
					$paypal = true;
					?><p>
						<input type="radio" name="forma-pago" value="4"
							<?=($max=="4"?"checked='checked'":"")?>
							onclick="cambiarFormaPago.call(this);" /> <img
							src="assets/images/html/logo-paypal.png" alt="Paypal" class="v-m" />
					</p><?php
					break;
			}
		}
	}
	?>
				</div>
			</div>
		</form>
		<div class="wrap" style="padding-bottom: 0px; overflow: auto;">
			<p id="parrafo1" <?=($max!=="1"?'style="display: none;"':"")?>
				class="fparrafo">
				<strong>ATENCIÓN:</strong> ¿Confirmas que ya has realizado el pago? Esta acción no puede deshacerse.
			</p>
			<p id="parrafo2" <?=($max!=="2"?'style="display: none;"':"")?>
				class="fparrafo">
				<strong>ATENCIÓN:</strong> ¿Confirmas que pagarás esta factura
				contra reembolso? Esta acción no puede deshacerse.
			</p>
			<p id="parrafo3" <?=($max!=="3"?'style="display: none;"':"")?>
				class="fparrafo">
				<strong>ATENCIÓN:</strong> ¿Confirmas que ya has realizado el pago
				por transferencia? Esta acción no puede deshacerse.
			</p>
		</div>
		<footer id="footerPago">
			<form id="formNormalPago" method="post"
				<?=($max=="4"?"style='display:none';":"")?>
				onsubmit="return enviarPago.call(this);">
				<p class="actions">
					<input name="formaPago" type="hidden" /> <input
						value="<?=$paquete->id?>" name="paquete" type="hidden" /> <input
						name="boton1" type="submit" class="bt" style="display: none;"
						value="<?=traducir("Sí, ya he realizado el pago")?>" /> <input
						name="boton2" type="submit" class="bt" style="display: none;"
						value="<?=traducir("Sí, pagaré contra reembolso")?>" /> <input
						name="boton3" type="submit" class="bt" style="display: none;"
						value="<?=traducir("Sí, ya he realizado el pago por transferencia")?>" />
					<span class="mhm">o</span> <a class="nyroModalClose">cancelar</a>
				</p>
			</form>
		<?php
	if ($paypal) {
		$paykey = $this->paypal->getPayKey ( $paquete->id, $total + $gastos );
		if (intval ( $paykey ) >= 0 && $paykey !== false) {
			$this->paypal->formLight ( $paykey, "apdg" );
		} elseif ($paykey == - 2) {
			?><form id="formPaypalPago" class="error">El vendedor no ha añadido su cuenta de Paypal, </span> <a
						href="home/modal/enviar-mensaje-privado/mensaje/<?=$vendedor->id?>"
						class="nmodal" title="Enviar mensaje privado">envíale un mensaje privado.</a></form><?php
		} elseif ($paykey == - 3) {
			?><form id="formPaypalPago" class="error">No tienes crédito suficiente
				en tu cuenta de Paypal</form><?php
		} else {
			?><form id="formPaypalPago" class="error">
				Debes tener en tu <a href="edit/buy-sell">configuración de
					compra-venta</a> un email de Paypal.
			</form><?php
		}
	}
	?>
		</footer>
	</div>
</div>
<script type="text/javascript">
	$(function() {
 	 	$('.nmodal').nyroModal();
	});
</script><?php
} else {
	?><script type="text/javascript">
	location.href=location.href.split("#").shift();
</script><?php
}
?>