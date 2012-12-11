<?php
if (isset ( $articulo )) {
	?><div id="popUp">
	<div class="formA">
		<header>
			<h1>Envío no disponible</h1>
			<p>
				Anuncio: <a class="nyroModalClose"><?=$articulo->titulo;?></a>
			</p>
		</header>
		<div class="wrap">
			<div class="line">
				Lo sentimos, el vendedor no realiza envíos a su país, puede
				contactar con él enviándole un <a
					href="home/modal/enviar-mensaje-privado/mensaje/<?=$articulo->usuario?>"
					class="nmodal">mensaje privado</a>.
			</div>
		</div>
		<!--wrap-->
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
</script><?php
} else {
	?><script>location.href=location.href;</script><?php
}
?>