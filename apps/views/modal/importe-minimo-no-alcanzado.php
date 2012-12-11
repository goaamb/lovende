<div id="popUp">
	<div class="formA">
		<header>
			<h1>Importe mínimo no alcanzado</h1>
			<p>
				Anuncio: <a class="nyroModalClose"><?=$articulo->titulo;?></a>
			</p>
		</header>
		<div class="wrap">
			<div class="line">
				Tu <?=($articulo->tipo=="Fijo"?"oferta":"puja")?> debe ser igual o superior a <?=formato_moneda(($oferta?($articulo->tipo=="Fijo"?$oferta->monto:(($oferta->usuario==$usuario->id)?$oferta->monto+0.5:$oferta->monto_automatico+0.5)):$articulo->precio))?> &euro;.
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