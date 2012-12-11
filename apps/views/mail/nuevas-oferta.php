<?php
$this->load->view ( "mail/mail-cabecera" );
?>
<h1
	style="color: #333333; font-size: 28px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 20px">Tiene
	nuevas ofertas</h1>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	Tiene nuevas ofertas de compra para el artículo <a href="<?=$url?>"
		style="text-decoration: none; color: #035f8d;"><?=$titulo?></a>.
</p>
<?php
$this->load->view ( "mail/mail-pie" );
?>