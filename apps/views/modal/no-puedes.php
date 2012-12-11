<div id="popUp">
	<div class="formA">
		<header>
			<h1>¡No puedes!</h1>
			<p>
				Anuncio: <a class="nyroModalClose"><?=$articulo->titulo;?></a>
			</p>
		</header>
		<div class="wrap">
			<div class="line">
				No puedes comprar un artículo tuyo, mejor cómprate algo nuevo.
			</div>
		</div>
		<footer>
			<p class="actions">
				<a class="bt nyroModalClose">Cerrar</a>
			</p>
		</footer>
	</div>
</div>

<script type="text/javascript">
	$(function() {
 	 	$('.nmodal').nyroModal();
	});
</script>