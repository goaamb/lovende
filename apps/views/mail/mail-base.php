<?php
$this->load->view ( "mail/mail-cabecera" );
?><div style="font-size: 15px; font-family: Arial;"><?php
if (isset ( $mensaje )) {
	print $mensaje;
}
?></div><?php
$this->load->view ( "mail/mail-pie" );
?>