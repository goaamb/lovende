<?php
$this->load->view ( "mail/mail-cabecera" );
?><h1
	style="color: #333333; font-size: 28px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 20px">Cambio
	de Email</h1>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	Ha realizado un cambio de email, desde ahora su cuenta estará vinculada a
	este correo:</p>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	<strong>Email:</strong> <?=$email;?></p>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	Esperamos que disfrute comprando y vendiendo en Lovende.</p><?php
	$this->load->view ( "mail/mail-pie" );
	?>