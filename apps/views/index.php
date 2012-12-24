<div class="col300">
	<div class="siguenos300">
		<h2><?=traducir("Síguenos en Facebook")?></h2>
		<p><?=traducir("Que no se te escape nada")?></p>
		<div class="fb clearfix">
			<a href="http://www.facebook.com/pages/Lovende/471203802892372"
				title="<?=traducir("Ir a Facebook de Lovende")?>" class="f-l mrl"><img
				src="assets/images/ico/ico-fb-29.png" alt="Facebook" /></a>
			<div id="fb-root"></div>
			<script>(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/es_ES/all.js#xfbml=1";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
			<div class="mts f-l">
				<div class="fb-like" data-href="http://www.facebook.com/pages/Lovende/471203802892372"
					data-send="false" data-layout="button_count" data-width="250"
					data-show-faces="false" data-font="arial"></div>
			</div>
		</div>
	</div>
	<div class="bannerIpad">
		<a href="home/modal/enviar-mail/mail/5" class="nmodal"
			title="<?=traducir("Preguntas frecuentes")?>"><?=traducir("¿Tienes alguna pregunta?")?></a>
	</div>
</div>
<!--col300-->
<div class="categoriesBox">
	<div class="cab">
		<h2>
			<?=traducir("Compra y vende por <strong>subasta</strong> o <strong>precio fijo</strong>")?>
			<a href="benefit" class="" title="<?=traducir("Leer más")?>"><?=traducir("leer más")?></a>
		</h2>
	</div>
	<div class="cont clearfix">
		<?php
		$nc = floor ( count ( $categorias ) / 3 );
		?><ul class="col"><?php
		$count = 0;
		$max = 3 * $nc;
		foreach ( $categorias as $id => $c ) {
			$count ++;
			?><li><a href="<?=base_url()."?categoria=$id"?>"
				title="<?=$c["nombre"]?>"><?=$c["nombre"]?></a></li><?php
			if ($count < $max && $count % $nc == 0 && $count !== count ( $categorias )) {
				?></ul>
		<ul class="col"><?php
			}
		}
		?></ul>
	</div>
</div>
<!--categoriesBox-->

<div class="carruselHome">
	<h3><?=traducir("Artículos que pueden interesarte")?></h3>
	<ul id="carruselHome">
	<?php
	foreach ( $articulos as $articulo ) {
		if ($articulo->usuario) {
			$imagen = array_shift ( explode ( ",", $articulo->foto ) );
			$imagen = imagenArticulo ( $articulo->usuario, $imagen, "thumb" );
			if ($imagen) {
				list ( $w, $h ) = getimagesize ( BASEPATH . "../$imagen" );
				$nh = 95;
				$nw = $nh * $w / $h;
				$furl = "product/" . $articulo->id . "-" . normalizarTexto ( $articulo->titulo );
				?><li><div class="image">
				<a href="<?=$furl?>" title="<?=$articulo->titulo?>"> <img src="<?=$imagen?>" alt="<?=$articulo->titulo?>"
					style="width:<?=$nw?>px;height:<?=$nh?>px;" /></a>
			</div>
			<h4>
				<a href="<?=$furl?>" title="<?=traducir("Ver producto")?>"
					style="height: 2.5em; display: inline-block; overflow: hidden;"><?=$articulo->titulo?></a>
			</h4>
			<p class="price"><?=formato_moneda($articulo->tipo=="Fijo" || $articulo->tipo=="Cantidad"?$articulo->precio:$articulo->mayorPuja)." €"?></p></li><?php
			}
		}
	}
	?>
	</ul>
</div>