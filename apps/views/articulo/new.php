<?php
$imagenes = $this->input->post ( "imagenes" );
if (trim ( $imagenes ) !== "") {
	$imagenes = explode ( ",", $imagenes );
} else {
	$imagenes = array ();
}
for($i = 0; $i < 6; $i ++) {
	if (! isset ( $imagenes [$i] )) {
		$imagenes [$i] = false;
	}
}
$nuevo = isset ( $nuevo ) ? $nuevo : false;
$modificar = isset ( $articulo ) && ! $nuevo;
$postFile = $modificar ? "product/edit/$articulo->id" : ($nuevo ? "product/nuevo/$articulo->id" : "product/nuevo");
if (isset ( $articulo )) {
	$terminado = $articulo->terminado;
	$productoLink = "product/$articulo->id-" . normalizarTexto ( $articulo->titulo );
}
$tipo_precio = $this->input->post ( "tipo-precio" ) ? $this->input->post ( "tipo-precio" ) : "precio-cantidad-box";
$cantidadVendidos = 0;
$editable = true;
if (isset ( $articulo )) {
	$cantidadVendidos = $this->articulo->obtenerVendidosDeCantidad ( $articulo->id );
	$editable = ($articulo->tipo !== "Cantidad" || ($articulo->tipo == "Cantidad" && $cantidadVendidos <= 0));
	$gastos_pais = ($articulo->gastos_pais !== null ? my_set_value ( "gastos_pais", (isset ( $articulo ) ? ($articulo->gastos_pais) : 0) ) : "");
	$gastos_continente = ($articulo->gastos_continente !== null ? my_set_value ( "gastos_continente", (isset ( $articulo ) ? ($articulo->gastos_continente) : 0) ) : "");
	$gastos_todos = ($articulo->gastos_todos !== null ? my_set_value ( "gastos_todos", (isset ( $articulo ) ? ($articulo->gastos_todos) : 0) ) : "");
} else {
	$gastos_pais = my_set_value ( "gastos_pais", "" );
	$gastos_continente = my_set_value ( "gastos_continente", "" );
	$gastos_todos = my_set_value ( "gastos_todos", "" );
}
$envio_local = my_set_value ( "envio_local" );
?>
<script type="text/javascript"
	src="<?=base_url()?>assets/js/uploadprogress.js"></script>
<script type="text/javascript"
	src="<?=base_url()?>assets/js/articulo/articulo.js"></script>
<script type="text/javascript"
	src="<?=base_url()?>assets/js/swfupload/swfupload.js"></script>
<script type="text/javascript"
	src="<?=base_url()?>assets/js/editor/ckeditor.js"></script>
