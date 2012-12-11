<div id="popUp">
	<div class="formA">
		<header>
			<h1>Cantidad modificada</h1><?php
			if (isset ( $articulo ) && $articulo) {
				?><p>
				Desde <a
					href="product/<?=$articulo->id."-".normalizarTexto($articulo->titulo)?>"
					title="<?=$articulo->titulo?>"><?=$articulo->titulo?></a>
			</p>
				<?php
			}
			?></header>
		<div class="wrap">
			<div class="line">
				<p>La cantidad fue modificada correctamente.</p>
			</div>
		</div>
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