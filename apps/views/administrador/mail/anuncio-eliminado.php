<?php
$this->load->view ( "mail/mail-cabecera" );
?><h1
	style="color: #333333; font-size: 28px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 20px">Anuncio Eliminado</h1>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	Lo sentimos, pero por infracción de los Términos de uso tu anuncio <a href="<?php echo base_url();?>product/<?=$idarticulo; ?>">
	<?=$articulo;?></a>
	 ha sido eliminado</p>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	La acumulación de infracciones puede ser motivo de desactivación de tu cuenta en Lovende.</p>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	Gracias por utilizar Lovende.</p>	

<?php
	$this->load->view ( "mail/mail-pie" );
	?>