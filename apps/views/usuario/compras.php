<div class="wrapper clearfix">
	<?php
	$preview = (isset ( $preview ) ? $preview : true);
	$articulosComprados = (isset ( $articulosComprados ) ? $articulosComprados : false);
	$articulosEnCompra = (isset ( $articulosEnCompra ) ? $articulosEnCompra : false);
	$articulosNoComprados = (isset ( $articulosNoComprados ) ? $articulosNoComprados : false);
	$preview1 = (! $preview && $articulosComprados);
	$preview2 = (! $preview && $articulosEnCompra);
	$preview3 = (! $preview && $articulosNoComprados);
	$this->load->view ( "usuario/cabecera-perfil", array (
			"seccion" => "perfil" 
	) );
	if ($preview || $preview1 || $seccion_pendiente) {
		$this->load->view ( "usuario/lista-comprados", array (
				"articulos" => $articulosComprados 
		) );
	}
	if ($preview && ($articulosComprados || $seccion_pendiente || $seccion_nuevo)) {
		?><div class="ventasSeparador"></div><?php
	}
	if ($preview || $preview2 || $seccion_nuevo) {
		$this->load->view ( "usuario/lista-en-compra", array (
				"articulos" => $articulosEnCompra 
		) );
	}
	if ($preview && ($articulosEnCompra || $seccion_pendiente || $seccion_nuevo)) {
		?><div class="ventasSeparador"></div><?php
	}
	if ($preview || $preview3) {
		$this->load->view ( "usuario/lista-no-comprados", array (
				"articulos" => $articulosNoComprados 
		) );
	}
	?>
</div>