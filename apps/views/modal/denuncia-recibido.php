<div id="popUp">
	<div class="formA">
		<header>
			<h1>Denunciar artículo no recibido</h1>
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
		$tdenuncia4a = floatval ( $this->configuracion->variables ( "denuncia4a" ) );
		$dif3 = ($ahora - strtotime ( $paquete->fecha_envio )) / 86400;
		if ($dif3 >= $tdenuncia4a) {
			?>
		<form action="" method="post"
			onsubmit="return denunciarRecibido.call(this);"
			id="formDenunciaRecibido">
			<div class="wrap">
				<p class="justify">Antes de denunciar como artículo no recibido
					envía un mensaje privado al vendedor, el retraso puede deberse a
					problemas con la compañia de envío.</p>
				<br />
				<p class="justify">Si no llegáis a un entente, denuncia la transacción, en ese momento tendras un plazo de <?=$this->configuracion->variables("denuncia4b");?> días para confirmar la recepción del artículo. Si no lo haces abriremos una disputa.</p>

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
			<p>Para denunciar esta transacción debe esperar <?=ceil($tdenuncia4a-$dif3)?> días</p>
		</div>
		<footer>
			<p class="actions">
				<a class="bt nyroModalClose">Cerrar</a>
			</p>
		</footer><?php
		}
		?>
	</div>
</div>
<script type="text/javascript">
	$(function() {
 	 	$('.nmodal').nyroModal();
	});
</script>