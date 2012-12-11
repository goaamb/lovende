<?php
$this->load->view ( "mail/mail-cabecera" );
?>
<div
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif;">
	<h1 style="margin: 0 0 20px">Disputa <?=$disputa?> por impago.</h1>
	<p style="margin: 0 0 15px">
		Se ha cerrado la disputa <?=$disputa?> por impago de los siguientes artículos que te compro <a
			href="<?=base_url()?>store/<?=$comprador->seudonimo?>"
			title="Ir a la tienda de <?=$comprador->seudonimo?>"><?=$comprador->seudonimo?></a>:
	</p>
	<ul style="margin: 0 0 15px">
		<?php
		foreach ( $articulos as $a ) {
			?><li><?=$a["cantidad"]?> x <a
			href="<?=base_url()?>product/<?=$a["id"]."-".normalizarTexto($a["titulo"])?>"
			title="<?=$a["titulo"]?>"><?=$a["titulo"]?></a></li><?php
		}
		?>
	</ul>
	<p style="margin: 0 0 15px">Tus artículos se han puesto a la venta de
		nuevo automáticamente y Lovende no te cobrará comisiones por esta
		transacción.</p>
	<p style="margin: 0 0 15px">Gracias por utilizar Lovende.</p>
</div>
<?php
$this->load->view ( "mail/mail-pie" );
?>