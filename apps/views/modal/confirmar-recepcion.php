<div id="popUp">
	<div class="formA">
		<form action="" method="post"
			onsubmit="return confirmarRecepcion.call(this);"
			id="formConfirmarRecepcion">
			<header>
				<h1>Confirmar recepción</h1>
				<p>
					Vendedor <a href="store/<?=$vendedor->seudonimo?>"
						<?php if($vendedor->estado=="Baneado"){print "class='baneado'";}?>
						title="Ver perfil de <?=$vendedor->seudonimo?>"><strong><?=$vendedor->seudonimo?></strong></a>
					<a href="home/modal/votos/votos/<?=$vendedor->id?>"
						class="nmodal green">+<?=$vendedor->positivo?></a> 
					<?php if($vendedor->negativo>0){?>
					<a href="home/modal/votos/votos/<?=$vendedor->id?>"
						class="nmodal red">-<?=$vendedor->negativo?></a> <?php }?><span
						class="dark-grey">|</span> <a
						href="home/modal/enviar-mensaje-privado/mensaje/<?=$vendedor->id?>"
						class="nmodal" title="Enviar mensaje privado">Enviar mensaje
						privado</a>
				</p>
			</header>
			<div class="wrap">
				 
				 
				 
				<table class="no-border mvl black">
					<tr>
						<td><input type="radio" <?php /*if ($disputa != 0){?> style="visibility:hidden;"<?php }*/?>  name="confirma" value="<?php if ($disputa == 0){ echo 1;}else{ echo 3;}?>" <?php if ($disputa != 0){ echo "CHECKED";}?>/></td>
						<td>He recibido el artículo y estoy conforme (ambos recibiráis un
							voto positivo).</td>
					</tr>
					<?php if ($disputa == 0){?>
					<tr>
						<td><input type="radio" name="confirma" value="2" /></td>
						<td>He recibido el artículo pero no coincide con la descripción
							del vendedor (abriremos una disputa).</td>
					</tr>
					<?php }?>
				</table>
				<p class="justify">ATENCIÓN: Esta acción no puede deshacerse; en
					caso de no estar conforme con el artículo recibido intenta primero
					contactar con el vendedor por mensaje privado.</p>
			</div>
			<!--wrap-->
			<footer>
				<p class="actions">
					<input type="hidden" name="paquete" value="<?=$paquete->id?>" /> <input
						type="submit" class="bt" value="Confirmar recepción" /> <span
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