<?php
$this->load->view ( "mail/mail-cabecera" );
?>
<div
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif;">
	<h1 style="margin: 0 0 20px">Disputa <?=$disputa?> por artículo no recibido.</h1>
	<p style="margin: 0 0 15px">
		Se ha abierto la disputa <?=$disputa?> por artículo no recibido de los siguientes artículos que te compro <a
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
	<p style="margin: 0 0 15px">
		<a href="<?=base_url()?>store/<?=$comprador->seudonimo?>"
			title="Ir a la tienda de <?=$comprador->seudonimo?>"><?=$comprador->seudonimo?></a>
		dispone de un plazo de <?=$this->configuracion->variables('denuncia4c');?> días a partir de hoy para confirmar la recepción;
		si no lo hace la transacción finalizará, y facilitaremos a ambos vuestra información de contacto por si debéis realizar reclamaciones legales.
	</p>
	<p style="margin: 0 0 15px">
		Aconsejamos que envíes un mensaje privado a <a
			href="<?=base_url()?>store/<?=$comprador->seudonimo?>"
			title="Ir a la tienda de <?=$comprador->seudonimo?>"><?=$comprador->seudonimo?></a>
		para llegar a un entente.
	</p>
	<p style="margin: 0 0 15px">La acumulación de disputas de impago puede
		ser motivo de suspensión de tu cuenta en Lovende.</p>
</div>
<?php
$this->load->view ( "mail/mail-pie" );
?>