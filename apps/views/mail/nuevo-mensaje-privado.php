<?php
$this->load->view ( "mail/mail-cabecera" );
?>
<h1
	style="color: #333333; font-size: 28px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 20px">Nuevo
	mensaje privado</h1>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	El usuario <?php
	if ($emisor) {
		?><a href="<?=base_url();?>store/<?=$emisor->seudonimo?>"
		title="Ir a la tienda de <?=$emisor->seudonimo?>"
		style="text-decoration: none; color: #035f8d;"><?=$emisor->seudonimo?></a><?php
	} else {
		?><a href="<?=base_url();?>" title="Ir a Lovende"
		style="text-decoration: none; color: #035f8d;">Lovende</a><?php
	}
	?>
	le ha enviado un nuevo mensaje privado.
</p>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	Ir a mis <a
		href="<?=base_url()?>store/<?=$receptor->seudonimo?>/messages"
		title="Ir a mis Mensajes">Mensajes</a>.
</p>
<?php
$this->load->view ( "mail/mail-pie" );
?>