<?php
$this->load->view ( "mail/mail-cabecera" );
?>
<h1
	style="color: #333333; font-size: 28px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 20px">
	Disputa <?=$idreporte?> por problema en el envío finalizada.</h1>


<p style="margin: 0 0 15px">
Se ha cerrado la disputa <?=$idreporte?> problema en el envío de los siguientes artículos que te compró 
		<a href="<?=base_url()?>store/<?=$comprador->seudonimo?>"
			title="Ir a la tienda de <?=$comprador->seudonimo?>"><?=$comprador->seudonimo?></a>:
</p>


<p>
	<?php 
	
		foreach ($articulo as $row)
		{ ?>
			<?=$row['cantidad']?> x <a
				href="<?=base_url()?>product/<?=$row['id']."-".normalizarTexto($row['titulo'])?>"
				title="<?=$row['titulo']?>"><?=$row['titulo']?>
			</a>			
		<?php 
		}
	
	?>
</p>

<p style="margin: 0 0 15px">
Tus artículos se han puesto a la venta de nuevo automáticamente y Lovende no te cobrará comisiones por esta transacción.
</p>

<p style="margin: 0 0 15px">
Los datos de contacto de <a href="<?=base_url()?>store/<?=$comprador->seudonimo?>"
			title="Ir a la tienda de <?=$comprador->seudonimo?>"><?=$comprador->seudonimo?></a> son:
</p>
 
 <p style="margin: 0 0 15px">
	<strong><?=$comprador->nombre?></strong>
	<br />
	<?=$comprador->dni?>
	<br />
	<?=$comprador->direccion?><br />
	<?=$comprador->telefono?>
	
</p>
 
 <p style="margin: 0 0 15px"><p style="margin: 0 0 15px">Gracias por utilizar Lovende.</p>

<?php
$this->load->view ( "mail/mail-pie" );
?>