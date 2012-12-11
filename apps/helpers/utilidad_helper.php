<?php
if (! function_exists ( "encriptacion" )) {
	function encriptacion($u, $p) {
		return sha1 ( md5 ( $u ) . base64_encode ( $p ) );
	}
}
if (! function_exists ( "codificarPassword" )) {
	function codificarPassword($texto) {
		$texto = strrev ( $texto );
		$texto = maymin ( $texto );
		$texto = rot5num ( $texto );
		return str_rot13 ( $texto );
	}
}
if (! function_exists ( "decodificarPassword" )) {
	function decodificarPassword($texto) {
		$texto = str_rot13 ( $texto );
		$texto = maymin ( $texto );
		$texto = rot5num ( $texto );
		return (strrev ( $texto ));
	}
}
if (! function_exists ( "maymin" )) {
	function maymin($texto) {
		for($i = 0; $i < strlen ( $texto ); $i ++) {
			$l = $texto [$i];
			if (preg_match ( "/[A-Z]/", $l )) {
				$l = strtolower ( $l );
			} else if (preg_match ( "/[a-z]/", $texto )) {
				$l = strtoupper ( $l );
			}
			$texto [$i] = $l;
		}
		return $texto;
	}
}
if (! function_exists ( "rot5num" )) {
	function rot5num($texto) {
		for($i = 0; $i < strlen ( $texto ); $i ++) {
			$l = $texto [$i];
			if (preg_match ( "/[0-9]/", $l )) {
				$l = ($l + 5) % 10;
			}
			$texto [$i] = $l;
		}
		return $texto;
	}
}
if (! function_exists ( "normalizarTexto" )) {
	function normalizarTexto($texto) {
		$texto = htmlentities ( $texto );
		$texto = preg_replace ( "/&([\w])acute;/", "$1", $texto );
		$texto = preg_replace ( "/&([\w])tilde;/", "$1", $texto );
		$texto = str_replace ( "/", "-", $texto );
		$texto = str_replace ( ",", "_", $texto );
		$texto = str_replace ( " ", "_", $texto );
		$texto = str_replace ( "__", "_", $texto );
		$texto = str_replace ( "_-_", "-", $texto );
		$texto = preg_replace ( "/[\W^-^_]/", "_", $texto );
		$texto = strtr ( strtolower ( "$texto" ), "ÀÁÂÃÄÅàáâãäåÈÉÊËèéêëÌÍÎÏìíîïÒÓÔÕÖØòóôõöøÙÚÛÜùúûüÇçÑñÿ", "aaaaaaaaaaaaeeeeeeeeiiiiiiiioooooooooooouuuuuuuuccnny" );
		return $texto;
	}
}
if (! function_exists ( "my_set_radio" )) {
	function my_set_radio($nombre, $value, $default = false) {
		$r = isset ( $_POST [$nombre] ) ? $_POST [$nombre] : (isset ( $_GET [$nombre] ) ? $_GET [$nombre] : false);
		if (($r && $value == $r) or (! $r && $default)) {
			return "checked='checked'";
		}
		return "";
	}
}
if (! function_exists ( "my_set_checkbox" )) {
	function my_set_checkbox($nombre, $value, $default = false) {
		$r = isset ( $_POST [$nombre] ) ? $_POST [$nombre] : (isset ( $_GET [$nombre] ) ? $_GET [$nombre] : "");
		if (($r && $value == $r) or (is_array ( $r ) && array_search ( $value, $r ) !== false) or (! $r && $default)) {
			return "checked='checked'";
		}
		return "";
	}
}
if (! function_exists ( "my_set_select" )) {
	function my_set_select($nombre, $value, $default = false) {
		$r = isset ( $_POST [$nombre] ) ? $_POST [$nombre] : (isset ( $_GET [$nombre] ) ? $_GET [$nombre] : "");
		if (($r && $value == $r) or (is_array ( $r ) && array_search ( $value, $r ) !== false) or (! $r && $default)) {
			return "selected='selected'";
		}
		return "";
	}
}
if (! function_exists ( "my_set_value" )) {
	function my_set_value($nombre, $default = false) {
		$default = $default===false ? "" : $default;
		$r = isset ( $_POST [$nombre] ) ? $_POST [$nombre] : (isset ( $_GET [$nombre] ) ? $_GET [$nombre] : "");
		return $r ? $r : $default;
	}
}
if (! function_exists ( "get_mime" )) {
	function get_mime($filename) {
		if (function_exists ( "mime_content_type" )) {
			$m = mime_content_type ( $filename );
		} else if (function_exists ( "finfo_open" )) {
			$finfo = finfo_open ( FILEINFO_MIME );
			$m = finfo_file ( $finfo, $filename );
			finfo_close ( $finfo );
		} else {
			$server = php_uname ( 'a' );
			if (strstr ( $server, "Windows" )) {
				return "";
			}
			if (strstr ( $server, "Macintosh" )) {
				$m = trim ( exec ( 'file -b --mime ' . escapeshellarg ( $filename ) ) );
			} else {
				$m = trim ( exec ( 'file -bi ' . escapeshellarg ( $filename ) ) );
			}
		}
		$m = explode ( ";", $m );
		return trim ( $m [0] );
	}
}

