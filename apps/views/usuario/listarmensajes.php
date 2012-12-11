<?php

$inicio = (isset ( $inicio ) ? intval ( $inicio ) + 1 : 0);
$totalpagina = (isset ( $totalpagina ) ? intval ( $totalpagina ) : 0);
$total = (isset ( $total ) ? intval ( $total ) : 0);
$mensaje = (isset ( $mensaje ) ? $mensaje : null);

$seccionPerfil = isset ( $seccionPerfil ) ? $seccionPerfil : "";

?>


<header class="cont-cab">
	<h1><?="Mis mensajes";?></h1>
	<p>
                  
		<?php
		$sections = array (
				"eliminar" => array (
						"texto" => traducir ( "Eliminar seleccionados" ),
						"title" => traducir ( "Ver todos los tipos de artículo" ),
						"url" => "mensaje/messages" 
				),
				"auction" => array (
						"texto" => traducir ( "Ver solo mensajes no leidos" ),
						"title" => traducir ( "Ver subastas" ),
						"url" => "mensaje/messages" 
				),
				"item" => array (
						"texto" => traducir ( "Marcar como no leidos" ),
						"title" => traducir ( "Ver sólo ventas con precio fijo" ),
						"url" => "mensaje/messages" 
				) 
		);
		$section = isset ( $section ) ? $section : "eliminar";
		foreach ( $sections as $i => $s ) {
			if ($section == $i) {
				print $s ["texto"] . " | ";
			} else {
				?><a href="<?=$s["url"]?>" title="<?=$s["title"]?>"><?=$s["texto"]?></a> | <?php
			}
		}
		
		?>
			<?//=$this->usuario_mensaje->get_mensaje_cantidad();?> 
                                
                                <?php
																																
																																$contador = 0;
																																if ($result->result () == NULL) {
																																	echo "0";
																																} else {
																																	
																																	foreach ( $result->result () as $dato ) :
																																		
																																		$cuenta = count ( $result->result () );
																																	endforeach
																																	;
																																	echo $cuenta;
																																}
																																?>
                             <?php //echo $num_results; ?>   mensajes, mostrando del <strong><?=$inicio?></strong>
		al <strong id="contadorFinal"><?=($inicio+count($mensaje)-1)?></strong>

	</p>
</header>
<?php

$contar = $this->usuario_mensaje->get_mensaje_cantidad ();
$result = $result->result ();
if ($result != NULL) {
	foreach ( $result as $dato ) {
		
		$imagen = $dato->imagen;
		$ext = (pathinfo ( $imagen, PATHINFO_EXTENSION ));
		$name = (pathinfo ( $imagen, PATHINFO_FILENAME ));
		$ruta = "files/" . $dato->seudonimo . "/";
		
		$dir = BASEPATH . "../$ruta";
		if (is_file ( $dir . "$name.$ext" )) {
			$imagen = "$ruta$name.$ext";
			?>

<div class="item clearfix">
	<span class="imagen" style="background: white url('<?=$imagen?>') no-repeat top right scroll;width:80px;height:80px;"></span>


</div>

<?php
		} 

		else {
			$imagen = "assets/images/html/profile-image.png";
			?>
<div class="item clearfix">
	<span class="imagen" style="background: white url('<?=$imagen?>') no-repeat top right scroll;width:80px;height:80px;"></span></a>


</div>

<?php
		}
		// echo $imagen;
		?>

<ul>
	<li><h1></h1></li>
	<li class="grey"></li>
</ul>

<?php
		$array ['seudonimo'] = $dato->seudonimo;
		$array ['mensaje'] = $dato->mensaje;
		$this->table->add_row ( $array );
		
		// agregamos la celda a la tabla por cada iteracion
		// echo $contador++;
	}
}
if ($result == NULL) {
	
	echo "no tiene mensajes";
}

echo $this->table->generate (); // cuando termina generamos la tabla a partir del
                               // vector
                               
// echo $this->pagination->create_links();
/*
foreach ( $articulos as $i => $articulo ) {
			
				
				$ruta = "files/" . $articulo->usuario->seudonimo . "/";
				$imagen = array_shift ( explode ( ",", $articulo->foto ) );
				$name = strtolower ( pathinfo ( $imagen, PATHINFO_FILENAME ) );
				$ext = strtolower ( pathinfo ( $imagen, PATHINFO_EXTENSION ) );
				$file = BASEPATH . "../$ruta$name.$ext";
				if (is_file ( $file )) {
					list ( , $h ) = getimagesize ( BASEPATH . "../$ruta$name.thumb.$ext" );
					//$furl = "product/" . $articulo->id . "-" . normalizarTexto ( $articulo->titulo );
					?><div
		class="item clearfix">
		<span class="imagen" style="background: white url(<?=$ruta.$name.".thumb.".$ext?>) no-repeat top right scroll;width:140px;height:<?=$h?>px;"></span></a>
		
		
	</div><?php/*
 * foreach ( $articulos as $i => $articulo ) { $ruta = "files/" .
 * $articulo->usuario->seudonimo . "/"; $imagen = array_shift ( explode ( ",",
 * $articulo->foto ) ); $name = strtolower ( pathinfo ( $imagen,
 * PATHINFO_FILENAME ) ); $ext = strtolower ( pathinfo ( $imagen,
 * PATHINFO_EXTENSION ) ); $file = BASEPATH . "../$ruta$name.$ext"; if (is_file
 * ( $file )) { list ( , $h ) = getimagesize ( BASEPATH .
 * "../$ruta$name.thumb.$ext" ); //$furl = "product/" . $articulo->id . "-" .
 * normalizarTexto ( $articulo->titulo ); ?><div class="item clearfix"> <span
 * class="imagen" style="background: white url(<?=$ruta.$name.".thumb.".$ext?>)
 * no-repeat top right scroll;width:140px;height:<?=$h?>px;"></span></a>
 * </div><?php } } //if ($total > $totalpagina) {
 */
?>