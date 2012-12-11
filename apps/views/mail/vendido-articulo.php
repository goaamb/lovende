<?php
$this->load->view ( "mail/mail-cabecera" );
$cantidad = 1;
if (isset ( $transaccion ) && $transaccion) {
	$cantidad = $transaccion->cantidad;
}
?>
<h1
	style="color: #333333; font-size: 28px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 20px">Ha
	vendido un artículo</h1>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	Ha vendido correctamente el artículo:</p>
<ul style="margin: 0 0 15px">
	<li><?=$cantidad?> x <a href="<?=$url?>"
		style="text-decoration: none; color: #035f8d;"><?=$titulo?></a></li>
</ul>
<p>
	Ahora debe especificar los gastos de envío en su listado de <a
		href="<?=base_url()."store/{$usuario->seudonimo}/sell/1/detail";?>"
		style="text-decoration: none; color: #035f8d;">artículos vendidos</a>
</p>
<?php
$this->load->view ( "mail/mail-pie" );
?>