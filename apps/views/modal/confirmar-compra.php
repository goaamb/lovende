<?php
if (isset ( $articulo ) && count ( $articulo ) > 0) {
	?><div id="popUp">
	<div class="formA">
		<header>
			<h1>Confirmación de compra</h1>
			<p>
				Anuncio: <a class="nyroModalClose"><?=$articulo->titulo;?></a>
			</p>
		</header>
		<div class="wrap">
			<div class="line">
				Al pulsar el botón <strong>Comprar</strong> te comprometes a comprar
				<?php
	if (! isset ( $extra )) {
		?>este artículo al vendedor por <?php
		print formato_moneda ( $articulo->precio );
	} else {
		if ($extra > $articulo->cantidad) {
			$extra = $articulo->cantidad;
		}
		print $extra;
		?> unidades de este articulo por <?php print formato_moneda($articulo->precio*$extra);}?> &euro;
			</div>
		</div>
		<!--wrap-->
		<footer>
			<p class="actions">
				<a href="#" class="bt" title="Comprar el artículo"
					onclick="$('#formComprar').submit();return false;">Comprar</a> <span
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