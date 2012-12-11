<?php
$this->load->view ( "mail/mail-cabecera" );
?><h1
	style="color: #333333; font-size: 28px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 20px">Anuncio reactivado</h1>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	Nos complace comunicarle que su anuncio <a href="<?php echo base_url();?>product/<?=$idarticulo; ?>">
	<?=$articulo;?></a> ha sido reactivado. Sentimos las molestias que esto haya podido ocasionarle.</p>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	Gracias por utilizar Lovende.</p>	

<?php
	$this->load->view ( "mail/mail-pie" );
	?>