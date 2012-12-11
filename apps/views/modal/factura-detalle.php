<?php
if (! isset ( $factura ) || (isset ( $factura ) && ! $factura)) {
	redirect ( "/", "refresh" );
}
$cuentas = isset ( $cuentas ) && $cuentas && is_array ( $cuentas ) ? $cuentas : array ();
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<base href="<?=base_url();?>" />
<title>Factura: <?=$factura->codigo?></title>
<style>
* {
	margin: 0;
	padding: 0
}

body {
	font: 15px Arial, Helvetica, sans-serif;
}

td,th {
	vertical-align: top;
	padding: 10px 10px
}

a {
	color: #035f8d;
	text-decoration: none;
}

.odd td {
	background: #e5e5e5;
}

tfoot td {
	background: #e5e5e5;
	padding: 5px 10px;
}
</style>
</head>
<body>
	<table cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td>
				<h1 style="font-size: 15px; margin: 0;">Factura con fecha <?=date("d-m-Y",strtotime($factura->fecha))?></h1>
			</td>
			<td align="right">
				<p>Número de factura: <?=$factura->codigo?></p>
			</td>
		</tr>
		<tr>
			<td bgcolor="#e5e5e5">
				<p>
					Seudónimo: <strong><?=$usuario->seudonimo?></strong><br />
					<?=$usuario->nombre." ".$usuario->apellido?><br />
					<?=$usuario->direccion?>, <?=$usuario->pais->nombre?>
				</p>
			</td>
			<td bgcolor="#e5e5e5" align="right">
				<p>
					<strong>Lovende</strong><br /> NIF: 43539353-P<br /> C/ Tramuntana
					29, 08030 Barcelona, Spain
				</p>
			</td>
		</tr>
		<!--conceptos factura-->
		<tr>
			<td colspan="2" valign="top" style="padding: 5px 0;">
				<table cellspacing="0" cellpadding="0" width="100%">
					<thead>
						<tr>
							<th align="left">Concepto / Anuncio</th>
							<th align="right">Fecha</th>
							<th align="right">Importe venta</th>
							<th align="right">Importe tarifa</th>
						</tr>
					</thead>
					<tbody>
				<?php
				foreach ( $cuentas as $i => $c ) {
					?><tr class="<?=($i%2==0?"odd":"");?>">
							<td><?php
					$cantidad = 1;
					$transaccion = false;
					if ($c->cantidad > 0) {
						print "{$c->cantidad} x ";
						$rt = $this->db->where ( array (
								"articulo" => $c->articulo,
								"paquete" => $c->paquete 
						) )->get ( "transaccion", 1, 0 )->result ();
						if ($rt && is_array ( $rt ) && count ( $rt ) > 0) {
							$transaccion = $rt [0]->id;
						}
						$cantidad = $c->cantidad;
					} else {
						print "1 x ";
					}
					?><a
								href="<?="product/$c->articulo-".normalizarTexto($c->titulo).($transaccion?"/$transaccion":"")?>"
								target="_blank" title="Ver ficha del producto"><?=$c->titulo?></a></td>
							<td align="right"><?=date("d-m-Y",strtotime($c->fecha))?></td>
							<td align="right"><?=formato_moneda($c->precio*$cantidad)?> &euro;</td>
							<td align="right"><?=formato_moneda($c->monto)?> &euro;</td>
						</tr><?php
				}
				$i = 0;
				if ($usuario->tipo_tarifa == "Plana") {
					$rz = $this->db->where ( array (
							"monto" => $factura->monto_tarifa,
							"tipo_tarifa" => "Plana" 
					) )->get ( "tarifa", 1, 0 )->result ();
					$tarifa = "";
					if ($rz && is_array ( $rz ) && count ( $rz ) > 0) {
						$tarifa = $rz [0]->nombre;
					}
					$i ++;
					?><tr class="<?=(count($cuentas)%2==0?"odd":"");?>">
							<td><?=traducir("Tarifa plana")." ".$tarifa.", ".traducir("stock actual: ").formato_moneda($factura->monto_stock)?> &euro;</td>
							<td align="right"><?=date("d-m-Y",strtotime($factura->fecha))?></td>
							<td align="right"><?=formato_moneda(0)?> &euro;</td>
							<td align="right"><?=formato_moneda($factura->monto_tarifa)?> &euro;</td>
						</tr><?php
				}
				?><tr class="<?=((count($cuentas)+$i)%2==0?"odd":"");?>">
							<td><?=traducir("Totales")?></td>
							<td align="right"></td>
							<td align="right"><?=formato_moneda($factura->monto_venta)?> &euro;</td>
							<td align="right"><?=formato_moneda($factura->monto_tarifa)?> &euro;</td>
						</tr><?php
						
						?></tbody>
					<tfoot>
						<tr>
							<td rowspan="3" colspan="2" style="vertical-align: bottom">
								<p style="">
									<strong>Gracias por hacer negocios con Lovende.</strong><br />
									Los servicios descritos en esta factura corresponden a tu
									cuenta de www.lovende.es.
								</p>
							</td>
							<td align="right">IMPONIBLE:</td>
							<td align="right"><?=formato_moneda($factura->monto_tarifa)?> &euro;</td>
						</tr>
						<tr>
							<td align="right">I.V.A. 21%:</td>
							<td align="right"><?=formato_moneda($factura->iva)?> &euro;</td>
						</tr>
						<tr>
							<td align="right"><strong>TOTAL:</strong></td>
							<td align="right"><?=formato_moneda($factura->iva+$factura->monto_tarifa)?> &euro;</td>
						</tr>
					</tfoot>
				</table>
			</td>
		</tr>
		<!--fin conceptos factura-->
	</table>
</body>
</html>