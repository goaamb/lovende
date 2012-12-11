<?php
$this->load->view ( "mail/mail-cabecera" );
?><h1
	style="color: #333333; font-size: 28px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 20px">Cuenta de usuario suspendida.</h1>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	Lo sentimos, pero tu cuenta en Lovende ha sido suspendida por Violación de los términos de uso:</p>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	Motivo: <?=$motivo;?></p>
<?php 
if($anuncio != false)
{?>
	<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	Anuncio: <a href="<?php echo base_url();?>product/<?php echo $idarticulo;?>"><?=$anuncio;?></a></p>
<?php }
if($descripcion != false)
{?>
	<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	Descripcion: <?=$descripcion;?></p>
<?php } ?>
<p
	style="color: #333333; font-size: 15px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 15px">
	Gracias por utilizar Lovende.</p>	

<?php
	$this->load->view ( "mail/mail-pie" );
	?>