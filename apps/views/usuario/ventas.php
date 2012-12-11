<div class="wrapper clearfix">
	<?php
	$articulosVendidos = (isset ( $articulosVendidos ) ? $articulosVendidos : false);
	$articulosEnVenta = (isset ( $articulosEnVenta ) ? $articulosEnVenta : false);
	$articulosNoVendidos = (isset ( $articulosNoVendidos ) ? $articulosNoVendidos : false);
	$preview1 = (! $preview && $articulosVendidos);
	$preview2 = (! $preview && $articulosEnVenta);
	$preview3 = (! $preview && $articulosNoVendidos);
	$this->load->view ( "usuario/cabecera-perfil", array (
			"seccion" => "perfil" 
	) );
	if ($preview || $preview1 || $seccion_pendiente) {
		$this->load->view ( "usuario/lista-vendidos", array (
				"articulos" => $articulosVendidos 
		) );
	}
	if ($preview && ($articulosVendidos || $seccion_pendiente || $seccion_nuevo)) {
		?><div class="ventasSeparador"></div><?php
	}
	if ($preview || $preview2 || $seccion_nuevo) {
		$this->load->view ( "usuario/lista-en-venta", array (
				"articulos" => $articulosEnVenta 
		) );
	}
	if ($preview && ($articulosEnVenta || $seccion_pendiente || $seccion_nuevo)) {
		?><div class="ventasSeparador"></div><?php
	}
	if ($preview || $preview3) {
		$this->load->view ( "usuario/lista-no-vendidos", array (
				"articulos" => $articulosNoVendidos 
		) );
	}
	?>
</div>