<?php
$this->load->view ( "mail/mail-cabecera" );
?><h1
	style="color: #333333; font-size: 28px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 20px">¡Bienvenido
	a Lovende!</h1>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	Se ha registrado correctamente en Lovende, estos son sus datos de
	acceso:</p>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	<strong>Seudónimo:</strong> <?=$usuario;?><br /> <strong>Contraseña:</strong>
	<?=$password;?>
</p>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	Esperamos que disfrute comprando y vendiendo en Lovende.</p><?php
	$this->load->view ( "mail/mail-pie" );
	?>