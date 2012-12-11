<div id="popUp">
	<div class="formA">
		<header>
			<h1>Denunciar retraso en el pago de los artículos</h1>
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
		</header><?php
		$ahora = time ();
		$dif = ($ahora - strtotime ( $paquete->fecha )) / 86400;
		$tdenuncia2a = floatval ( $this->configuracion->variables ( "denuncia2a" ) );
		if ($dif >= $tdenuncia2a) {
			?>
		<form action="" method="post"
			onsubmit="return denunciarPago.call(this);" id="formDenunciaEnvio">
			<div class="wrap">
				<p class="justify">Antes de denunciar el retraso en el pago de los
					artículos, envía un mensaje privado al comprador para intentar
					resolver el problema.</p>
				<br />

				<p class="justify">Si no llegáis a un entente, denuncia la transacción; en ese momento el comprador tiene un plazo de <?=$this->configuracion->variables("denuncia2b");?> días para pagar los artículos, si no lo hace abriremos una disputa.</p>
			</div>
			<!--wrap-->
			<footer>
				<p class="actions">
					<input type="hidden" name="paquete" value="<?=$paquete->id?>" /> <input
						type="submit" class="bt red" value="<?=traducir("Denunciar")?>" />
					<span class="mhm">o</span> <a class="nyroModalClose">cancelar</a>
				</p>
			</footer>
		</form>
	<?php
		} else {
			?><div class="wrap">
			<p>Para denunciar esta transacción debe esperar <?=ceil($tdenuncia2a-$dif)?> días</p>
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