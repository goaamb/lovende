<div id="popUp">
	<div class="formA">
		<header>
			<h1>Denunciar retraso en el envío de los artículos</h1>
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
		</header><?php
		$ahora = time ();
		$dif2 = ($ahora - strtotime ( $paquete->fecha_pago )) / 86400;
		$tdenuncia3a = floatval ( $this->configuracion->variables ( "denuncia3a" ) );
		if ($dif2 >= $tdenuncia3a) {
			?><form action="" method="post" id="formDenunciaEnvio"
			onsubmit="return denunciarEnvio.call(this);">
			<div class="wrap">
				<p class="justify">Antes de denunciar el retraso en el envío de los
					artículos, envía un mensaje privado al vendedor para intentar
					resolver el problema.</p>
				<br />

				<p class="justify">Si no llegáis a un entente, denuncia la transacción; en ese momento el vendedor tiene un plazo de <?=$this->configuracion->variables("denuncia3b");?> días para enviar los artículos o responder que no ha recibido el pago. Si no lo hace abriremos una disputa.</p>

			</div>
			<footer>
				<p class="actions">
					<input type="hidden" name="paquete" value="<?=$paquete->id?>" /> <input
						type="submit" class="bt red" value="<?=traducir("Denunciar")?>" />
					<span class="mhm">o</span> <a class="nyroModalClose">cancelar</a>
				</p>
			</footer>
		</form><?php
		} else {
			?><div class="wrap">
			<p>Para denunciar esta transacción debe esperar <?=ceil($tdenuncia3a-$dif2)?> días</p>
		</div>
		<footer>
			<p class="actions">
				<a class="bt nyroModalClose">Cerrar</a>
			</p>
		</footer><?php
		}
		?></div>
</div>
<script type="text/javascript">
	$(function() {
 	 	$('.nmodal').nyroModal();
	});
</script>