<?php
$this->load->view ( "mail/mail-cabecera" );
?><h1
	style="color: #333333; font-size: 28px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 20px">Solicitud
	de cambio de contraseña</h1>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">Se
	ha solicitado un de cambio de contraseña mediante el formulario de
	¿Olvidaste tu contraseña?; su nueva contraseña es:</p>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	<strong><?=$password;?></strong>
</p>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">Para
	activar esta nueva contraseña debe hacer click en el siguiente enlace:</p>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	<a href="<?=$link?>"><?=$link?></a>
</p>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	Esperamos que disfrute comprando y vendiendo en Lovende.</p><?php
	$this->load->view ( "mail/mail-pie" );
	?>