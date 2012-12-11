<?php
if (! isset ( $mensaje )) {
	$mensaje = "El contenido que busca no existe en Lovende";
}
?>
<div class="wrapper clearfix">
	<header class="cont-cab">
		<p class="no-existe"><?=$mensaje?></p>
	</header>
	<section class="section404"><?php
	$nc = floor ( count ( $categorias ) / 4 );
	?><ul class="columna"><?php
	$count = 0;
	$max = 4 * $nc;
	foreach ( $categorias as $id => $c ) {
		$count ++;
		?><li><a href="<?=base_url()."?categoria=$id"?>"
				title="<?=$c["nombre"]?>"><?=$c["nombre"]?></a></li><?php
		if ($count < $max && $count % $nc == 0 && $count !== count ( $categorias )) {
			?></ul>
		<ul class="columna"><?php
		}
	}
	?></ul><?php
	?></section>
</div>