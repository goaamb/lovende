<div id="popUp">
	<div class="formA">
		<form class="clearfix d-b" action="post" id="formGastos">
			<header>
				<h1>Retraso en añadir los gastos de envío denunciado</h1>
				<p>
					Comprador <a href="store/<?=$comprador->seudonimo?>"
						<?php if($comprador->estado=="Baneado"){print "class='baneado'";}?>
						title="Ver perfil de <?=$comprador->seudonimo?>"><strong><?=$comprador->seudonimo?></strong></a>
					<a href="home/modal/votos/votos/<?=$comprador->id?>"
						class="nmodal green">+<?=$comprador->positivo?></a> 
					<?php if($comprador->negativo>0){?>
					<a href="home/modal/votos/votos/<?=$comprador->id?>"
						class="nmodal red">-<?=$comprador->negativo?></a> <?php }?><span
						class="dark-grey">|</span> <a
						href="home/modal/enviar-mensaje-privado/mensaje/<?=$comprador->id?>"
						class="nmodal" title="Enviar mensaje privado">enviar mensaje
						privado</a>
				</p>
			</header>
			<div class="wrap">
				<p class="justify">
					El comprador ha denunciado su retraso en los gastos de envío. <strong>Tienes un plazo de <?=$this->configuracion->variables("denuncia1b")?> días para añadirlos</strong>,
					pasados estos la transacción se finalizará automáticamente y
					recibirás un voto negativo.
				</p>
				<p class="justify">Si no llegáis a un entente, denuncia la transacción; en ese momento el vendedor tiene un plazo de <?=$this->configuracion->variables("denuncia1b")?> días para añadir los gastos, pasados estos el vendedor recibirá un voto negativo y la transacción finalizará.</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
			</div>
			<footer>
				<p class="actions">
					<a class="nyroModalClose bt">Cerrar</a>
				</p>
			</footer>
		</form>
	</div>
</div>
<script type="text/javascript">
	$(function() {
 	 	$('.nmodal').nyroModal();
	});
</script>