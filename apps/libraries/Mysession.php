<?php
class Mysession {
	static $init = false;
	function __construct() {
		if (! self::$init && @session_start ()) {
			self::$init = true;
		}
	}
	function userdata($nombre) {
		return isset ( $_SESSION [$nombre] ) ? $_SESSION [$nombre] : "";
	}
	function set_userdata($nombre, $valor) {
		$_SESSION [$nombre] = $valor;
	}
	function unset_userdata($nombre) {
		unset ( $_SESSION [$nombre] );
	}
}