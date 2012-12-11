<div id="popUp">
	<div class="formA">
		<form class="clearfix d-b" action="post" id="formGastos"
			onsubmit="return denunciarGastosEnvio.call(this);">
			<header>
				<h1>Denunciar retraso en añadir los gastos de envío</h1>
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
						class="nmodal" title="Enviar mensaje privado">enviar mensaje
						privado</a>
				</p>
			</header>
			<div class="wrap">
				<p class="justify">Antes de denunciar el retraso en añadir los
					gastos de envío, envía un mensaje privado al vendedor para intentar
					resolver el problema</p>
				<p class="justify">Si no llegáis a un entente, denuncia la transacción; en ese momento el vendedor tiene un plazo de <?=$this->configuracion->variables("denuncia1b")?> días para añadir los gastos, pasados estos el vendedor recibirá un voto negativo y la transacción finalizará.</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
			</div>
			<footer>
				<p class="actions">
				<?php
				$total = 0;
				$id_articulos = array ();
				$id_transacciones = array ();
				foreach ( $articulos as $articulo ) {
					if ($articulo->tipo == "Cantidad") {
						$id_transacciones [] = $articulo->transaccion;
						$total += $articulo->precio * $articulo->cantidad;
					} else {
						$id_articulos [] = $articulo->id;
						$total += $articulo->precio;
					}
				}
				?>
					<input type="hidden" name="total" value="<?=$total?>" /> <input
						type="hidden" name="transacciones"
						value="<?=implode ( ",", $id_transacciones )?>" /> <input
						type="hidden" name="articulos"
						value="<?=implode ( ",", $id_articulos )?>" /> <input
						type="submit" class="bt red" value="<?=traducir("Denunciar")?>" />
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