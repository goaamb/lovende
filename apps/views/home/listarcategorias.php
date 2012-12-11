<?php
if (isset ( $categorias ) && is_array ( $categorias ) && count ( $categorias ) > 0) {
	if (! isset ( $seccion )) {
		$seccion = "category";
	}
	$seccion .= "/";
	$keys = array_keys ( $categorias );
	?><ul class="listaNivel<?=$categorias[$keys[0]]["datos"]["nivel"];?>"><?php
	$unico = (count ( $categorias ) == 1);
	foreach ( $categorias as $k => $categoria ) {
		$d = $categoria ["datos"];
		?><li><a href="?categoria=<?=$k?>" title="<?=$d["nombre"];?>"
		onclick="return cambiarBusquedaCategoria(<?=$k?>)"
		<?php if (count ( $categoria ["hijos"] ) > 0||($unico && $d["nivel"]==1)) {?>
		class="parent" <?php }else{?> class="son" <?php }?>><?=$d["nombre"];?></a>
		<span class="grey">(<?=$d["cantidad"]?>)</span><?php
		if (count ( $categoria ["hijos"] ) > 0) {
			$this->load->view ( "home/listarcategorias", array (
					"categorias" => $categoria ["hijos"] 
			) );
		}
		?></li><?php
	}
	?></ul><?php
}
?>