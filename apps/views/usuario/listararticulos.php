<?php
$criterio = (isset ( $criterio ) && trim ( $criterio ) !== "" ? $criterio : false);
$inicio = (isset ( $inicio ) ? intval ( $inicio ) + 1 : 0);
$totalpagina = (isset ( $totalpagina ) ? intval ( $totalpagina ) : 0);
$total = (isset ( $total ) ? intval ( $total ) : 0);
$orden = (isset ( $orden ) ? $orden : "");
$ubicacion = (isset ( $ubicacion ) ? $ubicacion : "");
$categoria = (isset ( $categoria ) ? $categoria : "");
$articulos = (isset ( $articulos ) ? $articulos : null);
$vencimientoOferta = intval ( $this->configuracion->variables ( "vencimientoOferta" ) ) * 86400;
$profile = (isset ( $profile ) ? $profile : null);
$pagSig = isset ( $pagSig ) ? $pagSig : 2;
?><section class="result-list">
	<header class="cont-cab">
		<h1><?=(isset($criterio) && trim($criterio)!==""?traducir("Búsqueda:")." ".$criterio:($profile?traducir("Tienda de")." ".$usuario->seudonimo:traducir("Últimos artículos")))?></h1>
		<p>
		<?php
		$sections = array (
				"all" => array (
						"texto" => traducir ( "Ver todo" ),
						"title" => traducir ( "Ver todos los tipos de artículo" ),
						"url" => base_url () . ($profile ? "store/{$usuario->seudonimo}/" : "") . "?" . ($criterio ? "criterio=$criterio" : "") . "&" . ($orden ? "orden=$orden" : "") . "&" . ($ubicacion ? "ubicacion=$ubicacion" : "") . "&" . ($categoria ? "categoria=$categoria" : "") 
				),
				"auction" => array (
						"texto" => traducir ( "Sólo subastas" ),
						"title" => traducir ( "Ver subastas" ),
						"url" => ($profile ? "store/{$usuario->seudonimo}/" : "") . "search/auction" . "?" . ($criterio ? "criterio=$criterio" : "") . "&" . ($orden ? "orden=$orden" : "") . "&" . ($ubicacion ? "ubicacion=$ubicacion" : "") . "&" . ($categoria ? "categoria=$categoria" : "") 
				),
				"item" => array (
						"texto" => traducir ( "Sólo precio fijo" ),
						"title" => traducir ( "Ver sólo ventas con precio fijo" ),
						"url" => ($profile ? "store/{$usuario->seudonimo}/" : "") . "search/item" . "?" . ($criterio ? "criterio=$criterio" : "") . "&" . ($orden ? "orden=$orden" : "") . "&" . ($ubicacion ? "ubicacion=$ubicacion" : "") . "&" . ($categoria ? "categoria=$categoria" : "") 
				) 
		);
		$section = isset ( $section ) ? $section : "all";
		foreach ( $sections as $i => $s ) {
			if ($section == $i) {
				print $s ["texto"] . " | ";
			} else {
				?><a href="<?=$s["url"]?>" title="<?=$s["title"]?>"><?=$s["texto"]?></a> | <?php
			}
		}
		?>
			<?=$total?> artículos, mostrando del <strong><?=count($articulos)>0?$inicio:0;?></strong>
			al <strong id="contadorFinal"><?=($inicio+count($articulos)-1)?></strong>
		</p>
	</header><?php
	if (isset ( $articulos ) && is_array ( $articulos ) && count ( $articulos ) > 0) {
		foreach ( $articulos as $i => $articulo ) {
			if ($articulo->usuario) {
				$imagen = array_shift ( explode ( ",", $articulo->foto ) );
				$imagen = imagenArticulo ( $articulo->usuario, $imagen, "thumb" );
				if ($imagen) {
					list ( , $h ) = getimagesize ( BASEPATH . "../$imagen" );
					$furl = "product/" . $articulo->id . "-" . normalizarTexto ( $articulo->titulo );
					?><div
		class="item clearfix <?php
					if ($i + 1 == count ( $articulos )) {
						print "last-child";
						if ($total <= $totalpagina) {
							print " border";
						}
					}
					
					?>">
		<a href="<?=$furl?>" title="<?=$articulo->titulo?>"><span class="imagen" style="background: white url(<?=$imagen?>) no-repeat top right scroll;width:140px;height:<?=$h?>px;"></span></a>
		<img src="<?=$imagen?>" alt="<?=$articulo->titulo?>"
			style="display: none;" />
		<div class="meta">
			<p>
				<strong><?=formato_moneda($articulo->tipo=="Fijo" || $articulo->tipo=="Cantidad"?$articulo->precio:$articulo->mayorPuja)." €"?></strong>
			</p>
			<p><?php
					if ($articulo->tipo == "Fijo" || $articulo->tipo == "Cantidad") {
						?><span class="italic">¡<?=traducir("Cómpralo")?></span> <span
					class="red italic"><?=traducir("ya")?>!</span><?php
						if ($articulo->tipo == "Fijo") {
							?><br /> <span class="oferta"><?=traducir("o Mejor oferta")?></span><?php
						}
					} else if (intval ( $articulo->cantidadPujas ) > 0) {
						?><span class="italic"><?php
						print (isset ( $articulo->cantidadPujas )) ? intval ( $articulo->cantidadPujas ) . " " . traducir ( "pujas" ) : "";
						?></span><?php
					}
					?></p>
			<p class="grey"><?
					if ($articulo->tipo == "Fijo" || $articulo->tipo == "Cantidad") {
						print calculaTiempoDiferencia ( date ( "Y-m-d H:i:s" ), strtotime ( $articulo->fecha_registro ) + $vencimientoOferta, true );
					} else {
						print calculaTiempoDiferencia ( date ( "Y-m-d H:i:s" ), strtotime ( $articulo->fecha_registro ) + $articulo->duracion * 86400, true );
					}
					?></p>

		</div>
		<ul>
			<li><h2>
					<a href="<?=$furl?>" title="<?=$articulo->titulo?>"><?=$articulo->titulo?></a>
				</h2></li>
			<li class="grey"><?=traducir("Ubicación").": ".(isset($articulo->pais_nombre)?$articulo->pais_nombre:"")?></li>
		</ul>
	</div><?php
				}
			}
		}
		if ($total > $totalpagina) {
			if (false) {
				?><p class="ver-mas">
		<img src="assets/images/ico/ajax-loader-see-more.gif"
			alt="<?=traducir("Ver más")?>" style="display: none;" /> <a
			href="?pagina=<?=$pagSig?>"
			title="<?=traducir("Ver más productos")?>"
			onclick="return verMasArticulos('home','<?=$inicio?>','<?=$section?>');"><?=traducir("Ver más")?></a><a
			href="#" title="<?=traducir("Ir al primer artículo")?>"
			onclick="document.body.scrollTop=0; return false;"
			style="display: none;"><?=traducir("Ir al primer artículo")?></a>
	</p><?php
			} else {
				
				?><ul class="ver-mas">
		<li><a href="?pagina=1" onclick="return irPagina(1)">&lt;&lt;</a></li>
		<li><?php
				if ($pagSig <= 2) {
					?><span>&lt;</span><?php
				} else {
					?><a href="?pagina=<?=$pagSig-2?>" onclick="return irPagina(<?=$pagSig-2?>)">&lt;</a><?php
				}
				?></li><?php
				$vpag = 9;
				$npaginas = ceil ( $total / $totalpagina );
				$apag = $pagSig - 1;
				$ipag = $apag - ceil ( $vpag / 2 ) + 1;
				if ($ipag <= 0) {
					$ipag = 1;
					$lpag = $ipag + $vpag;
				} else {
					$lpag = $ipag + $vpag - 1;
				}
				$lpag = ($lpag > $npaginas ? $npaginas : $lpag);
				for($i = $ipag; $i <= $lpag; $i ++) {
					if ($i == ($pagSig - 1)) {
						?><li><span><?=$i?></span></li><?php
					} else {
						?><li><a href="?pagina=<?=$i?>" onclick="return irPagina(<?=$i?>)"><?=$i?></a></li><?php
					}
				}
				?><li><?php
				if ($pagSig <= $npaginas) {
					?><a href="?pagina=<?=$pagSig?>" onclick="return irPagina(<?=$pagSig?>)">&gt;</a><?php
				} else {
					?><span>&gt;</span><?php
				}
				?></li>
		<li><a href="?pagina=<?=$npaginas?>" onclick="return irPagina(<?=$npaginas?>)">&gt;&gt;</a></li>

	</ul><?php
			}
		}
	} else {
		?><div class="item clearfix last-child"
		style="text-align: center; padding: 20px 0px;"><?php
		if (! isset ( $usuarioPropio ) || (isset ( $usuarioPropio ) && ! $usuarioPropio)) {
			if ($profile) {
				print traducir ( "Para poner
		artículos a la venta utiliza el link vender en el menú superior." );
			} else {
				print traducir ( "La búsqueda ha devuelto 0 artículos." );
			}
		} else {
			print traducir ( "Sin artículos en venta." );
		}
		?></div>
	<p class="ver-mas"></p><?php
	}
	?>		</section>