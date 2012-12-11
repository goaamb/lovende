<?php
if (! function_exists ( 'traducir' )) {
	function traducir($texto) {
		$CI = &get_instance ();
		if (! isset ( $CI->idioma )) {
			$CI->load->library ( "idioma" );
			$CI->idioma->darLenguaje ();
		}
		return $CI->idioma->traducir ( $texto );
	}
}
?>