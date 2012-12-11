<?php
$this->load->view ( "mail/mail-cabecera" );
?>
<h1
	style="color: #333333; font-size: 28px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 20px">
	Disputa <?=$idreporte?> por artículo diferente a su descripción.</h1>


<p style="margin: 0 0 15px">
Se ha abierto la disputa <?=$idreporte?> por artículo diferente a su descripción de los siguientes artículos que compraste a  
		<a href="<?=base_url()?>store/<?=$vendedor->seudonimo?>"
		title="Ir a la tienda de <?=$vendedor->seudonimo?>"><?=$vendedor->seudonimo?></a>:
</p>

<ul style="margin: 0 0 15px">
	<?php 
	
		foreach ($articulo as $row)
		{ ?>
			
			<li><?=$row["cantidad"]?> x <a
		href="<?=base_url()?>product/<?=$row["id"]."-".normalizarTexto($row["titulo"])?>"
		title="<?=$row["titulo"]?>"><?=$row["titulo"]?>
			</a>
	
	<li>			
		<?php 
		}
	
	?>


</ul>

<p style="margin: 0 0 15px">
Disponéis de un plazo de <?=$this->configuracion->variables('denuncia4c');?> días a partir de hoy para contactar por mensaje privado, llegar a un entente y marcar el artículo como recibido y conforme en tus <a
		href="<?=base_url()?>store/<?=$comprador->seudonimo?>/sell"
		title="Ir a la tienda de <?=$comprador->seudonimo?>">ventas</a>.
</p>

<p style="margin: 0 0 15px">Si no llegáis a un entente, la transacción
	finalizará y facilitaremos a ambos vuestra información de contacto por
	si debéis realizar reclamaciones legales.</p>

<p style="margin: 0 0 15px">
<p style="margin: 0 0 15px">La acumulación de disputas por artículo no
	recibido puede ser motivo de suspensión de tu cuenta en Lovende.</p>

<?php
$this->load->view ( "mail/mail-pie" );
?>