<?php
if (isset ( $articulo )) {
	$id = $articulo->id;
}
if ($id) {
	$this->load->view ( "no-existe", array (
			"mensaje" => "Lo sentimos, el articulo $id ya no esta disponible" 
	) );
} else {
	$this->load->view ( "no-existe");
	return;
}
?>