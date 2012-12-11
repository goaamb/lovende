<div class="wrapper clearfix">
	<?php
	$this->load->view ( "usuario/cabecera-perfil", array (
			"seccion" => "perfil" 
	) );
	function paypalButton($titulo, $precio, $codigo, $id, $usuario) {
		$CI = &get_instance ();
		$baseURL = base_url ();
		?><form
		action="<?=$CI->configuracion->variables("urlBase");?>cgi-bin/webscr"
		method="post">
		<input type="hidden" name="cmd" value="_xclick"> <input type="hidden"
			name="business" value="W8HCWU4RVBCK4"> <input type="hidden" name="lc"
			value="ES"> <input type="hidden" name="item_name"
			value="<?=$titulo?>"> <input type="hidden" name="item_number"
			value="<?=$codigo?>"> <input type="hidden" name="amount"
			value="<?=$precio?>"> <input type="hidden" name="currency_code"
			value="EUR"> <input type="hidden" name="button_subtype"
			value="services"> <input type="hidden" name="no_note" value="1"> <input
			type="hidden" name="no_shipping" value="1"> <input type="hidden"
			name="rm" value="1"> <input type="hidden" name="return"
			value="<?=base_url()."store/$usuario->seudonimo/billing"?>"> <input
			type="hidden" name="cancel_return"
			value="<?=base_url()."store/$usuario->seudonimo/billing"?>"> <input
			type="hidden" name="bn"
			value="PP-BuyNowBF:btn_buynow_SM.gif:NonHosted"> <input type="hidden"
			name="notify_url" value="<?=$baseURL."paypal/billingPay/$id"?>"> <input
			type="submit" border="0" name="submit" class="bt"
			alt="PayPal - The safer, easier way to pay online!"
			value="<?=traducir("Pagar")?>" />
	</form>
		<?php
	}
	?>
	<header class="cont-cab">
		<h1>Mis cuentas</h1>
		<p>
		<?php
		if (! isset ( $facturas ) || (isset ( $facturas ) && is_array ( $facturas ) && count ( $facturas ) == 0)) {
			?>Aquí podrás consultar el importe de tus ventas y las facturas de Lovende.<?php
		} else {
			?><a href="home/modal/cambiar-tarifas" class="nmodal">Cambiar tipo de
				tarifas</a> | <?=$total?> artículos, mostrando del <strong>1</strong>
			al <strong><?=count($facturas)?></strong><?php
		}
		?>
		</p>
	</header>
	<?php
	if (! isset ( $facturas ) || (isset ( $facturas ) && is_array ( $facturas ) && count ( $facturas ) > 0)) {
		?><table class="no-border">
		<thead>
			<tr>
				<th><strong>Periodo</strong></th>
				<th class="t-r"><strong>Importe Stock</strong></th>
				<th class="t-r"><strong>Importe ventas</strong></th>
				<th class="t-r"><strong>Tarifas Lovende</strong></th>
				<th class="t-r"><strong>Factura detalle</strong></th>
				<th class="t-r"><strong>Estado</strong></th>
			</tr>
		</thead>
		<tbody>
		<?php
		$meses = array (
				"Enero",
				"Febrero",
				"Marzo",
				"Abril",
				"Mayo",
				"Junio",
				"Julio",
				"Agosto",
				"Septiembre",
				"Octubre",
				"Nobiembre",
				"Diciembre" 
		);
		
		foreach ( $facturas as $i => $f ) {
			list ( $mes, $anio ) = explode ( "-", $f->mes );
			$tmes = $meses [intval ( $mes ) - 1];
			/*
			 * if (isset ( $f->estado ) && $f->estado == "Pendiente") { $paykey
			 * = $this->paypal->getPayKeyBilling ( $f->id ); }
			 */
			?><tr class="<?=$i%2==0?"bg-grey":""?>">
				<td><?=$tmes?> <?=$anio?></td>
				<td class="t-r"><?=formato_moneda($f->monto_stock); ?> &euro;</td>
				<td class="t-r"><?=formato_moneda($f->monto_venta); ?> &euro;</td>
				<td class="t-r"><?=formato_moneda($f->monto_tarifa+$f->iva)." ( incl. IVA )" ?> &euro;</td>
				<td class="t-r"><a
					href="home/modal/factura-detalle/facturaDetalle/<?=isset($f->id)?$f->id:"x"?>"
					title="Ver detalle de la factura" target="blank"><?=$f->codigo?></a></td>
				<td class="t-r"><?=isset($f->estado)?($f->estado=="Pendiente"?(/*$this->paypal->formLightBilling ( $paykey, "apdg" )*/paypalButton(traducir("Pago por factura: $f->codigo de $tmes/$anio en Lovende.com"), $f->monto_tarifa+$f->iva, $f->codigo, $f->id, $this->myuser)):($f->estado=="Pagado"?traducir("Pagado"):traducir("Pagando"))):traducir("En curso")?></td>
			</tr><?php
		}
		?>
		</tbody>
	</table>
	<?php
		if (count ( $facturas ) < $total) {
			?><div class="ver-mas">
		<a title="cancelar e ir a lorem ipsum" href="#">Ver más</a>
	</div><?php
		}
	}
	?>
</div>