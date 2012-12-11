<?php
$this->load->view ( "mail/mail-cabecera" );
?>
<div
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif;">
	<h1 style="font-size: 28px; margin: 0 0 20px;">Articulos que finalizan
		hoy</h1>
	<p style="margin: 0 0 15px;">Los siguientes artículos en tu lista de
		seguimientos finalizan hoy:</p>
	<ul style="margin: 0 0 15px; padding: 0px; list-style: none;"><?php
	foreach ( $articulos as $id => $t ) {
		?><li style="margin: 0px; padding: 0px;"><a
			href="<?=base_url()."product/$id-".normalizarTexto($t)?>"
			style="text-decoration: none;"><?=$t?></a></li><?php
	}
	?></ul>
	<p style="margin: 0 0 15px;">No olvides de pujar o enviar tus ofertas
		antes de que finalicen.</p>
	<p style="margin: 0 0 15px;">¡Buena suerte!</p>
</div>
<?php
$this->load->view ( "mail/mail-pie" );
?>