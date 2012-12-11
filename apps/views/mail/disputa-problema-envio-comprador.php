<?php
$this->load->view ( "mail/mail-cabecera" );
?>
<h1
	style="color: #333333; font-size: 28px; font-family: Arial, Helvetica, sans-serif; margin: 0 0 20px">
	Disputa <?=$idreporte?> por problema en el envío.</h1>


<p style="margin: 0 0 15px">
Se ha abierto la disputa <?=$idreporte?> por problema en el envío de los siguientes artículos que compraste a 
		<a href="<?=base_url()?>store/<?=$vendedor->seudonimo?>"
			title="Ir a la tienda de <?=$vendedor->seudonimo?>"><?=$vendedor->seudonimo?></a>:
</p>


<p>
	<?php 
	
		foreach ($articulo as $row)
		{ ?>
			<?=$row->cantidad?> x <a
				href="<?=base_url()?>product/<?=$row->id."-".normalizarTexto($row->titulo)?>"
				title="<?=$row->titulo?>"><?=$row->titulo?>
			</a>			
		<?php 
		}
	
	?>
</p>

<p style="margin: 0 0 15px">
Disponéis de un plazo de 15 días a partir de hoy para contactar por mensaje privado, llegar a un entente y  que  <a href="<?=base_url()?>store/<?=$vendedor->seudonimo?>/sell"
			title="Ir a la tienda de <?=$vendedor->seudonimo?>"><?=$vendedor->seudonimo?></a> marque el artículo como enviado.
</p>

<p style="margin: 0 0 15px">Si no llegáis a un entente, la transacción finalizará y facilitaremos a ambos vuestra información de contacto por si debéis realizar reclamaciones legales.</p>
 
<p style="margin: 0 0 15px">La acumulación de disputas por problema en el envío puede ser motivo de suspensión de tu cuenta en Lovende.</p>

<?php
$this->load->view ( "mail/mail-pie" );
?>