if (! function_exists ( "formato_moneda" )) {
	function formato_moneda($numero) {
		if (is_numeric ( $numero )) {
			return number_format ( $numero, 2, ",", "." );
		}
		return $numero;
	}
}

if (! function_exists ( "maxArrayValue" )) {
	function maxArrayValue($a) {
		if ($a && is_array ( $a ) && count ( $a ) > 0) {
			foreach ( $a as $k => $v ) {
				if ($v) {
					return array (
							$k,
							$v 
					);
				}
			}
		}
		return false;
	}
}
if (! function_exists ( "calculaTiempoDiferencia" )) {
	function calculaTiempoDiferencia($tiempo, $ahora = false, $alMayor = false) {
		$ahora = $ahora ? $ahora : time ();
		$tiempo = strtotime ( $tiempo );
		$dif = $ahora - $tiempo;
		if ($dif > 0) {
			$dias = intval ( $dif / 86400 );
			$horas = intval ( ($dif % 86400) / 3600 );
			$minutos = intval ( ($dif % 3600) / 60 );
			$segundos = intval ( ($dif % 60) );
			if ($alMayor) {
				$d = array (
						$dias,
						$horas,
						$minutos,
						$segundos 
				);
				$t = array (
						"d",
						"h",
						"m",
						"s" 
				);
				$fm = maxArrayValue ( $d );
				if ($fm) {
					$p = $fm [0];
					$tm = $t [$p];
					array_splice ( $t, $p, 1 );
					array_splice ( $d, $p, 1 );
					$fm = $fm [1];
					$sm = maxArrayValue ( $d );
					if ($sm) {
						$p = $sm [0];
						$ts = $t [$p];
						array_splice ( $t, $p, 1 );
						array_splice ( $d, $p, 1 );
						$sm = $sm [1];
					}
					return "$fm$tm" . ($sm ? " $sm$ts" : "");
				}
			} else {
				$texto = $dias > 0 ? "{$dias}d " : "";
				$texto .= $horas > 0 ? "{$horas}h " : "";
				$texto .= $minutos > 0 ? "{$minutos}m " : "";
				$texto .= $segundos > 0 ? "{$segundos}s" : "";
			}
			return $texto;
		}
		return "0s";
	}
}

if (! function_exists ( "encriptarNombre" )) {
	function encriptarNombre($usuario, $cantidad = 4) {
		srand ( time () );
		$key = rand ();
		$val = md5 ( base64_encode ( $usuario ) . $key );
		$inicio = rand ( 0, strlen ( $val ) - $cantidad - 1 );
		return substr ( $val, $inicio, $cantidad );
	}
}

