<?php
$this->load->view ( "mail/mail-cabecera" );
?>
<h1
	style="color: #333333; font-size: 28px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 20px"><?=$asunto?></h1>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	<strong>Nombre: </strong> <?=$nombre?><br /> <strong>Email: </strong> <?=$email?><br />
	<strong>Mensaje: </strong><br />
	<?=$mensaje?>
</p>
<?php
$this->load->view ( "mail/mail-pie" );
?>