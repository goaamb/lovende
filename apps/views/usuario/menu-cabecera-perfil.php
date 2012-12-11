<p><?=$titulo?></p>
<ul>
<?php
foreach ( $listaMenu as $menu ) {
	?><li><a href="<?=$menu["link"];?>" title="<?=$menu["descripcion"];?>"
		class="<?=$menu["class"]?>"><?=$menu["descripcion"];?></a></li><?php
}
?>
</ul>