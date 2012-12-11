<div id="popUp">
	<div class="formA">
		<form class="clearfix d-b" action="post" id="formGastos">
			<header>
				<h1>Retraso en el pago de los artículos denunciado</h1>
				<p>
					Vendedor <a href="store/<?=$vendedor->seudonimo?>"
						<?php if($vendedor->estado=="Baneado"){print "class='baneado'";}?>
						title="Ver perfil de <?=$vendedor->seudonimo?>"><strong><?=$vendedor->seudonimo?></strong></a>
					<a href="home/modal/votos/votos/<?=$vendedor->id?>"
						class="nmodal green">+<?=$vendedor->positivo?></a> 
					<?php if($vendedor->negativo>0){?>
					<a href="home/modal/votos/votos/<?=$vendedor->id?>"
						class="nmodal red">-<?=$vendedor->negativo?></a> <?php }?><span
						class="dark-grey">|</span> <a
						href="home/modal/enviar-mensaje-privado/mensaje/<?=$vendedor->id?>"
						class="nmodal" title="Enviar mensaje privado">Enviar mensaje
						privado</a>
				</p>
			</header>
			<div class="wrap">
				<p class="justify">
					El vendedor ha denunciado tu retraso en el pago de los artículos. Tienes un plazo de <?=$this->configuracion->variables("denuncia2b");?> días para pagarlos,
					si no lo haces abriremos una disputa.
				</p>
				
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