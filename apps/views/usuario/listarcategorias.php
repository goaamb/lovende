<?php
if (isset ( $categorias ) && is_array ( $categorias ) && count ( $categorias ) > 0) {
	if (! isset ( $seccion )) {
		$seccion = "category";
	}
	$seccion .= "/";
	$keys = array_keys ( $categorias );
	?><ul class="listaNivel<?=$categorias[$keys[0]]["datos"]["nivel"];?>"><?php
	foreach ( $categorias as $k => $categoria ) {
		$d = $categoria ["datos"];
		?><li><a href="<?=base_url()."{$seccion}category/".$d["url"]."-".$k?>"
		title="<?=$d["nombre"];?>"
		<?php if (count ( $categoria ["hijos"] ) > 0) {?> class="child"
		<?php }else{?> class="nochild" <?php }?>><?=$d["nombre"];?></a> <span
		class="grey">(<?=$d["cantidad"]?>)</span><?php
		if (count ( $categoria ["hijos"] ) > 0) {
			$this->load->view ( "usuario/listarcategorias", array (
					"categorias" => $categoria ["hijos"] 
			) );
		}
		?></li><?php
	}
	?></ul><?php
}
?>