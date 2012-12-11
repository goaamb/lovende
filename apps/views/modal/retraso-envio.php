<div id="popUp">
	<div class="formA">
		<form class="clearfix d-b" action="post" id="formRetrasoEnvio"
			onsubmit="return verificarRetrasoEnvio.call(this)">
			<header>
				<h1>Retraso en el envío de los artículos denunciado</h1>
				<p>
					Comprador <a href="store/<?=$comprador->seudonimo?>"
						<?php if($comprador->estado=="Baneado"){print "class='baneado'";}?>
						title="Ver perfil de <?=$comprador->seudonimo?>"><strong><?=$comprador->seudonimo?></strong></a>
					<a href="home/modal/votos/votos/<?=$comprador->id?>"
						class="nmodal green">+<?=$comprador->positivo?></a> 
					<?php if($comprador->negativo>0){?>
					<a href="home/modal/votos/votos/<?=$comprador->id?>"
						class="nmodal red">-<?=$comprador->negativo?></a> <?php }?><span
						class="dark-grey">|</span> <a
						href="home/modal/enviar-mensaje-privado/mensaje/<?=$comprador->id?>"
						class="nmodal" title="Enviar mensaje privado">Enviar mensaje
						privado</a>
				</p>
			</header>
			<div class="wrap">
				<p class="justify">El comprador ha denunciado tu retraso en el envío
					de los artículos. Tienes <?=$this->configuracion->variables("denuncia3b");?> días para confirmar el envío o marcar que no has recibido el pago, si no lo haces abriremos una disputa.</p>
				<br />
				<p>
					<label><input type="checkbox" name="nopago" value="1" /> Todavía no
						he recibido el pago (abriremos una disputa por impago).</label>
				</p>
				<p>&nbsp;</p>
			</div>
			<footer>
				<p class="actions">
					<input type="hidden" name="paquete" value="<?=$paquete->id?>" /> <input
						type="submit" class="bt" value="<?=traducir("Aceptar")?>" /> <span
						class="mhm">o</span> <a class="nyroModalClose">Cerrar</a>
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