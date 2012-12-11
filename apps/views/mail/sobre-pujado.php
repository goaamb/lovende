<?php
$this->load->view ( "mail/mail-cabecera" );
?>
<h1
	style="color: #333333; font-size: 28px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 20px">Le
	han sobrepujado</h1>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	Le han sobrepujado en la subasta del artículo: <a href="<?=$url?>"
		style="text-decoration: none; color: #035f8d;"><?=$titulo?></a>.<br />Debe
	aumentar su puja si quiere comprar este artículo.
</p>
<?php
$this->load->view ( "mail/mail-pie" );
?>