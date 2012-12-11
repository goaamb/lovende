<?php
$this->load->view ( "mail/mail-cabecera" );
?>
<div
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif;">
	<h1 style="margin: 0 0 20px">Disputa <?=$disputa?> por artículo no recibido.</h1>
	<p style="margin: 0 0 15px">
		Se ha cerrado la disputa <?=$disputa?> por artículo no recibido de los siguientes artículos que te compro <a
			href="<?=base_url()?>store/<?=$comprador->seudonimo?>"
			title="Ir a la tienda de <?=$comprador->seudonimo?>"><?=$comprador->seudonimo?></a>:
	</p>
	<ul style="margin: 0 0 15px">
		<?php
		foreach ( $articulos as $a ) {
			?><li>
			<?=$a["cantidad"]?> x 
			<a
			href="<?=base_url()?>product/<?=$a["id"]."-".normalizarTexto($a["titulo"])?>"
			title="<?=$a["titulo"]?>"><?=$a["titulo"]?></a></li><?php
		}
		?>
	</ul>
	<p style="margin: 0 0 15px">
		Los datos de contacto de <a
			href="<?=base_url()?>store/<?=$comprador->seudonimo?>"
			title="Ir a la tienda de <?=$comprador->seudonimo?>"><?=$comprador->seudonimo?></a>
		son:
	</p>
	<p>
		<strong><?=$comprador->nombre?></strong><br />
		<?=$comprador->dni?><br />
		<?=$comprador->direccion?><br />
		<?=$comprador->telefono?><br />
		<?=$comprador->ciudad->nombre.", ". $comprador->pais->nombre?><br />
	</p>
	<p style="margin: 0 0 15px">Gracias por utilizar Lovende.</p>

</div>
<?php
$this->load->view ( "mail/mail-pie" );
?>