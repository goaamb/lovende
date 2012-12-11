<div id="popUp">
	<div class="formA">
		<form class="clearfix d-b" action="post" id="formRetrasoEnvio"
			onsubmit="return verificarRetrasoEnvio.call(this)">
			<header>
				<h1>Denuncia de artículo no recibido</h1>
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
				<p class="justify">El comprador ha denunciado que no ha recibido el artículo. Si no confirma la recepción en <?=$this->configuracion->variables("denuncia3b");?> días abriremos una disputa.</p>
				<p>&nbsp;</p>
				<p class="justify">Por favor contacta con el comprador por mensaje
					privado para llegar a un entente.</p>
				<p>&nbsp;</p>
			</div>
			<footer>
				<p class="actions">
					<input type="hidden" name="paquete" value="<?=$paquete->id?>" /> <a
						class="nyroModalClose bt">Cerrar</a>
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