<script type="text/javascript"><?php
if ($usuario && $usuario->estado === "Incompleto") {
	?>
	var completoUsuario=false;
$(function(){
	$(".user-box .nmodal").click();
});
<?php
} else {
	?>var completoUsuario=true;<?php
}
$maxCarTitulo = 80;
?>
CKEDITOR.config.font_defaultLabel="Arial";
CKEDITOR.config.fontSize_defaultLabel="15";
CKEDITOR.config.contentsCss="<?=base_url()?>assets/css/editorDefault.css";
CKEDITOR.config.removeFormatTags = 'b,big,code,del,dfn,em,font,i,ins,kbd';
CKEDITOR.config.width = 650;
CKEDITOR.config.toolbar =
	[
		['Font','FontSize','TextColor','BGColor','-','Bold', 'Italic','Underline', '-', 'NumberedList', 'BulletedList', '-', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Source']
	];
</script>
<link href="<?=base_url()?>assets/css/nuevo.css" type="text/css"
	rel="stylesheet" />
<div class="wrapper clearfix">
	<header class="cont-cab mbl">
		<h1>Pon a la venta tu artículo GRATIS</h1>
		<p>
			Sólo pagas comisión si realizas una venta con éxito, estas son
			nuestras <a href="fees" title="Tarifas" target="_blank">tarifas de
				venta.</a>
		</p>
	</header>

	<div class="formA">
		<form id="formFake" data-submit="validFormFake">
			<div class="line">
				<label for="" class="col652"><span class="numero">1</span> Título y
					categoría del artículo o lote: <span class="f-r grey pt13"
					id="controlTitulo"><?php $titulo=set_value("titulo"); if($titulo){ print "Tiene ".($maxCarTitulo-strlen($titulo))." caracteres restantes.";}else{ print "Máximo $maxCarTitulo caracteres.";}?></span></label>
				<div class="recuadro con-consejo"><?php
				if ($editable) {
					?>
					<input type="text" class="enfoque required" data-consejo="consejo1"
						name="titulo" value="<?=$titulo?>" maxlength="<?=$maxCarTitulo?>"
						onkeyup="verificarMaximo.call(this,<?=$maxCarTitulo?>,'controlTitulo');"
						data-error-required="Añade un título." />
					<?php
				} else {
					?><span class="input"><?=$titulo;?></span><?php
				}
				?></div>
				<?=form_error("titulo");?><span class="errorTxt" id="tituloError"></span>
			</div>
			<div class="line clearfix w100">
				<div
					class="line-category <?php if(isset($arbol) && count($arbol)>0){ print "l-cat-selected";}?>"
					id="lista1">
						<?php $this->load->view("articulo/listacategorias")?>
					</div>
				<div class="postCat"
					<?php if(isset($arbol) && count($arbol)>1){ print "style='display:block;'";}?>>
					<img src="assets/images/ico/cat-arrow-right.png" />
				</div>
				<div
					class="line-category <?php if(!isset($arbol) ||(isset($arbol) && count($arbol)<=1)){ print "hidden";}else{print "l-cat-selected";}?>"
					id="lista2">
					<span class="choice"><?php if(isset($arbol[1])){ print $arbol[1]["nombre"];}?></span>
				</div>
				<div class="postCat"
					<?php if(isset($arbol) && count($arbol)>2){ print "style='display:block;'";}?>>
					<img src="assets/images/ico/cat-arrow-right.png" />
				</div>
				<div
					class="line-category <?php if(!isset($arbol) ||(isset($arbol) && count($arbol)<=2)){ print "hidden";}else{print "l-cat-selected";}?>"
					id="lista3">
					<span class="choice"><?php if(isset($arbol[2])){ print $arbol[2]["nombre"];}?></span>
				</div>
				<div class="postCat">
					<img src="assets/images/ico/cat-arrow-right.png" />
				</div>
				<div class="line-category hidden" id="lista4"></div>&nbsp<?=form_error("categoria");?>
			<span class="errorTxt" id="categoriaError"></span>
			<?php
			if ($editable) {
				?><div class="postCat mhn cl w225"
					<?php if(isset($arbol)){print "style='display:block;'";}?>>
					<a href="#" class="reset-line-categories">Cambiar categorías</a>
				</div><?php
			}
			?>
			</div>
			<div class="line con-consejo">
				<span class="numero">2</span> <label for="">Descripción del artículo
					o lote:</label>
				<?php
				if ($editable) {
					?><p>
					<textarea class="enfoque required ckeditor" data-consejo="consejo2"
						rows="5" cols="" name="descripcion"
						data-error-required="El campo Descripción es requerido"><?=set_value("descripcion");?></textarea>
					<?=form_error("descripcion");?><span class="errorTxt"
						id="descripcionError"></span>
				</p><?php
				} else {
					?><div class="textarea"><?=$articulo->descripcion?></div><?php
				}
				?></div>
		</form>
		<?php
		if ($editable) {
			?><div class="line clearfix" id="uploader1">
			<p class="mbn w650">
				<span class="numero">3</span> <label>Sube al menos 1 foto:(Máximo
					por foto 4Mb)</label> <label class="f-r pt13 mrg0">¿Prefieres el <a
					href='#' title='cargador clásico'
					onclick="return cambiarModoClasico();">cargador clásico</a>?
				</label>
			</p>
			<p id="errorUploading" class="errorTxt"></p>
			<?php
			$this->load->view ( "articulo/upload_photo", array (
					"classcapaphoto" => "portada",
					"idcapaphoto" => "capaimagen1",
					"textocapaphoto" => "foto portada",
					"quiencapaphoto" => "1",
					"imagen" => $imagenes [0] 
			) );
			for($i = 2; $i <= 6; $i ++) {
				$classcapaphoto = "";
				if ($imagenes [$i - 2]) {
					$classcapaphoto = "";
				}
				if ($i == 6) {
					$classcapaphoto .= " mrn";
				}
				$this->load->view ( "articulo/upload_photo", array (
						"classcapaphoto" => $classcapaphoto,
						"idcapaphoto" => "capaimagen$i",
						"textocapaphoto" => "subir foto",
						"quiencapaphoto" => $i,
						"imagen" => $imagenes [$i - 1] 
				) );
			}
			?>&nbsp;<?=form_error ( "imagenes" );?><span class="errorTxt"
				id="imagenesError"></span>
			<div id="divFileProgressContainer"></div>
		</div><?php
		} else {
			?><div class="line clearfix" id="uploader1">
			<p class="mbn">
				<label>Imágenes</label>
			</p>
			<?php
			foreach ( $imagenes as $img ) {
				if ($img) {
					?><div class="uploader" style="background: transparent url(<?=imagenArticulo($usuario,$img,"thumb")?>) center center no-repeat;"></div><?php
				} else {
					?><div class="uploader"></div><?php
				}
			}
			?>
		</div><?php
		}
		?>
		<?=form_open($postFile,array("id"=>"formItem","data-submit"=>"validFormItem"));?>
		<?php
		if ($editable) {
			?><div class="line clearfix" id="uploader2" style="display: none;">
			<p class="mbn col652">
				<span class="numero">3</span> <label>Sube al menos 1 foto:(Máximo
					por foto 4Mb)</label> <label class="f-r pt13 mgr0">¿Prefieres el <a
					href='#' title='cargador moderno'
					onclick="return cambiarModoModerno();">cargador normal</a>?
				</label>
			</p>
			<p class="fleft">
				<label><input name="file1" type="file" /></label><br /> <label><input
					name="file2" type="file" /></label><br /> <label><input
					name="file3" type="file" /></label><br /> <label><input
					name="file4" type="file" /></label><br /> <label><input
					name="file5" type="file" /></label><br /> <label><input
					name="file6" type="file" /></label>
			</p>
			<span class="errorTxt" id="imagenesFileError" style="display: none;"><?=traducir("Debe ingresar almenos una imagen")?></span>
		</div>
		<input type="hidden" name="titulo" class="required"
			value="<?=set_value("titulo")?>"
			data-error-required="Añade un título." /> <input type="hidden"
			name="categoria" class="required" value="<?=set_value("categoria")?>"
			data-error-required="Añade una categoría." />
		<textarea class="required" name="descripcion" style="display: none;"
			data-error-required="Añade una descripción."><?=set_value("descripcion")?></textarea>
		<input type="hidden" name="imagenes"
			value="<?=set_value("imagenes")?>" class="required"
			data-error-required="Añade una imagen." /><?php
		}
		?>
		<div class="line clearfix d-b mbl">
			<div class="opciones">
				<p style="margin-bottom: 3px;">
					<span class="numero">4</span> <label>Tipo de anuncio y precio:</label>
				</p><?php
				if ($editable) {
					?><p>
					<label><input type="radio" name="tipo-precio" class="tipo-precio"
						value="precio-cantidad-box"
						<?php
					if ($tipo_precio == "precio-cantidad-box") {
						print "checked='checked'";
					}
					?> /> Precio fijo</label>
				</p>
				<p>
					<label><input type="radio" name="tipo-precio" class="tipo-precio"
						value="precio-fijo-box"
						<?php
					if ($tipo_precio == "precio-fijo-box") {
						print "checked='checked'";
					}
					?> /> Precio fijo con opción a oferta</label>
				</p>
				<p>
					<label><input type="radio" name="tipo-precio" class="tipo-precio"
						value="subasta-box"
						<?php
					if ($tipo_precio == "subasta-box") {
						print "checked='checked'";
					}
					?> /> Subasta</label>
					<?=form_error("tipo-precio")?>
				</p>
			</div><?php
				} else {
					?><p>
				<label> Precio fijo</label>
			</p><?php
				}
				?>
				<div class="dinero">
				<div id="precio-cantidad-box" class="tipo-precio-box"
					<?php if($tipo_precio == "precio-cantidad-box"){$tp="block"; }else{$tp="none";}print "style='display:$tp;'";?>>
					<div class="recuadro w225 con-moneda f-l d-b mbm">
				<?php
				if ($editable) {
					?><input type="text" class="t-r decimal propio min-value"
							data-min-value="0" name="precio-cantidad"
							data-error-funcion="validarPrecioCantidad"
							data-error-propio="Añade el precio."
							data-error-min-value="Añade un número entero superior a 0."
							value="<?=my_set_value("precio-cantidad")?>" /><?php
				} else {
					?><span class="t-r input"><?=formato_moneda($articulo->precio)?></span><?php
				}
				?>
				</div>
					<span class="EUR">EUR</span>
				&nbsp;<?=form_error("precio-cantidad")?><span class="errorTxt"
						id="precio-cantidadError"></span>
					<div style="clear: both;">
						<p class="durac">
							<label>Cantidad:</label> <input type="text"
								class="t-r entero min-value texto " data-min-value="0"
								name="cantidad-precio" data-min-value-equal="true"
								data-error-min-value="Añade un número entero superior a 0."
								data-error-entero="Añade un número entero superior a 0"
								value="<?=my_set_value("cantidad-precio",1)?>" />
						</p>
						<span class="EUR">Unidades disponibles</span><?=form_error("cantidad-precio")?><span
							class="errorTxt" id="cantidad-precioError"></span>
					</div>
				</div>
			<?php
			if ($editable) {
				?>
			<div id="precio-fijo-box" class="tipo-precio-box"
					<?php if($tipo_precio == "precio-fijo-box"){$tp="block"; }else{$tp="none";}print "style='display:$tp;'";?>>
					<div class="recuadro w225 con-moneda f-l d-b mbm">
						<input type="text" class="t-r decimal propio" name="precio-oferta"
							data-error-funcion="validarPrecioOferta"
							data-error-propio="Añade el precio."
							value="<?=my_set_value("precio-oferta")?>" />
					</div>
					<span class="EUR">EUR</span>
				&nbsp;<?=form_error("precio-oferta")?><span class="errorTxt"
						id="precio-ofertaError"></span>
					<p class="cl mbm">
						<input type="checkbox" class="ofertasInferioresTrigger"
							name="rechazar" value="1"
							<?php $rechazar=my_set_checkbox("rechazar", "1"); print $rechazar;?> />
						Rechazar automáticamente ofertas inferiores a:
					</p>
					<div id="ofertasInferiores" class="hidden" <?php if($rechazar){?>
						style="display: inline-block;" <?php }?>>
						<div class="recuadro w225 con-moneda">
							<input type="text"
								class="t-r decimal propio max-value <?php
				if ($rechazar) {
					print "required";
				}
				?>"
								data-error-funcion="validarPrecioRechazo"
								data-error-propio="El Campo de precio minimo de oferta es requerido"
								data-max-value="this.form['precio-oferta'].value"
								data-error-max-value="El importe debe ser inferior al precio de venta."
								data-max-value-tipo="dom" name="precio-oferta-inferior"
								data-error-required="<?=traducir("Añadir importe")?>"
								value="<?=my_set_value("precio-oferta-inferior")?>" />
						</div>
						<span class="EUR">EUR</span>
				<?=form_error("precio-oferta-inferior")?><span class="errorTxt"
							id="precio-oferta-inferiorError"></span>
					</div>
				</div>
				<div id="subasta-box" class="tipo-precio-box"
					<?php if($tipo_precio=="subasta-box"){$tp="block"; }else{$tp="none";}print "style='display:$tp;'";?>>
					<div class="recuadro w225 con-moneda f-l d-b mbm">
						<input type="text" class="t-r decimal propio"
							name="precio-subasta" data-error-funcion="validarPrecioSubasta"
							data-error-propio="Añade un precio de salida."
							value="<?=my_set_value("precio-subasta")?>" />
					</div>
					<span class="EUR">EUR</span>
				&nbsp;<?=form_error("precio-subasta")?><span class="errorTxt"
						id="precio-subastaError"></span>
					<p class="durac">
						<label>Duración:</label> <select class="texto" name="duracion"><option
								value="5" <?php echo my_set_select('duracion', '5', TRUE); ?>>5
								días</option>
							<option value="7"
								<?php echo my_set_select('duracion', '7', TRUE); ?>>7 días</option>
							<option value="14" <?php echo my_set_select('duracion', '14'); ?>>14
								días</option>
							<option value="28" <?php echo my_set_select('duracion', '28'); ?>>28
								días</option>
						</select>
					</p>
				</div><?php
			}
			?></div>
		</div>
		<div class="line" id="destino-envios">
			<p>
				<span class="numero">5</span> <label for="" class="mbn">Destinos
					aceptados y gastos de envío:</label>
			</p>
			<p class="errorTxt" id="gastosError"
				data-error="<?=traducir("Añade los destinos de envío.");?>"
				style="clear: both; display: block;"><?=isset($errorGastosEnvio)?$errorGastosEnvio:""?></p>
			<p style="margin-bottom: 0px; margin-top: 3px; float: left;">
				<label style="margin-bottom: 14px; float: left;"><input
					type="checkbox" onclick="envioLocalCambio.call(this);" value="1"
					name="envio_local"
					<?php
					if ($envio_local !== "") {
						print 'checked="checked"';
					}
					?> /> <?=traducir("Sólo recogida local (los compradores deben recoger el artículo en tu dirección)");?></label>
			</p>
			<span id="envio_localError" class=" errorTxt"></span>
			<div style="clear: both;"></div>
			<p class="durac">
				<label><input type="checkbox" data-input="gastos_pais" class="c-b"
					onclick="cambiarGastos.call(this);"
					<?php
					if ($gastos_pais !== "") {
						print 'checked="checked"';
					}
					if ($envio_local !== "") {
						print ' disabled="disabled"';
					}
					?> /> <?=$this->myuser->pais->nombrelocal?></label> <input
					type="text" class="t-r decimal texto " data-min-value="0"
					data-error-min-value="La cantidad debe ser mayor igual a 0."
					data-error-required="Añade los gastos de envío." name="gastos_pais"
					value="<?=$gastos_pais?>"
					<?php
					if ($gastos_pais === "" || $envio_local !== "") {
						print 'disabled="disabled"';
					}
					?> />
			</p>
			<span class="EUR">EUR</span> <span id="gastos_paisError"
				class=" errorTxt"></span>
			<div style="clear: both;"></div>
			<p class="durac">
				<label><input type="checkbox" data-input="gastos_continente"
					class="c-b" onclick="cambiarGastos.call(this);"
					<?php
					if ($gastos_continente !== "") {
						print 'checked="checked"';
					}
					if ($envio_local !== "") {
						print ' disabled="disabled"';
					}
					?> /> <?=$this->myuser->pais->continente?></label> <input
					type="text" class="t-r decimal texto " data-min-value="0"
					data-error-min-value="La cantidad debe ser mayor igual a 0."
					data-error-required="Añade los gastos de envío."
					name="gastos_continente" value="<?=$gastos_continente?>"
					<?php
					if ($gastos_continente === "" || $envio_local !== "") {
						print 'disabled="disabled"';
					}
					?> />
			</p>

			<span class="EUR">EUR</span> <span id="gastos_continenteError"
				class=" errorTxt"></span>
			<div style="clear: both;"></div>
			<p class="durac">
				<label><input type="checkbox" data-input="gastos_todos" class="c-b"
					onclick="cambiarGastos.call(this);"
					<?php
					if ($gastos_todos !== "") {
						print 'checked="checked"';
					}
					if ($envio_local !== "") {
						print ' disabled="disabled"';
					}
					?> /> <?=traducir("El mundo")?></label> <input type="text"
					class="t-r decimal texto " data-min-value="0"
					data-error-required="Añade los gastos de envío."
					data-error-min-value="La cantidad debe ser mayor igual a 0."
					name="gastos_todos" value="<?=$gastos_todos?>"
					<?php
					if ($gastos_todos === "" || $envio_local !== "") {
						print 'disabled="disabled"';
					}
					?> />
			</p>
			<span class="EUR">EUR</span> <span id="gastos_todosError"
				class=" errorTxt"></span>
		</div>
		<div class="line">
			<p>
				<span class="numero">6</span> <label for="" class="mbn">Formas de
					pago aceptadas:</label>
			</p>
			<?php
			if ($editable) {
				?>
			<p>
				<input type="checkbox" name="forma-pago[]" value="1"
					<?=my_set_checkbox("forma-pago","1")?> /> Otros (ver descripción)
			</p>
			<p>
				<input type="checkbox" name="forma-pago[]" value="2"
					<?=my_set_checkbox("forma-pago","2")?> /> Pago contra reembolso
			</p>
			<p>
				<input type="checkbox" name="forma-pago[]" value="3"
					<?=my_set_checkbox("forma-pago","3")?> /> Transferencia bancaria
			</p>
			<p>
				<input type="checkbox" name="forma-pago[]" value="4"
					<?=my_set_checkbox("forma-pago","4",true)?> /> <img
					src="assets/images/html/logo-paypal.png" alt="Paypal" class="v-m" />
			</p>
			<p class="errorTxt" id="forma-pagoError"
				data-error="Añade las formas de pago aceptadas"></p>
			<?php
				print form_error ( "forma-pago[]" );
			} else {
				$formasPago = array (
						"1" => "Otros (ver descripción)",
						"2" => "Pago contra reembolso",
						"3" => "Transferencia bancaria",
						"4" => '<img
					src="assets/images/html/logo-paypal.png" alt="Paypal" class="v-m" />' 
				);
				foreach ( $_POST ["forma-pago"] as $fp ) {
					?><p><?=$formasPago[$fp]?></p><?php
				}
			}
			?>
		</div>
		<div class="ver-mas">
		<?
		if ($modificar) {
			?><input type="hidden" name="id" value="<?=$articulo->id?>" /><?php }?>
			<input type="hidden" name="__accion"
				value="<?=($modificar?"modificar":"ingresar");?>" /> 
				<?php
				if ($usuario && $usuario->estado === "Incompleto") {
					?><a href="#"
				onclick="$('.user-box .nmodal').click();return false;" class="bt"><?=($modificar?"Actualizar":"Poner a la venta");?></a><?php
				} else {
					?>
					<input type="hidden" name="modo" value="1" /> <input type="submit"
				value="<?=($modificar?"Actualizar":"Poner a la venta");?>"
				class="bt" /><?php }?> <span class="mhm">o</span> <a
				href="<?=(($modificar || $nuevo)?$productoLink:"store/{$this->myuser->seudonimo}");?>"
				title="cancelar e ir al <?=(($modificar || $nuevo)?"producto":"store/{$this->myuser->seudonimo}");?>">cancelar</a>
		</div><?=form_close()?>
</div>

	<div id="consejo1" class="consejos">
		<p>
			<strong>Consejos:</strong>
		</p>
		<ul>
			<li>Utiliza palabras clave que atraigan a los compradores, como
				marcas o categorías.</li>
			<li>Piensa cómo buscarías tú el artículo si lo quisieras comprar y
				escríbelo así.</li>
			<li>Escribe con corrección ortográfica y sin mayúsculas, una buena
				presentación da confianza al comprador.</li>
		</ul>
		<span class="arrow"></span>
	</div>
	<!--consejos-->

	<div id="consejo2" class="consejos">
		<p>
			<strong>Consejos:</strong>
		</p>
		<ul>
			<li>Describe claramente lo que vendes, no omitas anomalías ni
				desperfectos.</li>
			<li>Incluye todo aquello que tú preguntarías si fueras el comprador.</li>
			<li>Escribe con corrección ortográfica y sin mayúsculas, una buena
				presentación da confianza al comprador.</li>
			<li>Puedes añadir descripciones HTML para personalizar tus anuncios.</li>
		</ul>
		<span class="arrow"></span>
	</div>
</div>
<div style="display: none;" id="otrosErrores"></div>