<div id="popUp">
	<div class="formA">
		<form action="" method="post">
			<header>
				<h1>Añadir nota aclarativa</h1>
				<p>
					Anuncio: <a href="#" title="Ver perfil de Kazevargas"
						class="nyroModalClose"><?=$articulo->titulo;?></a>
				</p>
			</header>
			<div class="wrap">
				<div class="line">
					<label for="">Nota:</label>
					<p>
						<textarea rows="4" cols="" class="w100" id="notaAclarativaModal"></textarea>
					</p>
				</div>
			</div>
			<footer>
				<p class="actions">
					<input type="button" class="bt" value="Enviar"
						onclick="enviarNotaAclarativa(<?=$articulo->id?>);" /> <span
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