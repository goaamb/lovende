var nuevos = 0;
$(function() {
	iniciarCategorias(1);
	$(".uploader .cerrar").click(eliminarImagen);
	$("#formItem").submit(formItemSubmit);
	$("#formFake").submit(formItemSubmit);
	$("#formFake input[name='titulo']").change(onChangeField);
	$("#formFake textarea[name='descripcion']").change(onChangeField);
	$('a.reset-line-categories').click(function() {
		cambiar.call({
			value : ""
		}, "categoria");
		return false;
	});
	$('input.ofertasInferioresTrigger').change(function() {
		if ($(this).is(':checked')) {
			$('#ofertasInferiores').css("display", "inline-block");
			$("input[name='precio-oferta-inferior']").addClass("required");
		} else {
			$('#ofertasInferiores').hide();
			$("input[name='precio-oferta-inferior']").removeClass("required");
		}
	});
	if (G.nav.isIE && G.nav.version < 9) {
		G.util.includeCSS("assets/css/nuevoie.css");
	}
	$('a.reset-line-categories').click(function() {
		$("div.line-category.hidden").removeAttr("style");
		return false;
	});
	actualizarEditor();
	editorUnaVes();
});
function editorUnaVes() {
	if (CKEDITOR && CKEDITOR.instances.descripcion) {
		CKEDITOR.instances.descripcion.on('focus', function(evt) {
			var desc = $("#formFake textarea[name='descripcion']");
			var consejo = desc.data('consejo');
			var ancho = desc.width() + 12;
			var offset = $("#cke_descripcion").offset();
			$('#' + consejo).show().css({
				'top' : offset.top - 3,
				'left' : offset.left + ancho + 20
			});
		});
		CKEDITOR.instances.descripcion.on('blur', function(evt) {
			$('.consejos').hide();
		});
	} else {
		setTimeout(editorUnaVes, 1000);
	}
}
function actualizarEditor() {
	if (CKEDITOR && CKEDITOR.instances.descripcion) {
		CKEDITOR.instances.descripcion.updateElement();
		$("#formItem textarea[name='descripcion']").val(
				CKEDITOR.instances.descripcion.getData());
		$("#formItem textarea[name='descripcion']").html(
				CKEDITOR.instances.descripcion.getData());
	}
	if ($("input[name='modo']").val() == "2") {
		if (alMenosUnValor()) {
			$("input[name='imagenes']").val("#");
		} else {
			$("input[name='imagenes']").val("");
		}
	} else {
		var x = $("input[name='imagenes']").val();
		if (x == "#") {
			$("input[name='imagenes']").val("");
		}
	}
	var x = $("#formItem input[name='cantidad-precio']");
	if ($("#formItem").find("input[name='tipo-precio']:checked").val() !== "precio-cantidad-box"
			&& G.util.trim(x.val()) === "") {
		x.val("1");
	}
	setTimeout(actualizarEditor, 1000);
}

function onChangeField() {
	cambiar.call(this, this.name);
}

