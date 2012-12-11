<?php
if (isset ( $categorias ) && is_array ( $categorias ) && count ( $categorias ) > 0) {
	$keys = array_keys ( $categorias );
	$nivel = $categorias [$keys [0]] ["nivel"];
	?>
<a href="#" class="trigger" style="display: none;">Selecciona una
	categoría</a>
<span class="choice" <?php if(isset($arbol)){?> style="display: block;"
	<?php }?>><?php if(isset($arbol[$nivel-1])){ print $arbol[$nivel-1]["nombre"];}?></span>
<div class="cat" <?php if(isset($arbol)){?> style="display: none;"
	<?php }?>>
	<ul><?php
	foreach ( $categorias as $id => $categoria ) {
		?><li><a href="<?=base_url()."category/".$categoria["url"]?>"
			title="<?=$categoria["nombre"];?>"
			data-value="<?=$categoria["nombre"];?>" data-id="<?=$id?>"
			data-nivel="<?=$categoria["nivel"];?>"><?=$categoria["nombre"];?></a></li><?php
	}
	?></ul>
</div><?php
	if ($nivel == 1 && ! isset ( $arbol )) {
		?><script type="text/javascript">
$("#lista1 .trigger").show();
$("#lista1 .cat").hide();
</script><?php
	}
}
?>