<?php
$this->load->view ( "mail/mail-cabecera" );
?><h1
	style="color: #333333; font-size: 28px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 20px">Cuenta de usuario reactivada.</h1>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	Nos complace comunicarle que su cuenta de usuario y su tienda han sido reactivadas. Sus datos de acceso son:</p>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	Seudónimo: <?=$seudonimo;?></p>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	Contraseña: <?=$contrasena;?></p>

<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	Gracias por utilizar Lovende.</p>	

<?php
	$this->load->view ( "mail/mail-pie" );
	?>