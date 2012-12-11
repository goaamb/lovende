$(function() {
	$("#formLogin").submit(formItemSubmit);
	$("#formItem").submit(formItemSubmit);
	$(".uploader .cerrar").click(eliminarImagen);
});
function quitarImagen() {
	var c = $(".uploader .cerrar");
	if (c.length > 0) {
		eliminarImagen.call($(".uploader .cerrar")[0])
	}
	return false;
}
function eliminarImagen() {
	var padre = $(this).parent();
	var name = padre.data("name");
	padre.removeAttr("style").removeClass("uploaded").addClass("loading");
	var quien = padre.find("form input[name='quien']").val();
	$.ajax({
		url : "usuario/removeImage",
		data : {
			quien : quien,
			imagen : name
		},
		dataType : "json",
		type : "POST",
		success : function(data) {
			if (data && data.quien) {
				var q = data.quien;
				var d = $("#capaimagen" + q);
				var imagenes = $("#formItem input[name='imagenes']").val();
				imagenes = imagenes.split(",");
				for ( var i = 0; i < imagenes.length; i++) {
					if (imagenes[i] === d.data("name")) {
						imagenes.splice(i, 1);
						i--;
					}
				}
				$("#formItem input[name='imagenes']").val(imagenes.join(","));
				d.removeClass("loading").removeClass("progress");
				d.find("span").css("filter", "alpha(opacity=100)").css(
						"opacity", "1");
				d.find(".block").hide();
				var st = d.find(".spanTxt");
				st.html(st.data("html"));
			}
		}
	});
}
function imageReady(json) {
	if (json) {
		var d = $("#capaimagen" + json.quien);
		if (json.quien) {
			if (!json.error) {
				d.addClass("uploaded");
				d.removeClass("loading").removeClass("progress").css(
						"background",
						"transparent url(" + json.path + json.name + "."
								+ json.ext + ") center center no-repeat");
				d.find("span").css("filter", "alpha(opacity=0)").css("opacity",
						"0");
				var st = d.find(".spanTxt");
				st.html(st.data("html"));
				d.data("name", json.name + "." + json.ext);
				d.data("thumb", json.path + json.name + ".thumb." + json.ext);
				$("#errorUploading").html("");
				$("#formItem input[name='imagenes']").val(
						json.name + "." + json.ext);
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
}
function validar2Campos(a, b) {
	var f = this.form;
	if (f) {
		var x = f[a].value;
		var y = f[b].value;
		if (G.util.trim(x) === "" || G.util.trim(y) === "") {
			return false;
		}
	}
	return true;
}
function validarNombreApellido() {
	return validar2Campos.call(this, "nombre", "apellido");
}
function validarDNI() {
	return validar2Campos.call(this, "dni-num", "dni-letra");
}
function validarLocacion() {
	return validar2Campos.call(this, "pais", "ciudad");
}

function cargarCiudades() {
	var f = this.form;
	if (f && f.pais && f.pais.value && f.ciudad) {
		$(f.ciudad).load("usuario/cargarCiudades", {
			pais : f.pais.value
		});
	}
}
$(document).on(
		"ready",
		function() {
			if (G.cookie.get("datos_guardados")) {
				$.nmManual("home/modal/perfil-actualizado/usuario/"
						+ G.cookie.get("datos_guardados"));
				G.cookie.set("datos_guardados", false, -1, "/");
				setTimeout("$('.nyroModalClose').click();", 4000);
			}
		});