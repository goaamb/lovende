<?php
if (isset ( $ofertas ) && count ( $ofertas ) > 0) {
	?><div id="popUp">
	<div class="formA">
		<header>
			<h1>Finalizar anuncio</h1>
			<p>
				Anuncio: <a class="nyroModalClose"><?=$articulo->titulo;?></a>
			</p>
		</header>
		<div class="wrap">
			<div class="line">
				ATENCIÓN: Si finalizas este anuncio no se venderá aunque tenga pujas
				y se almacenará en tu listado de no vendidos.<br /> <br /> ¿Seguro que
				quieres finalizar el anuncio?
			</div>
		</div>
		<!--wrap-->
		<footer>
			<p class="actions">
				<a href="product/end/<?=$articulo->id?>" class="bt"
					title="Finalizar el anuncio">Finalizar el anuncio</a> <span
					class="mhm">o</span> <a class="nyroModalClose">cancelar</a>
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