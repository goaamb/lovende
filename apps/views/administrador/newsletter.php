<script type="text/javascript"
	src="<?=base_url()?>assets/js/editor/ckeditor.js"></script>
<script type="text/javascript">
CKEDITOR.config.font_defaultLabel="Arial";
CKEDITOR.config.fontSize_defaultLabel="15";
CKEDITOR.config.contentsCss="<?=base_url()?>assets/css/editorDefault.css";
CKEDITOR.config.width = 650;
CKEDITOR.config.filebrowserBrowseUrl="assets/filemanager/index.html"
CKEDITOR.config.toolbar=[
		                     	[ 'NewPage','DocProps','Preview','Print' ],
		                    	[ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] ,
		                    	 [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] ,
		                    	 [ 'Maximize', 'ShowBlocks' ] ,
		                    	 [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar' ] ,
		                    	[ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton' ],
		                     	 [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv',
			                    	'-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl','Source' ] ,
		                    	 [ 'Format','Font','FontSize' ], [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] ,
		                    	 ['Link','Unlink'],
		                    	 [ 'TextColor','BGColor' ] 
		                    ];
</script>
<div class="wrapper clearfix">
	<header class="cont-cab mbl">
		<h1>Newsletter</h1>
		<p>En esta sección podra enviar correos masivos a usuarios dentro de
			la web que tengan activo en su configuracion la opción de recibir
			mails, o bien a emails externos a la web.</p>
	</header>
	<div class="formA">
		<span class="errorTxt"><?=(isset($errores) && array_search("general-No-Email",$errores)!==false?"No se envio Ningun Mensaje":"")?></span>
		<span class="green"><?=(isset($exito)?"Se envio la petición de emails para que sea procesada.":"")?></span>
		<form id="formNewsletter" enctype="multipart/form-data" method="post">
			<div class="line clearfix">
				<label class="col652">Asunto:</label>
				<div class="recuadro con-consejo">
					<input name="asunto" type="text" class="required enfoque"
						data-consejo="consejo1"
						data-error-required="Ingrese un asunto para su mensaje" />
				</div>
				<span class="errorTxt" id="asuntoError"><?=(isset($errores) && array_search("asunto",$errores)!==false?"Ingrese un asunto para su mensaje":"")?></span>
			</div>
			<div class="line clearfix">
				<label class="col652">Mensaje:</label>
				<div style="display: inline-block;">
					<div class="ckeditor con-consejo" id="mensaje"></div>
					<textarea class="required hidden" data-consejo="consejo2"
						name="mensaje" data-error-required="Ingrese un mensaje"></textarea>
				</div>
				<span class="errorTxt" id="mensajeError"><?=(isset($errores) && array_search("mensaje",$errores)!==false?"Ingrese un mensaje":"")?></span>
			</div>
			<div class="line clearfix d-b mbl">
				<label>Destinatarios</label>
				<p>
					<label class="col652"><input type="radio" name="destino" value="1"
						checked="checked" onclick="cambiarDestino.call(this);" /> Usuarios
						internos, con notificación activa.</label>
				</p>
				<p>
					<label class="col652"><input type="radio" name="destino" value="2"
						onclick="cambiarDestino.call(this);" /> Usuarios externos,
						importados mediante un archivo <a
						href="assets/excel/ejemplo.emails.xlsx" title="Ejemplo de emails"
						target="_blank">excel</a></label>
				</p>
			</div>
			<div class="tipo-precio-box line" style="display: none;">
				<label><input name="excel" type="file"
					data-error-required="Debe ingresar un archivo con el listado de emails" /></label>
			</div>
			<span class="errorTxt" id="excelError"></span>
			<div class="ver-mas line">
				<input type="submit" value="Enviar" class="bt" />
			</div>
		</form>
	</div>
	<header class="cont-cab mbl">
		<h1>Resumen</h1>
		<p>En esta sección podra ver todos los envios ya programados y sus
			respectivos avances.</p>
	</header>
	<div class="formA">
		<div class="line"><?php
		if (isset ( $envios ) && is_array ( $envios ) && count ( $envios ) > 0) {
			foreach ( $envios as $e ) {
				?><p>
				<strong><label>Fecha: </label> <span><?=date("d/m/Y H:i:s",strtotime($e->fecha))?></span>
				<?php
				if ($e->porcentaje < 100) {
					?><a href="#"
					onclick="return cancelarEnviosPendientes(<?=$e->id?>)">Cancelar
						envíos pendientes</a><?php
				}
				?></strong><br /> <label>Asunto: </label> <span><?=$e->asunto?></span><br />
				<label>Mensaje: </label> <span><?=array_shift(explode("\n",strip_tags ( trim($e->mensaje )) ))."..."?></span><br />
				<label>Avance: </label> <span><?=$e->porcentaje?>%</span><br />
			</p><?php
			}
		} else {
			?><p>No se realizaron envíos aún.</p><?php
		}
		?></div>
	</div>
</div>
<div id="consejo1" class="consejos">
	<p>
		<strong>Consejos para que un mensaje no se considere SPAM:</strong>
	</p>
	<ul>
		<li>Usar mayúsculas nada mas para lo mínimo necesario como ser la
			primera letra de la primera palabra, tener muchas palabras en
			mayúsculas puede causar que el mail sea reconocido como spam.</li>
		<li>No uses direcciones de correo electronico o direcciones web largas
			en el asunto.</li>
		<li>No usar frases comerciales simples, como ser: gran oferta,
			novedad, etc; o bien se mas especifico en ellas.</li>
		<li>La Cantidad de caracteres no debe exceder los 255.</li>
	</ul>
	<span class="arrow"></span>
</div>
<div id="consejo2" class="consejos">
	<p>
		<strong>Consejos para que un mensaje no se considere SPAM:</strong>
	</p>
	<ul>
		<li>Puedes usar %nombre% para que se envíe el mensaje con el nombre
			real del destinatario ya sea del sistema o bien del archivo excel
			importado.</li>
		<li>Si copias un mensaje desde Word, revisa que se utilice estilos
			inline (atributo style en un tag), y que no exista el tag style o
			link, por que existen algunas bandejas de correo que no la soportan
			como ser Microsoft Outlook.</li>
		<li>Si copias desde una página web, y este contenido contiene
			imagenes, las referencias a estas imagenes estarán sujetas a esa web,
			es recomendable subir tus propias imágenes, con el icono de
			imagen&rArr;Ver Servidor.</li>
		<li>Si subes una imagen o archivo, no la borres si ya la usaste en
			algun mensaje por que podría causar enlaces rotos en los ya enviados</li>
	</ul>
	<span class="arrow"></span>
</div>
<script type="text/javascript">
var envioMasivo=false;
$("#formNewsletter").on("submit",function(){
	if(envioMasivo){
		$(".nyroModalClose").click();
	}
	var b64=G.base64.encode(CKEDITOR.instances.mensaje.getData());
	$("textarea[name='mensaje']").val(b64);
	$("textarea[name='mensaje']").html(b64);
	if(formItemSubmit.call(this) && !envioMasivo){
		$.nmManual("home/modal/confirmar-envio-masivo");
	}
	return envioMasivo;
	});
function editorUnaVes() {
	if(CKEDITOR ){
		CKEDITOR.replace("mensaje");
	}
	if (CKEDITOR && CKEDITOR.instances.mensaje) {
		CKEDITOR.instances.mensaje.on('focus', function(evt) {
			var desc = $("textarea[name='mensaje']");
			var consejo = desc.data('consejo');
			var ancho = desc.width() + 12;
			var offset = $("#cke_mensaje").offset();
			$('#' + consejo).show().css({
				'top' : offset.top - 3,
				'left' : offset.left + ancho + 20
			});
		});
		CKEDITOR.instances.mensaje.on('blur', function(evt) {
			$('.consejos').hide();
		});
	} else {
		setTimeout(editorUnaVes, 1000);
	}
}
editorUnaVes();
</script>