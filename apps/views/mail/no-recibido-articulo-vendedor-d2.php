<?php
$this->load->view ( "mail/mail-cabecera" );
?>
<div
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif;">
	<h1 style="margin: 0 0 20px">Problema con el envío de un artículo.</h1>
	<p style="margin: 0 0 15px">
		<a href="<?=base_url()?>store/<?=$comprador->seudonimo?>"
			title="Ir a la tienda de <?=$comprador->seudonimo?>"><?=$comprador->seudonimo?></a>
		ha denunciado tu retraso en el envío de los siguientes artículos que te compró:
	</p>
	<ul style="margin: 0 0 15px">
		<?php
		foreach ( $articulos as $a ) {
			?><li><?=$a->cantidad?> x <a
			href="<?=base_url()?>product/<?=$a->id."-".normalizarTexto($a->titulo)?>"
			title="<?=$a->titulo?>"><?=$a->titulo?></a></li><?php
		}
		?>
	</ul>
	<p style="margin: 0 0 15px">
		Dispones de un plazo de <?=$this->configuracion->variables('denuncia4b');?> dias a partir de hoy para marcar el artículo como enviado en 
		<a
			href="<?=base_url()."store/$vendedor->seudonimo/sell"?>">tus ventas</a> o para marcar que todavía no has recibido el pago.</p>
			
	<p style="margin: 0 0 15px">
		Aconsejamos que envíes un mensaje privado a <a
			href="<?=base_url()?>store/<?=$comprador->seudonimo?>"
			title="Ir a la tienda de <?=$comprador->seudonimo?>"><?=$comprador->seudonimo?></a>
		para llegar a un entente.
	</p>
</div>
<?php
$this->load->view ( "mail/mail-pie" );
?>