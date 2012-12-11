<?php
const NINGUNO = 0, OTRO = 1, MISMO = 2;
$baneado = ($articulo->usuario->estado == "Baneado" || $articulo->estado == "Baneado");
$visible = false;
if ($transaccion && $transaccion->comprador && $this->myuser) {
	$visible = $transaccion->comprador->id == $this->myuser->id;
} elseif ($articulo->comprador && $this->myuser) {
	$visible = $articulo->comprador == $this->myuser->id;
}
if ($this->myuser && $this->myuser->estado == "Baneado") {
	$articulo->terminado = 1;
}
$visible = ($visible && $articulo->estado !== "Baneado");
if ($baneado) {
	$articulo->terminado = 1;
}
if (! $baneado || $visible) {
	
	$seudonimo = ucfirst ( $articulo->usuario->seudonimo );
	$tipo_usuario = NINGUNO; // 0 ninguno, 1 usuario no dueño del articulo, 2
	                         // usuario
	                         // dueño
	                         // del articulo
	$cantidadOfertas = 3;
	if ($usuario) {
		if ($usuario->id !== $articulo->usuario->id) {
			$tipo_usuario = OTRO;
		} else {
			$tipo_usuario = MISMO;
		}
		$cantidadOfertas = $this->configuracion->variables ( "maximoCantidad" ) - $this->articulo->cantidadOfertas ( $articulo->id, $usuario->id )->cantidad;
	}
	$siguiendo = isset ( $siguiendo ) ? $siguiendo : false;
	$thisLink = "product/$articulo->id-" . normalizarTexto ( $articulo->titulo );
	$b64tl = str_replace ( "=", "", base64_encode ( $thisLink ) );
	$redirectLogin = "login/$b64tl";
	$vencimientoOferta = intval ( $this->configuracion->variables ( "vencimientoOferta" ) ) * 86400;
	$imagenes = explode ( ",", $articulo->foto );
	$ruta = "files/" . $articulo->usuario->id . "/";
	$file = BASEPATH . "../$ruta";
	$paisobj = ($this->myuser ? $this->myuser->pais : $this->pais);
	$pais = ($this->myuser && $this->myuser->pais && isset ( $this->myuser->pais->codigo2 ) ? $this->myuser->pais->codigo2 : $this->pais->codigo2);
	$continente = ($this->myuser && $this->myuser->pais && isset ( $this->myuser->pais->continente ) ? $this->myuser->pais->continente : $this->pais->continente);
	$gasto_envio = ($articulo->usuario->pais->codigo2 == $pais && $articulo->gastos_pais !== null ? $articulo->gastos_pais : false);
	$gasto_envio = ($gasto_envio !== false ? $gasto_envio : ($articulo->usuario->pais->continente == $continente && $articulo->gastos_continente !== null ? $articulo->gastos_continente : false));
	$gasto_envio = ($gasto_envio !== false ? $gasto_envio : ($articulo->gastos_todos !== null ? $articulo->gastos_todos : false));
	$gasto_envio = ($gasto_envio !== false ? $gasto_envio : ($articulo->envio_local ? 0 : false));
	$sincompra = false;
	if ($gasto_envio === false) {
		$sincompra = true;
	}
	?><link href="assets/css/articulo.css" type="text/css" rel="stylesheet" />
<script src="assets/js/articulo/vista-articulo.js"
	type="text/javascript"></script>
<div class="wrapper clearfix">
<?php $this->load->view("usuario/cabecera-perfil",array("seccion"=>"articulo"))?>
	<header class="cont-cab">
		<h1><?=$articulo->titulo?></h1>
		<p>
			Vendedor <a
				href="store/<?=strtolower($articulo->usuario->seudonimo);?>"
				title="ver perfil de <?=$seudonimo?>"><strong><?=$seudonimo?></strong></a>
			<a href="home/modal/votos/votos/<?=$articulo->usuario->id?>"
				class="nmodal"><span class="green">+<?=$articulo->usuario->positivo?></span>
			<?php if($articulo->usuario->negativo){?><span class="red">+<?=$articulo->usuario->negativo?></span><?php }?></a>
				<?php
	if ($tipo_usuario == OTRO) {
		?>| <a
				href="articulo/modal/enviar-mensaje-privado/mensaje/<?=$articulo->id?>"
				class="nmodal" title="Enviar mensaje privado">Enviar mensaje privado</a>
			| <a href="store/<?=strtolower($articulo->usuario->seudonimo);?>"
				title="Tienda de <?=$seudonimo?>">Ver su tienda</a> <?php
	}
	?>| <a href="home/modal/denunciar/articulo/<?=$articulo->id?>"
				title="denunciar" class="nmodal">Denunciar</a>
		</p>
	</header>
	<div class="product-file clearfix">
		<div class="gallery">
			<div id="productGallery">
				<ul><?php
	foreach ( $imagenes as $ima ) {
		if (is_file ( $file . $ima )) {
			?><li><div style=" background:transparent url(<?=$ruta.$ima?>) center center no-repeat scroll;width:640px; height: 480px;"></div></li><?php
		}
	}
	?></ul><?php
	foreach ( $imagenes as $ima ) {
		if (is_file ( $file . $ima )) {
			?><img src="<?=$ruta.$ima?>" alt="<?=$articulo->titulo?>"
					style="display: none;" /><?php
		}
	}
	?></div>
		</div>

		<div class="data"><?php
	$monto = $articulo->precio;
	if ($articulo->tipo == "Fijo") {
		$og = $this->articulo->darOfertaGanadora ( $articulo->id );
		if ($og) {
			$monto = $og->monto;
		}
	} else {
		$c = $this->articulo->mayorOferta ( $articulo->id, true );
		if ($c) {
			$monto = $monto = $c->monto_automatico;
			if ($usuario && $c->usuario === $usuario->id) {
				$monto_min = $c->monto + 0.5;
			} else {
				$monto_min = $monto + 0.5;
			}
		}
	}
	?>
			<h2>
				<span id="montoFinal"><?=formato_moneda($monto);?></span> €
			</h2>
			<p class="mbl"><?php
	if (! $articulo->terminado) {
		if ($articulo->tipo === "Fijo" || $articulo->tipo === "Cantidad") {
			print "Precio comprar ahora";
		} else {
			print "Puja más alta";
		}
	} else {
		print "Finalizado";
	}
	?></p>
			<dl <?php if ($articulo->tipo !== "Cantidad") {?> class="clearfix"
				<?php }?>><?php
	switch ($articulo->tipo) {
		case "Cantidad" :
			?>
				<?php
			
			if (! $articulo->terminado) {
				$final = $vencimientoOferta + strtotime ( $articulo->fecha_registro );
				$ahora = time ();
				$difTiempo = $final - $ahora;
				?>
				<dt>Finaliza en:</dt>
				<dd id="tiempoSubasta"><?=calculaTiempoDiferencia(date("Y-m-d H:i:s",$ahora),$final,true);?></dd>
				<?php
			} else {
				?><dt>Finalizado hace:</dt>
				<dd><?=calculaTiempoDiferencia($articulo->fecha_terminado,false,true);?></dd><?php
			}
			break;
		case "Fijo" :
			?>
				<dt>Ofertas:</dt>
				<dd><?php
			if ($articulo->ofertas) {
				?><a href="articulo/modal/ofertas/ofertas/<?=$articulo->id?>"
						class="nmodal" id="ofertasArticulo"><?=$articulo->ofertas?></a><?php
			} else {
				print "0";
			}
			?></dd>
				<?php
			
			if (! $articulo->terminado) {
				$final = $vencimientoOferta + strtotime ( $articulo->fecha_registro );
				$ahora = time ();
				$difTiempo = $final - $ahora;
				?>
				<dt>Finaliza en:</dt>
				<dd id="tiempoSubasta"><?=calculaTiempoDiferencia(date("Y-m-d H:i:s",$ahora),$final,true);?></dd>
				<?php
			} else {
				?><dt>Finalizado hace:</dt>
				<dd><?=calculaTiempoDiferencia($articulo->fecha_terminado,false,true);?></dd><?php
			}
			break;
		case "Subasta" :
			?>
				<dt>Pujas:</dt>
				<dd><?php
			if ($articulo->ofertas) {
				?><a href="#" onclick="return desplegarPujas(<?=$articulo->id?>);"
						id="ofertasArticulo"><?=$articulo->ofertas?></a><?php
			} else {
				print "0";
			}
			?></dd>
				<?php
			if (! $articulo->terminado) {
				$final = $articulo->duracion * 86400 + strtotime ( $articulo->fecha_registro );
				$ahora = time ();
				$difTiempo = $final - $ahora;
				?>
				<dt>Finaliza en:</dt>
				<dd id="tiempoSubasta"><?=calculaTiempoDiferencia(date("Y-m-d H:i:s",$ahora),$final,true);?></dd>
				<?php
			} else {
				?><dt>Finalizado hace:</dt>
				<dd><?=calculaTiempoDiferencia($articulo->fecha_terminado,false,true);?></dd><?php
			}
			?><?php
			break;
	}
	?>
				<dt>Ubicación:</dt>
				<dd><?=$articulo->usuario->ciudad->nombre.", ".$articulo->usuario->pais->nombre?></dd>
				<dt>Envío:</dt>
				<dd>
					<?php
	if ($gasto_envio !== false) {
		if ($articulo->envio_local) {
			print traducir ( "Sólo recogida local" );
		} else {
			print formato_moneda ( $gasto_envio ) . " EUR";
			?><br />Envío a <?php
			if ($articulo->gastos_todos !== false && $articulo->gastos_todos !== null) {
				print traducir ( "todo el mundo" );
			} elseif ($articulo->gastos_continente !== false && $articulo->gastos_continente !== null) {
				print $paisobj->continente;
			} else {
				print $paisobj->nombrelocal;
			}
		}
	} else {
		print traducir ( "No disponible" );
	}
	?>
				</dd>
				<dt>Forma de pago:</dt>
				<dd><?php
	$fp = explode ( ",", $articulo->pagos );
	$xfp = array ();
	foreach ( $fp as $fpx ) {
		if (isset ( Articulo_model::$formas_pago [$fpx] )) {
			$xfp [] = Articulo_model::$formas_pago [$fpx];
		}
	}
	print implode ( ", ", $xfp );
	?></dd>
				<dt>Vistas:</dt>
				<dd><?=$articulo->visita?></dd>
			</dl><?php
	$form1 = "";
	$form1c = "";
	$form2 = "";
	$form2c = "";
	$extra = "";
	$extra2 = "";
	$class = "";
	$class2 = "";
	$link2 = "product/follow/$articulo->id";
	?>
			<div class="formA"><?php
	if (! $articulo->terminado) {
		$botonTexto = ($articulo->tipo === "Fijo" ? "Enviar oferta" : "Enviar Puja");
		if ($usuario && $usuario->estado === "Incompleto") {
			$link = "home/modal/informacion-compra-venta";
			$class = $class2 = "nmodal";
			$extra2 = "<a href=\"$link\" class=\"bt bt-apaisado $class\">$botonTexto</a>";
			$link2 = $link;
		} else {
			switch ($tipo_usuario) {
				case OTRO :
					
					if (! $sincompra) {
						$form1 = form_open ( $thisLink, array (
								"id" => "formComprar" 
						) );
						$options = array (
								"id" => "formOfertar" 
						);
						if ($articulo->tipo == "Subasta") {
							// $options ["onsubmit"] = "return false;";
						}
						$form2 = form_open ( $thisLink, $options );
						$form2c = $form1c = form_close ();
						if ($articulo->tipo !== "Cantidad") {
							$link = "articulo/modal/confirmar-compra/articulo/$articulo->id";
							$class = "nmodal";
						} else {
							$link = "#";
							$class = "";
							$extra = " onclick='return confirmarCompra(\"$articulo->id\");'";
						}
						$tipoBoton = "submit";
						$extra2 = "<input type=\"$tipoBoton\"
								value=\"$botonTexto\" class=\"bt
								bt-apaisado\"/>";
					} else {
						$link = "articulo/modal/envio-nodisponible/articulo/$articulo->id";
						$class = "nmodal";
						$extra2 = "<a href=\"$link\" class=\"bt bt-apaisado $class\">$botonTexto</a>";
					}
					
					break;
				case MISMO :
					$link = "articulo/modal/no-puedes/articulo/$articulo->id";
					$class = "nmodal";
					$extra2 = "<a href=\"$link\" class=\"bt bt-apaisado $class\">$botonTexto</a>";
					break;
				default :
					$link = $redirectLogin;
					$extra2 = "<input type=\"submit\" value=\"$botonTexto\" class=\"bt bt-apaisado\" onclick=\"location.href='$redirectLogin';return false;\" />";
					$link2 = $link;
					break;
			}
		}
		if ($articulo->tipo === "Cantidad") {
			?><p class="mbs"><?php
			print $form1;
			?>
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				<dl class="clearfix">
					<dt>Cantidad:</dt>
					<dd>
						<input type="text" name="cantidad" value="1" class="required w30" />
						<span><?=$articulo->cantidad." ".traducir("disponibles")?></span><span
							id="cantidadError" class="errorTxt"
							style="color: red; margin-left: 5px;"></span>
					</dd>
				</dl>
				<span class="errorTxt" style="color: red;"><?=isset($ofertaError)?$ofertaError:""?></span>
				<input type="hidden" name="articulo" value="<?=$articulo->id?>" /> <input
					type="hidden" name="__accion" value="comprar" /> <a
					href="<?=$link?>" class="bt-blue <?=$class?>" <?=$extra?>
					title="Comprar producto">Comprar ahora</a>
					<?=$form1c;?>
				</p>
				<?php
		} else if ($articulo->tipo === "Fijo") {
			$maxof = $this->configuracion->variables ( "maximoCantidad" );
			$uo = false;
			if ($usuario) {
				$uo = $this->articulo->ultimaOferta ( $articulo->id, $usuario->id );
			}
			$nof = $maxof;
			$montoMin = 0;
			if ($uo) {
				$nof = $maxof - $uo->cantidad;
				$montoMin = $uo->monto;
			}
			?>
					<p class="mbs">
					<?=$form1;?>
					<input type="hidden" name="articulo" value="<?=$articulo->id?>" />
					<input type="hidden" name="__accion" value="comprar" /> <a
						href="<?=$link?>" class="bt-blue <?=$class?>" <?=$extra?>
						title="Comprar producto">Comprar ahora</a>
					<?=$form1c;?>
				</p>
				<?php if ($nof > 0) {?>
				<?=$form2;?>
				<input type="hidden" name="__accion" value="ofertar" /> <input
					type="hidden" name="articulo" value="<?=$articulo->id?>" />
				<div class="recuadro con-moneda mtl mbs f-l">
					<input type="text"
						class="t-r required decimal max-value <?=$montoMin>0?"min-value":""?>"
						data-max-value-modal="articulo/modal/confirmar-compra/articulo/<?=$articulo->id?>"
						data-max-value="<?=$articulo->precio?>" name="oferta"
						data-max-value-equal="true"
						data-error-required="<?=traducir("Añade el importe");?>"
						<?php
				if ($montoMin > 0) {
					print "data-min-value='$montoMin' data-error-min-value='El monto debe ser mayor a su ultima oferta' data-min-value-equal='true' data-min-value-modal='articulo/modal/importe-minimo-no-alcanzado/ultimaOferta/$articulo->id'";
				}
				?>
						id="campoOferta" /> <strong>€</strong>
				</div>
				<?=$extra2?>
				<div class="errorTxt" id="ofertaError"
					style="clear: both; color: red;"><?=form_error("oferta")?><?=isset($ofertaError)?$ofertaError:"";?></div>
				<?=$form2c;?>
				<p class="cl grey mbl">
					Te quedan <span id="cantidadOfertas"><?=$cantidadOfertas?></span>
					ofertas
				</p>
				<?php
			}
		} else {
			print $form2;
			?>
									<input type="hidden" name="__accion" value="pujar" /> <input
					type="hidden" name="articulo" value="<?=$articulo->id?>" />
				<div class="recuadro con-moneda mtl mbs f-l">
					<input type="text" class="t-r required decimal min-value"
						name="oferta" id="campoOferta"
						data-min-value='<?=(isset($c)&&$c?($monto_min):($articulo->precio)) ?>'
						data-error-min-value='El monto debe ser mayor a su ultima puja'
						data-min-value-modal='articulo/modal/importe-minimo-no-alcanzado/ultimaPuja/<?=$articulo->id?>' />
					<strong>€</strong>
				</div>
									<?=$extra2?>
									<div class="errorTxt" id="ofertaError"
					style="clear: both; color: red;"><?=form_error("oferta")?><?=isset($ofertaError)?$ofertaError:"";?></div>
									<?=$form2c;?>
									<?php
			?><p class="cl grey mbl">
					Puja mínima es de <span id="pujaMinima"><?=(isset($c)&&$c?formato_moneda($monto_min):formato_moneda($articulo->precio))?></span>
					€
				</p>
				<?php
		}
	} else {
	}
	?><p style="margin-top: 15px;">
				<?php
	if (! $siguiendo) {
		?><a href="<?=$link2?>" class="<?=$class2?>">Añadir a mis seguimientos</a><?php
	} else {
		?><span style="color: #333;">Guardado en tus </span><a
						href="store/<?=$usuario->seudonimo?>/following">Seguimientos</a><?php
	}
	?></p>
			</div>
			<div class="shareThis">
				<script type="text/javascript"
					src="http://w.sharethis.com/button/buttons.js"></script>
				<script type="text/javascript">stLight.options({publisher: "4e272322-c8ad-44a7-9ad4-c9b430126cd0"}); </script>
				<span class='st_pinterest'></span> <span class='st_facebook'></span>
				<span class='st_twitter'></span>
			</div>
			<?php if(isset($difTiempo)){?>
			<script type="text/javascript">contadorInverso('<?=$difTiempo?>','tiempoSubasta');</script>
			<?php }?>
		</div>
		<div class="post cl">
			<iframe name="iframeDescripcion" id="iframeDescripcion"
				src="articulo/mostrarDescripcion/<?=$articulo->id?>" frameborder="0"
				style="border: none; width: 100%; min-height: 30px; overflow: hidden;"
				scrolling="no" onload="calcularContenidoTamaño.call(this);"></iframe>
			<?php
	if (isset ( $aclaraciones ) && $aclaraciones && is_array ( $aclaraciones ) && count ( $aclaraciones ) > 0) {
		foreach ( $aclaraciones as $ta ) {
			?><p>
				<strong><?=traducir("Nota añadida por el vendedor hace")." ".calculaTiempoDiferencia($ta->fecha,false,true);?></strong><br /><?php
			print parse_text_html ( $ta->texto );
			?></p><?php
		}
	}
	?>
		</div>

	</div>
</div><?php
} else {
	$this->load->view ( "articulo/no-existe");
}
?>




