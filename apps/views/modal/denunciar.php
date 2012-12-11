<div id="popUp">
	<div class="formA">
		<form action="" method="post" id="formDenunciaEnvio">
			<header>
				<h1>Denunciar</h1>
				<?php if(isset($articulo) && $articulo){?>
				<p>
					Desde <a
						href="product/<?=$articulo->id."-".normalizarTexto($articulo->titulo)?>"
						title="<?=$articulo->titulo?>"><?=$articulo->titulo?></a>
				</p>
				<?php }?>
				<?php if(isset($usuario) && $usuario){?>
				<p>
					Desde <a href="store/<?=$usuario->seudonimo?>" <?php if($usuario->estado=="Baneado"){print "class='baneado'";}?>
						title="<?=$usuario->seudonimo?>"><?=$usuario->seudonimo?></a>
				</p>
				<?php }?>
			</header>
			<div class="wrap">
				<div class="line">
					<label for="">Motivo:</label>
					<p>
						<select name="motivo" class="required"
							data-error-required="<?=traducir("Añade el motivo.");?>"><option
								value="">Elige</option>
							<option value="Spam">Spam</option>
							<option value="Anuncio ilegal">Anuncio ilegal</option>
							<option value="Violación de copyright">Violación de copyright</option>
							<option value="Otros">Otros</option></select>

					</p>
				</div>
				<div class="line">
					<label for="">Describe el Motivo (opcional):</label>
					<p>
						<textarea rows="4" cols="" class="w100" name="descripcion"></textarea>
					</p>
				</div>
			</div>
			<!--wrap-->
			<footer>
				<p class="actions">
					<input type="hidden" name="articulo"
						value="<?=isset($articulo)?$articulo->id:""?>" /><input
						type="hidden" name="usuario"
						value="<?=isset($usuario)?$usuario->id:""?>" /> <input
						type="submit" class="bt" value="Enviar" /> <span class="mhm">o</span>
					<a class="nyroModalClose">cancelar</a>
				</p>
			</footer>
		</form>
	</div>
</div>

<script type="text/javascript">
	$(function() {
 	 	$('.nmodal').nyroModal();
 	 	$("#formDenunciaEnvio").on("submit",function(){var x=formItemSubmit.call(this);if(x){enviarDenuncia.call(this)};return false;});
	});
</script>