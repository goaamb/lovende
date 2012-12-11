<style>
#popUp .wrap {
	height: 256px;
}
</style>
<div id="popUp">
	<header>
		<h1>Listado de votos</h1>
		<p>
			De <a href="#" title="Ver perfil de <?=$usuario->seudonimo?>"
				<?php if($usuario->estado=="Baneado"){print "class='baneado'";}?>><strong><?=$usuario->seudonimo?></strong></a>
			<span class="green">+<?=$usuario->positivo?></span> <?php
			if ($usuario->negativo > 0) {
				?><span class="red">-<?=$usuario->negativo?></span><?php
			}
			?>
		</p>
	</header>
	<div class="wrap">
		<table class="no-border">
			<thead>
				<tr>
					<th>Tipo</th>
					<th class="t-r">1 mes</th>
					<th class="t-r">6 meses</th>
					<th class="t-r">12 meses</th>
					<th class="t-r">Todas</th>
				</tr>
			</thead>
			<tbody>
				<tr class="odd">
					<td class="green">Positivos ventas</td>
					<td class="green t-r">+<?=$mes1["Venta"]["Positivo"]?></td>
					<td class="green t-r">+<?=$mes6["Venta"]["Positivo"]?></td>
					<td class="green t-r">+<?=$mes12["Venta"]["Positivo"]?></td>
					<td class="green t-r">+<?=$todos["Venta"]["Positivo"]?></td>
				</tr>
				<tr>
					<td class="green">Positivos compras</td>
					<td class="green t-r">+<?=$mes1["Compra"]["Positivo"]?></td>
					<td class="green t-r">+<?=$mes6["Compra"]["Positivo"]?></td>
					<td class="green t-r">+<?=$mes12["Compra"]["Positivo"]?></td>
					<td class="green t-r">+<?=$todos["Compra"]["Positivo"]?></td>
				</tr>
				<tr class="odd">
					<td class="red">Negativos ventas</td>
					<td class="red t-r">-<?=$mes1["Venta"]["Negativo"]?></td>
					<td class="red t-r">-<?=$mes6["Venta"]["Negativo"]?></td>
					<td class="red t-r">-<?=$mes12["Venta"]["Negativo"]?></td>
					<td class="red t-r">-<?=$todos["Venta"]["Negativo"]?></td>
				</tr>
				<tr>
					<td class="red">Negativos compras</td>
					<td class="red t-r">-<?=$mes1["Compra"]["Negativo"]?></td>
					<td class="red t-r">-<?=$mes6["Compra"]["Negativo"]?></td>
					<td class="red t-r">-<?=$mes12["Compra"]["Negativo"]?></td>
					<td class="red t-r">-<?=$todos["Compra"]["Negativo"]?></td>
				</tr>
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