<div id="popUp">
	<div class="formA">
		<form action="" method="post" id="formCantidad">
			<header>
				<h1>Modificar cantidad de unidades disponibles</h1>
				<?php if(isset($articulo) && $articulo){?>
				<p>
					Desde <a
						href="product/<?=$articulo->id."-".normalizarTexto($articulo->titulo)?>"
						title="<?=$articulo->titulo?>"><?=$articulo->titulo?></a>
				</p>
				<?php }?>
			</header>
			<div class="wrap">
				<div class="line">
					<label for="" class="col110">Cantidad actual:</label> <span
						class="col60 t-r mr10"><?=$articulo->cantidad?></span><span
						class="col110">disponibles</span>
				</div>
				<div class="line">
					<label for="" class="col110">Nueva cantidad:</label> <span
						class="col60 t-r mr10"><input name="cantidadModificar" type="text"
						value="<?=$articulo->cantidad?>"
						class="w3em t-r required min-value"
						data-error-required="La nueva cantidad es requerida"
						data-min-value="0" data-min-value-equal="true"
						data-error-min-value="La nueva cantidad debe ser mayor 0"
						maxlength="4" /></span><span class="col110">disponibles</span> <span
						class="errorTxt" id="cantidadModificarError"></span>
				</div>
			</div>
			<!--wrap-->
			<footer>
				<p class="actions">
					<input type="hidden" name="articulo"
						value="<?=isset($articulo)?$articulo->id:""?>" /><input
						type="submit" class="bt" value="Actualizar" /> <span class="mhm">o</span>
					<a class="nyroModalClose">cancelar</a>
				</p>
			</footer>
		</form>
	</div>
</div>

<script type="text/javascript">
	$(function() {
 	 	$('.nmodal').nyroModal();
 	 	$("#formCantidad").on("submit",function(){var x=formItemSubmit.call(this);if(x){modificarCantidad.call(this)};return false;});
	});
</script>