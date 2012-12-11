<?php
$this->load->view ( "mail/mail-cabecera" );
?>
<h1
	style="color: #333333; font-size: 28px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 20px">Su artículo ha finalizado sin pujas</h1>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	Su artículo finalizó sin pujas:<a href="<?=$url?>"
		style="text-decoration: none; color: #035f8d;"><?=$titulo?></a>.
</p>
<p>
	Puede volver a ponerlo en venta automáticamente desde su listado de <a
   href="<?=base_url()."store/{$seudonimo}/sell/3/detail";?>"
   style="text-decoration: none; color: #035f8d;">artículos finalizados</a>
</p>

<?php
$this->load->view ( "mail/mail-pie" );
?>