if (! function_exists ( "parse_text_html" )) {
	function parse_text_html($texto) {
		$texto = strip_tags ( $texto );
		$texto = preg_replace ( "/(http\:\/\/[^\s]+)/im", "<a href='$1' target='__blank' title='$1'>[link]</a>", $texto );
		$texto = nl2br ( $texto );
		return $texto;
	}
}
if (! function_exists ( "imagenArticulo" )) {
	function imagenArticulo($usuario, $imagen = false, $tipo = "") {
		$noimagen = ! $imagen;
		if ($noimagen) {
			$imagen = $usuario->imagen;
		}
		$user = $usuario->id;
		$ext = pathinfo ( $imagen, PATHINFO_EXTENSION );
		$name = pathinfo ( $imagen, PATHINFO_FILENAME );
		$ruta = "files/" . $user . "/";
		$dir = BASEPATH . "../$ruta";
		if (trim ( $tipo ) == "") {
			$tipo = ".";
		} else {
			$tipo = ".$tipo.";
		}
		if (is_file ( $dir . "$name$tipo$ext" )) {
			$imagen = "$ruta$name$tipo$ext";
		} else {
			if ($noimagen) {
				$imagen = "assets/images/html/profile-image{$tipo}png";
			} else {
				$imagen = "assets/images/html/no-imagen{$tipo}jpg";
			}
		}
		return $imagen;
	}
}
if (! function_exists ( "imagenPerfil" )) {
	function imagenPerfil($usuario, $tipo = "") {
		return imagenArticulo ( $usuario, false, $tipo );
	}
}
if (! function_exists ( "strip_not_allowed" )) {
	function strip_not_allowed($texto, $tags = false) {
		if ($tags) {
			$tags = explode ( ",", $tags );
			foreach ( $tags as $t ) {
				$p = "/<{$t}[^>]*>([^<\/]*)<\/{$t}>/Umi";
				$texto = preg_replace ( $p, "$1", $texto );
			}
			$p = "/<([^>]+)on[^=>'\"]+\s*=\s*['\"][^'\">]+['\"]([^>]+)>/Umi";
			$texto = preg_replace ( $p, "<$1 $2>", $texto );
		}
		return $texto;
	}
}
if (! function_exists ( "linkTargetBlank" )) {
	function linkTargetBlank($texto) {
		$p = "/<a([^>]+)target\s*=\s*['\"][^'\"]*['\"]([^>]*)>/Umi";
		$texto = preg_replace ( $p, "<a$1 $2>", $texto );
		$p = "/<a([^>]*)>/Umi";
		$texto = preg_replace ( $p, "<a$1 target='_blank'>", $texto );
		return $texto;
	}
}
if (! function_exists ( "uploadImage" )) {
	function uploadImage($ouput = true, $user = false) {
		$CI = &get_instance ();
		$CI->load->library ( "image_lib" );
		$identificador = rand ();
		$s = $user;
		if ($s) {
			$return = array ();
			$EXTENDIONTYPESIMA = array (
					"jpg",
					"bmp",
					"gif",
					"jpg",
					"png",
					"png" 
			);
			$MIMETYPESIMA = array (
					"image/jpeg",
					"image/bmp",
					"image/gif",
					"image/pjpeg",
					"image/png",
					"image/x-png" 
			);
			if (isset ( $_FILES ) && isset ( $_FILES ["imagen"] )) {
				$files = $_FILES ["imagen"];
				if (intval ( $files ["size"] ) <= 4194304 && $files ["error"] !== UPLOAD_ERR_FORM_SIZE) {
					if ($files ["type"] == "application/octet-stream") {
						$files ["type"] = get_mime ( $files ["tmp_name"] );
					}
					if (array_search ( $files ["type"], $MIMETYPESIMA ) !== false) {
						try {
							$baseruta = "files/" . $s->id . "/";
							$ruta = BASEPATH . "../" . $baseruta;
							if (! is_dir ( $ruta )) {
								mkdir ( $ruta, 0777, true );
							}
							
							$config ['image_library'] = 'gd2';
							$config ['source_image'] = $files ["tmp_name"];
							
							srand ( time () );
							do {
								$name = rand ();
								$ext = strtolower ( pathinfo ( $files ["name"], PATHINFO_EXTENSION ) );
								$rutafinal = "$ruta$name.$ext";
							} while ( is_file ( $rutafinal ) );
							
							@copy ( $files ["tmp_name"], "$ruta$name.original.$ext" );
							
							$config ['new_image'] = $rutafinal;
							$config ['create_thumb'] = false;
							$config ['maintain_ratio'] = TRUE;
							$config ['master_dim'] = "auto";
							$config ['quality'] = "60%";
							if ($CI->input->post ( "quien" ) !== "perfil") {
								$config ['width'] = 640;
								$config ['height'] = 480;
							} else {
								$config ['width'] = 150;
								$config ['height'] = 150;
							}
							$CI->image_lib->initialize ( $config );
							$CI->image_lib->resize ();
							
							$config ['new_image'] = "$ruta$name.thumb.$ext";
							if ($CI->input->post ( "quien" ) !== "perfil") {
								$config ['width'] = 140;
								$config ['height'] = 140;
							} else {
								$config ['width'] = 60;
								$config ['height'] = 60;
							}
							$CI->image_lib->initialize ( $config );
							$CI->image_lib->resize ();
							
							if ($CI->input->post ( "quien" ) == "perfil") {
								
								$config ['new_image'] = "$ruta$name.small.$ext";
								$config ['width'] = 35;
								$config ['height'] = 35;
								$CI->image_lib->initialize ( $config );
								$CI->image_lib->resize ();
							}
							$return = array (
									'name' => "$name",
									'ext' => "$ext",
									'path' => "$baseruta",
									"quien" => $CI->input->post ( "quien" ) 
							);
						} catch ( Exception $ex ) {
							
							$return = array (
									'error' => true,
									'mensaje' => "La Imagen no se pudo subir por favor revise el formato o vuelva a intentarlo mas tarde",
									"quien" => $CI->input->post ( "quien" ) 
							);
						}
					} else {
						
						$return = array (
								'error' => true,
								'mensaje' => "El Formato no es Valido",
								"quien" => $CI->input->post ( "quien" ) 
						);
					}
				} else {
					
					$return = array (
							'error' => true,
							'mensaje' => "La imagen no puede ser mayor a 4Mb",
							"quien" => $CI->input->post ( "quien" ) 
					);
				}
			} else {
				
				$return = array (
						'error' => true,
						'mensaje' => "El Archivo no se pudo subir por favor revise el formato o vuelva a intentarlo mas tarde",
						"quien" => $CI->input->post ( "quien" ) 
				);
			}
		} else {
			$return = array (
					'error' => true,
					'mensaje' => "No se encuetra loggeado en el sistema",
					"quien" => $CI->input->post ( "quien" ) 
			);
		}
		
		if ($ouput) {
			$CI->output->set_content_type ( 'text/plain' )->set_output ( json_encode ( $return ) );
		}
		
		if (! $ouput) {
			return $return;
		}
	}
}
?>