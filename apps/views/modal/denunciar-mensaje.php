<?php
/*
 * $data['reportador'] = $this->usuario->darUsuarioXId($id); $data['idmensaje']
 * = $extra; $data['reportado']
 */

?>

<script type="text/javascript">

function buscarvotos(reportador,reportado,idmensaje)
{
	var descripcion = document.getElementById('txt1').value;
	var motivo = document.getElementById('motivo').value;
	
	if(motivo!='')
	{
		var parametros = {reportador:reportador , reportado:reportado, idmensaje:idmensaje, descripcion:descripcion, motivo:motivo};

		$.ajax({
			url:'administrador/guardarbdreporte',
			data:parametros,
			type:"POST",
			
				success:function(res)
				{
					
						$.nmManual("home/modal/denuncia-enviado/myuser");
					
					setTimeout("$('.nyroModalClose').click();", 4000);
					
				}
			
			  });
	}
	else
	{
		alert("sin datos");
		}
	
	
}
</script>
<div id="popUp">
	<div class="formA">
		<form action="" method="post">
			<header>
				<h1>Denunciar</h1>

				<p>
					Desde <a href="store/<?php echo $reportado->seudonimo;?>"
						<?php if($reportado->estado=="Baneado"){print "class='baneado'";}?>
						title="<?php echo $reportado->seudonimo;?>"><?php echo $reportado->seudonimo;?></a>
				</p>

				<p>					
						Desde mensaje id: <?php echo $idmensaje;?>
				</p>

			</header>
			<div class="wrap">
				<div class="line">
					<label for="">Motivo:</label>
					<p>
						<select name="motivo" id="motivo"><option value="">Elige</option>
							<option value="Spam">Spam</option>
							<option value="Anuncio ilegal">Anuncio ilegal</option>
							<option value="Violación de copyright">Violación de copyright</option>
							<option value="Otros">Otros</option></select>

					</p>
				</div>
				<div class="line">
					<label for="">Describe el Motivo (opcional):</label>
					<p>
						<textarea rows="4" cols="" class="w100" name="descripcion"
							id="txt1"></textarea>
					</p>
				</div>
			</div>
			<!--wrap-->
			<footer>
				<p class="actions">
					<input type="submit" class="bt" value="Send"
						onclick="buscarvotos('<?php echo $reportador->id."','".$reportado->id."','".$idmensaje;?>')">
					<span class="mhm">o</span> <a class="nyroModalClose">cancelar</a>
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