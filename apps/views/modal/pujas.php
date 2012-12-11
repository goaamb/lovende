<div id="popUp">
	<header>
		<h1>Listado de pujas</h1>
		<p>
			Artículo <a href="#" class="nyroModalClose"
				title="Ver ficha del artículo"><?=$articulo->titulo?></a>
		</p>
	</header>
	<div class="wrap">
		<table class="no-border mbl">
			<thead>
				<tr>
					<th>Usuario</th>
					<th class="t-r">Fecha</th>
					<th class="t-r">Puja</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if ($ofertas && count($ofertas)>0) {
				$ahora = time ();
				foreach ( $ofertas as $i => $oferta ) {
					$seudonimo = $oferta->seudonimo;
					if (! $usuario || ($usuario && $usuario->id != $articulo->usuario && $usuario->seudonimo !== $seudonimo)) {
						$oferta->codigo [1] = "*";
						$oferta->codigo [2] = "*";
						$seudonimo = $oferta->codigo;
					}
					$tiempo = calculaTiempoDiferencia ( $oferta->fecha, $ahora );
					?><tr class="<?=($i%2==0?"odd":"")?>">
					<td><a href="store/<?=$oferta->seudonimo?>"
						title="Ver perfil de <?=$seudonimo?>"><?=$seudonimo?></a> <a
						href="home/modal/votos/votos/<?=$articulo->usuario?>"
						class="nmodal green">+<?=$oferta->positivo?></a>
						<?php if($oferta->negativo>0){?><a
						href="home/modal/votos/votos/<?=$articulo->usuario?>"
						class="nmodal red">-<?=$oferta->negativo?></a><?php }?></td>
					<td class="t-r">Hace <?=$tiempo;?></td>
					<td class="t-r"><?=formato_moneda($oferta->monto_automatico);?> €</td>
				</tr><?php
				}
			} else {
				?><tr class="odd">
					<td colspan="4">No existen Pujas</td>
				</tr><?php
			}
			?>
			</tbody>
		</table>
	</div>
	<!--wrap-->
	<footer>
		<p class="actions">
			<a class="nyroModalClose big">Cerrar</a>
		</p>
	</footer>
</div>

<script type="text/javascript">
	$(function() {
 	 	$('.nmodal').nyroModal();
	});
</script>