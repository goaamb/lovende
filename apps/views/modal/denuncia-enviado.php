<div id="popUp">
	<div class="formA">
		<header>
			<h1>Denúncia enviada</h1>
			<p>
				De <a href="store/<?=$usuario->seudonimo?>"
					title="Ver perfil de <?=$usuario->seudonimo?>"
					<?php if($usuario->estado=="Baneado"){print "class='baneado'";}?>><strong><?=$usuario->seudonimo?></strong></a>
				<span class="green">+<?=$usuario->positivo?></span> <?php
				if ($usuario->negativo > 0) {
					?><span class="red">-<?=$usuario->negativo?></span><?php
				}
				?>
		</p>
		</header>
		<div class="wrap">
			<div class="line">
				<p>La denuncia se ha enviado correctamente, trataremos de resolverla
					lo antes posible.</p>
			</div>
		</div>
		<!--wrap-->
		<footer>
			<p class="actions">
				<a class="nyroModalClose big">Cerrar</a>
			</p>
		</footer>
	</div>
</div>

<script type="text/javascript">
	$(function() {
 	 	$('.nmodal').nyroModal();
	});
</script>