function validFormFake() {
	$('#formItem').submit();
	return false;
}
function alMenosUnValor() {
	var files = $("input[type='file']");
	for ( var i = 0; i < files.length; i++) {
		if ($(files[i]).val()) {
			return true;
		}
	}

	return false;
}
function validFormItem() {
	if ($("input[name='modo']").val() == "2") {
		if (alMenosUnValor()) {
			$("#imagenesFileError").hide();
		} else {
			$("#imagenesFileError").show();
			return false;
		}
	}
	var nc = $("input.c-b:checked").length;
	nc += $("input[name='envio_local']:checked").length;
	if (nc <= 0) {
		$("#gastosError").html($("#gastosError").data("error"));
		return false;
	}
	var x = $("input[name='forma-pago[]']:checked");
	if (x.length <= 0) {
		$("#forma-pagoError").html($("#forma-pagoError").data("error"));
		return false;
	}
	CKEDITOR.instances.descripcion.updateElement();
	$("textarea[name='descripcion']").val(
			CKEDITOR.instances.descripcion.getData());
	if (!completoUsuario) {
		$(".user-box .nmodal").click();
	}
	return completoUsuario;
}
function eliminarImagen() {
	var padre = $(this).parent();
	var name = padre.data("name");
	padre.removeAttr("style").removeClass("uploaded").addClass("loading");
	var quien = padre.find("form input[name='quien']").val();
	$.ajax({
		url : "product/remove/" + name,
		data : {
			quien : quien
		},
		dataType : "json",
		type : "POST",
		success : function(data) {
			if (data && data.quien) {
				var q = parseInt(data.quien, 10);
				q = (isNaN(q) ? 1 : q);
				var d = $("#capaimagen" + q);
				d.data("name", "");
				d.data("thumb", "");
				d.removeClass("loading").removeClass("progress");
				d.find("span").css("filter", "alpha(opacity=100)").css(
						"opacity", "1");
				d.find(".block").hide();
				var st = d.find(".spanTxt");
				st.html(st.data("html"));
			}
		}
	});
	verificarOrden();
}
function imageReady(json) {
	if (json) {
		var d = $("#capaimagen" + json.quien);
		if (json.quien) {
			if (!json.error) {
				d.addClass("uploaded");
				d.removeClass("loading").removeClass("progress").css(
						"background",
						"transparent url(" + json.path + json.name + ".thumb."
								+ json.ext + ") center center no-repeat");
				d.find("span").css("filter", "alpha(opacity=0)").css("opacity",
						"0");
				var st = d.find(".spanTxt");
				st.html(st.data("html"));
				d.data("name", json.name + "." + json.ext);
				d.data("thumb", json.path + json.name + ".thumb." + json.ext);
				$("#errorUploading").html("");
			} else {
				d.removeClass("loading").removeClass("progress");
				$("#errorUploading").html(json.mensaje);
			}
			var p = new Progreso({
				padre : "capaimagen" + json.quien
			});
			p.esconder();
		} else {
			d.removeClass("loading").removeClass("progress");
			d.find("span").css("filter", "alpha(opacity=100)").css("opacity",
					"1");
			var st = d.find(".spanTxt");
			st.html(st.data("html"));
		}
	}
	verificarOrden();
}
var ntimer = false;
function verificarOrden() {
	clearTimeout(ntimer);
	var t = $(".formA .uploader");
	var u = t.not(".loading").not(".progress");
	if (u.length === t.length) {
		for ( var i = 0; i < t.length; i++) {
			var d = G.dom.$("capaimagen" + (i + 1));
			if (d && (!$(d).data("name") || !$(d).data("thumb"))) {
				for ( var j = i + 1; j < t.length; j++) {
					var z = G.dom.$("capaimagen" + (j + 1));
					var th = $(z).data("thumb");
					var na = $(z).data("name");
					if (th && na) {
						$(d).data("name", na).data("thumb", th).css(
								"background",
								"transparent url(" + th
										+ ") center center no-repeat")
								.addClass("uploaded").find("span").css(
										"opacity", "0").css("filter",
										"alpha(opacity=0)");
						$(d).find(".block").show();
						$(z).data("name", "").data("thumb", "").removeClass(
								"uploaded").find("span").css("opacity", "1")
								.css("filter", "alpha(opacity=100)");
						$(z).find(".block").hide();
						$(z).removeAttr("style");
						break;
					}
				}
			}
		}
		var imagenes = [];
		for ( var i = 0; i < t.length; i++) {
			var d = G.dom.$("capaimagen" + (i + 1));
			if (d && $(d).data("name") && $(d).data("thumb")) {
				imagenes.push($(d).data("name"));
			}
		}
		cambiar.call({
			value : imagenes.join(",")
		}, "imagenes");
	} else {
		ntimer = setTimeout("verificarOrden()", 500);
	}
}
function startUpload() {
	clearTimeout(ntimer);
}
function iniciarCategorias(nivel) {
	if (!nivel) {
		nivel = 1;
	}
	$('div#lista' + nivel + ' a.trigger').click(function() {
		$(this).hide();
		$(this).siblings('div.cat').slideDown();
		return false;
	});
	$('div#lista' + nivel + ' div.cat a').click(aplicarClickCategorias);
}
function aplicarClickCategorias() {
	var el = $(this);
	var nivel = (parseInt(el.data('nivel'), 10) + 1);
	nivel = (isNaN(nivel) ? 1 : nivel);
	var id = el.data('id');
	var padre = el.parent('li').parent('ul').parent('div.cat').parent(
			'div.line-category');
	padre.children('div.cat').hide();
	var category = el.data('value');
	padre.children('span.choice').text(category);
	padre.addClass('l-cat-selected');
	if (nivel < 4) {
		$("#lista" + nivel)
				.html(
						"<img src='assets/images/ico/ajax-loader-see-more.gif' alt='...'/>")
				.show();
	}
	$("#lista" + nivel).load("product/getCategory/" + id, function(response) {
		if ($.trim(response) !== "") {
			if (nivel < 4) {
				padre.next('div.postCat').show();
				padre.next().next('div.line-category').removeClass('hidden');
				iniciarCategorias(nivel);
				return;
			}
		} else {
			$("#lista" + nivel).hide();
		}
		cambiar.call({
			value : id
		}, "categoria");
		$("#categoriaError").html("");
		$("a.reset-line-categories").parent().show();
	});

	return false;
}

function processResponse() {
	var w = this.contentWindow;
	if (w) {
		var res = w.document.documentElement.innerHTML;
		var json = false;
		try {
			res = res.replace(/<[a-zA-Z]\s*[^>]*>/ig, "", res);
			res = res.replace(/<\/[a-zA-Z]\s*[^>]*>/ig, "", res);
			eval("json=" + res);
		} catch (e) {
		}
		if (json && this.formRequest && this.formRequest.onjsonready
				&& this.formRequest.onjsonready.call) {
			this.formRequest.onjsonready(json);
		}
	}
	return true;
}

function validarPrecioRechazo() {
	var f = this.form;
	if (f && f["rechazar"] && f["rechazar"].checked
			&& !f["tipo-precio"][1].checked && G.util.trim(this.value) === "") {
		return false;
	}
	return true;
}
function validarPrecioCantidad() {
	return validarPrecio.call(this, "precio-cantidad-box");
}
function validarPrecioOferta() {
	return validarPrecio.call(this, "precio-fijo-box");
}
function validarPrecioSubasta() {
	return validarPrecio.call(this, "subasta-box");
}
function validarPrecio(valor) {
	var f = this.form;
	if (f && f["tipo-precio"]) {
		var ftp = f["tipo-precio"];
		var ch = false;
		for ( var i = 0; i < ftp.length; i++) {
			if (ftp[i].checked) {
				ch = ftp[i];
				break;
			}
		}
		if (ch && ch.value == valor && G.util.trim(this.value) === "") {
			return false;
		}
	}
	return true;
}
function cambiar(quien) {
	$("#formItem input[name='" + quien + "']").val(this.value);
}

function cambiarModoClasico() {
	$('#uploader1').hide();
	$('#uploader2').show();
	$("input[name='modo']").val("2");
	$("#formItem").attr("enctype", "multipart/form-data");
	return false;
}

function cambiarModoModerno() {
	$('#uploader2').hide();
	$('#uploader1').show();
	$("input[name='modo']").val("1");
	$("#formItem").attr("enctype", "application/x-www-form-urlencoded");
	return false;
}