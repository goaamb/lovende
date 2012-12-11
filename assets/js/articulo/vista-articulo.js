$(function() {
	$("#formOfertar").submit(formItemSubmit);
	if (G.cookie.get("cantidad_guardada")) {
		$.nmManual("home/modal/cantidad-modificada/articulo/"
				+ G.cookie.get("cantidad_guardada"));
		G.cookie.set("cantidad_guardada", "", -1, "/");
		setTimeout("$('.nyroModalClose').click()", 4000);
	}
});

function enviarNotaAclarativa(articulo) {
	var t = G.dom.$("notaAclarativaModal");
	if (t) {
		$.ajax({
			url : "articulo/adicionarNota",
			data : {
				articulo : articulo,
				nota : t.value
			},
			type : "post",
			success : function() {
				location.href = location.href;
			}
		});
	}
}
function cambiarCantidad() {
	$("#formComprar input[name='cantidad' ]").val(this.value);
}
function enviarPuja() {
	var f = this;
	if (validarFormulario.call(f)) {
		$(f.oferta).attr("disabled", "disabled");
		$(f).find("input[type='button']").hide();
		$.ajax({
			url : "articulo/enviarPuja",
			data : {
				a : f.articulo.value,
				oferta : f.oferta.value
			},
			type : "post",
			success : function(data) {
				f.oferta.value = "";
				$(f.oferta).removeAttr("disabled");
				$(f).find("input[type='button']").show();
			},
			error : function() {
				$(f.oferta).removeAttr("disabled");
				$(f).find("input[type='button']").show();
			}
		});
	}
}

function darSeudonimo() {
	/*
	 * var c = G.cookie.get("ci_session"); if (c) { var p =
	 * /s:7:"usuario";s:[^:]+:"([^"]+)"/i; var m = p.exec(c); if (m && m.length >
	 * 1) { return m[1]; } } return false;
	 */
	return SeudonimoUsuario;
}

function parseOfertas(str, articulo) {
	var x = false;
	try {
		eval("x=" + G.base64.decode(str));
	} catch (e) {
	}
	if (x && x.length > 0) {
		var s = darSeudonimo();
		$("#montoFinal").html(formato_moneda(x[0].monto_automatico));
		$("#ofertasArticulo").html(x.length);

		var pm = 0;
		if (s == x[0].seudonimo) {
			pm = parseFloat(x[0].monto) + 0.5;
		} else {
			pm = parseFloat(x[0].monto_automatico) + 0.5;
		}

		pm = isNaN(pm) ? 0 : pm;
		$("#pujaMinima").html(formato_moneda(pm));
		$("#cabeceraPerfil").load(
				"articulo/darCabecera/" + articulo + "?rand=" + Math.random());
		location.hash = "#top";
		$("#campoOferta").data("min-value", pm);
	}
	return x;
}

function formato_moneda(m) {
	m = parseFloat(m);
	m = isNaN(m) ? 0 : m;
	var e = "" + Math.floor(m);
	var d = Math.round((m - e) * 100) / 100;
	d = ("" + d).split(".").pop();
	d = d.substring(0, 2);
	if (d.length == 1) {
		d += "0";
	}
	if (e.length > 3) {
		e = e.substring(0, e.length - 3) + "." + e.substring(e.length - 3);
	}
	return e + "," + d;
}
function maxArrayValue(a) {
	for ( var i = 0; i < a.length; i++) {
		if (a[i]) {
			return [ i, a[i] ];
		}
	}
	return false;
}
function contadorInverso(tiempo, elemento) {
	if (tiempo > 0) {
		tiempo = parseInt(tiempo, 10);
		tiempo = isNaN(tiempo) ? 0 : tiempo;
		var dias = Math.floor(tiempo / 86400);
		var horas = Math.floor((tiempo % 86400) / 3600);
		var minutos = Math.floor((tiempo % 3600) / 60);
		var segundos = Math.floor((tiempo % 60));
		var d = [ dias, horas, minutos, segundos ];
		var t = [ "d", "h", "m", "s" ];
		var fm = maxArrayValue(d);
		if (fm) {
			var p = fm[0];
			var tm = t[p];
			t.splice(p, 1);
			d.splice(p, 1);
			fm = fm[1];
			var sm = maxArrayValue(d);
			var ts;
			if (sm) {
				p = sm[0];
				ts = t[p];
				t.splice(p, 1);
				d.splice(p, 1);
				sm = sm[1];
			}
			$("#" + elemento).html(fm + tm + (sm ? " " + sm + ts : ""));
		}
		setTimeout("contadorInverso('" + (tiempo - 1) + "', '" + elemento
				+ "')", 1000);
	} else {
		// location.href = location.href.split("#").shift();
	}
}
var maxLimint = 10;
var countLimit = maxLimint;
var ofertasVal = false;
function actualizarPuja(articulo) {
	if (countLimit > 0) {
		$.ajax({
			url : "articulo/actualizarPuja",
			data : {
				a : articulo,
				code : ofertasVal
			},
			dataType : "json",
			type : "post",
			success : function(data) {
				if (data.resultado) {
					ofertasVal = data.resultado;
					parseOfertas(ofertasVal, articulo);
					actualizarPuja(articulo);
					countLimit = maxLimint;
				} else {
					countLimit--;
					setTimeout("actualizarPuja(" + articulo + ")", 60000);
				}
			},
			error : function(a, t) {
				countLimit--;
				setTimeout("actualizarPuja(" + articulo + ")", 60000);
			}
		});
	} else {
		location.href = location.href.split("#").shift();
	}
}
var ultimaPujaObj = false;
function ultimaPuja(articulo) {
	if (countLimit > 0) {
		$.ajax({
			url : "articulo/ultimaPuja",
			data : {
				a : articulo,
				ultima : ultimaPujaObj ? ultimaPujaObj["id"] : 0
			},
			dataType : "json",
			type : "post",
			success : function(data) {
				if (data.resultado) {
					ultimaPujaObj = data.resultado;
					parseOfertas(ultimaPujaObj, articulo);
					ultimaPuja(articulo);
					countLimit = maxLimint;
				} else {
					countLimit--;
					setTimeout("ultimaPuja(" + articulo + ")", 60000);
				}
			},
			error : function(a, t) {
				countLimit--;
				setTimeout("ultimaPuja(" + articulo + ")", 60000);
			}
		});
	} else {
		location.href = location.href.split("#").shift();
	}
}
function desplegarPujas(a) {
	if (a) {
		var d = 'articulo/modal/pujas/ofertas/' + a + '?r=' + Math.random();
		$.nmManual(d);
	}
	return false;
}

function calcularContenidoTamaño() {
	var h = this.contentWindow.document.body.clientHeight + 30;
	if (h > 100) {
		h = this.contentWindow.document.body.scrollHeight;
	}
	this.style.height = h + 'px';
}

function confirmarCompra(a) {
	$.nmManual("articulo/modal/confirmar-compra/articulo/" + a + "/"
			+ $(".formA input[name='cantidad']").val());
	return false;
}