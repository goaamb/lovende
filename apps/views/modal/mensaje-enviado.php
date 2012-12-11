<div id="popUp">
	<div class="formA">
		<header>
			<h1>Mensaje enviado</h1>
			<p>
				De <a href="store/<?=$usuario->seudonimo?>"  <?php if($usuario->estado=="Baneado"){print "class='baneado'";}?>
					title="Ver perfil de <?=$usuario->seudonimo?>"><strong><?=$usuario->seudonimo?></strong></a>
				<span class="green">+<?=$usuario->positivo?></span> <?php
				if ($usuario->negativo > 0) {
					?><span class="red">-<?=$usuario->negativo?></span><?php
				}
				?>
		</p>
		</header>
		<div class="wrap">
			<div class="line">
				<p>El mensaje se ha enviado correctamente.</p>
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