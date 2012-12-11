<div id="popUp">
	<div class="formA">
		<form action="" method="post">
			<header>
				<h1>Enviar mensaje privado</h1>
				<p>
					A <a href="store/<?=$receptor->seudonimo?>"
						<?php if($receptor->estado=="Baneado"){print "class='baneado'";}?>
						title="Ver perfil de <?=$receptor->seudonimo?>"><strong><?=$receptor->seudonimo?></strong></a>
					<a href="home/modal/votos/votos/<?=$receptor->id?>"
						class="nmodal green">+<?=$receptor->positivo?></a>
					<?php if($receptor->negativo>0){?><a
						href="home/modal/votos/votos/<?=$receptor->id?>"
						class="nmodal red">-<?=$receptor->negativo?></a><?php }?>
					
				</p>
			</header>
			<div class="wrap">
				<div class="line">
					<label for="">Mensaje:</label>
					<p>
						<textarea rows="4" cols="" class="w100" name="mensaje"></textarea>
					</p>
				</div>
			</div>
			<!--wrap-->
			<footer>
				<p class="actions">
					<input type="hidden" name="receptor" value="<?=$receptor->id?>" />
					<input type="hidden" name="articulo"
						value="<?=isset($articulo)?$articulo->id:""?>" /> <input
						type="button" class="bt" value="Enviar"
						onclick="enviarMensajePrivado.call(this.form)" /> <span
						class="mhm">o</span> <a class="nyroModalClose">cancelar</a>
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