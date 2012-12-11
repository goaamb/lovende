$(document)
		.on(
				"ready",
				function() {
					$('.closeThisAction').click(function() {
						var f = $(this).data("action");
						try {
							eval(f + ".call(this)");
						} catch (e) {
						}
						return false;
					});
					var ot = $(".OwnTextBox");
					if (ot) {
						for ( var i = 0; i < ot.length; i++) {
							G.OwnTextBox.convert(ot[i]);
						}
					}

					$(".orderby").on("click", function() {
						var ob = $(this).data("orderby");
						var a = $(this).data("asc");
						var w = $(this).data("who");
						if (ob && w) {
							var url = G.url._setGET("orderby", ob);
							url = G.url._setGET("asc", a, url);
							location.href = G.url._setGET("who", w, url);
						}
					});

					$('.showMiperfil').mouseover(
							function() {
								var $this = $(this);
								var desplegable = $('#miperfil-desplegable');
								var offset = $this.offset();
								var w = $this.outerWidth();
								var w2 = w + 22;
								var w3 = (w2 - w);
								var hh = $('header.header').outerHeight();
								$this.css({
									'position' : 'relative',
									'z-index' : '300'
								});
								$('#miperfil-desplegable header')
										.height(hh - 1).width(w + 22);
								clearTimeout(tiempoDesplegable);
								desplegable.css({
									'left' : offset.left - w3
								}).show();
								$(this).find("a.sep").css({
									'color' : '#035F8D',
									'border-color' : '#ffe8dd'
								});
							});
					$('#miperfil-desplegable').mouseover(function() {
						$('.showMiperfil').css({
							'position' : 'relative',
							'z-index' : '300'
						});
						clearTimeout(tiempoDesplegable);
						$(this).css('display', 'block');
						$('.showMiperfil').find("a.sep").css({
							'color' : '#035F8D',
							'border-color' : '#ffe8dd'
						});
					});
					$('.showMiperfil')
							.mouseout(
									function() {
										$(this).css({
											'position' : 'static',
											'z-index' : '1'
										});
										clearTimeout(tiempoDesplegable);
										tiempoDesplegable = setTimeout(
												"$('#miperfil-desplegable').hide();$('.showMiperfil').find('a.sep').css({'color' : '#fff','border-color' : '#E3554F'});",
												10);

									});
					$('#miperfil-desplegable')
							.mouseout(
									function() {
										$('.showMiperfil').css({
											'position' : 'static',
											'z-index' : '1'
										});
										clearTimeout(tiempoDesplegable);
										tiempoDesplegable = setTimeout(
												"$('#miperfil-desplegable').hide();$('.showMiperfil').find('a.sep').css({'color' : '#fff','border-color' : '#E3554F'});",
												10);
									});
					verificarOrdenamiento();

				});
var tiempoDesplegable = false;
function focusCriterio() {
	var c = $("input[name='criterio']");
	if (c.val() && c.length > 0) {
		c[0].focus();
	}
}
function verificarOrdenamiento() {
	var who = G.url._GET("who");
	if (who) {
		var g = G.dom.$(who);
		if (g) {
			location.hash = who;
		}
		var gob = G.url._GET("orderby");
		$("#" + who).next("table").find("th").removeClass("select");
		var a = G.url._GET("asc");
		switch (a) {
		case "asc":
			a = "desc";
			break;
		default:
			a = "asc";
			break;
		}
		$("#" + who).next("table").find("th[data-orderby='" + gob + "']")
				.addClass("select " + a).data("asc", a);
	} else {
		var s = $("th[data-default='true']").addClass("select");
		for ( var i = 0; i < s.length; i++) {
			var a = $(s[i]).data("asc");
			if (a) {
				$(s[i]).addClass(a);
			}
		}
	}
}
function cerrarMarquesinaHome() {
	G.cookie.set("marquesinaHome", "1", 365, "/");
}

function crearArticuloNoComprados(ab, a, an) {
	var tr = G.dom.create("tr");
	var td = G.dom.create("td");
	tr.appendChild(td);
	td.className = "td-item";
	var d = G.dom.create("div");
	d.className = "imagen td-imagen";
	td.appendChild(d);
	var img = G.dom.create("img");
	img.src = a.imagen;
	img.alt = a.titulo;
	var vw = 60;
	var vh = 60;
	var w = parseInt(a.width, 10);
	w = isNaN(w) ? 0 : w;
	var h = parseInt(a.height, 10);
	h = isNaN(h) ? 0 : h;

	var nw = vw;
	var nh = Math.ceil(nw * h / w);
	if (nh > vh) {
		nh = vh;
		nw = Math.ceil(nh * w / h);
	}
	img.width = nw;
	img.height = nh;
	d.appendChild(img);
	var al = G.dom.create("a");
	al.href = a.furl;

	al.title = a.titulo;
	al.innerHTML = a.titulo;
	td.appendChild(al);
	td = G.dom.create("td");
	tr.appendChild(td);
	td.className = "t-r";
	td.innerHTML = a.fecha_terminado;
	td = G.dom.create("td");
	tr.appendChild(td);
	td.className = "t-r";
	td.innerHTML = a.precio;
	return tr;
}
function crearArticuloNoVendidos(ab, a, an) {
	var tr = G.dom.create("tr");
	var td = G.dom.create("td");
	tr.appendChild(td);
	td.className = "td-item";
	var d = G.dom.create("div");
	d.className = "imagen td-imagen";
	td.appendChild(d);
	var img = G.dom.create("img");
	img.src = a.imagen;
	img.alt = a.titulo;
	var vw = 60;
	var vh = 60;
	var w = parseInt(a.width, 10);
	w = isNaN(w) ? 0 : w;
	var h = parseInt(a.height, 10);
	h = isNaN(h) ? 0 : h;

	var nw = vw;
	var nh = Math.ceil(nw * h / w);
	if (nh > vh) {
		nh = vh;
		nw = Math.ceil(nh * w / h);
	}
	img.width = nw;
	img.height = nh;
	d.appendChild(img);
	var al = G.dom.create("a");
	al.href = a.furl;

	al.title = a.titulo;
	al.innerHTML = a.titulo;
	td.appendChild(al);
	td = G.dom.create("td");
	tr.appendChild(td);
	td.className = "t-r";
	td.innerHTML = a.tipo;
	td = G.dom.create("td");
	tr.appendChild(td);
	td.className = "t-r";
	td.innerHTML = a.fecha_terminado + a.purl;
	td = G.dom.create("td");
	tr.appendChild(td);
	td.className = "t-r";
	td.innerHTML = a.precio;
	return tr;
}

function crearArticuloEnCompra(ab, a, an) {
	var tr = G.dom.create("tr");
	var td = G.dom.create("td");
	tr.appendChild(td);
	td.className = "td-item";
	var d = G.dom.create("div");
	d.className = "imagen td-imagen";
	td.appendChild(d);
	var img = G.dom.create("img");
	img.src = a.imagen;
	img.alt = a.titulo;
	var vw = 60;
	var vh = 60;
	var w = parseInt(a.width, 10);
	w = isNaN(w) ? 0 : w;
	var h = parseInt(a.height, 10);
	h = isNaN(h) ? 0 : h;

	var nw = vw;
	var nh = Math.ceil(nw * h / w);
	if (nh > vh) {
		nh = vh;
		nw = Math.ceil(nh * w / h);
	}
	img.width = nw;
	img.height = nh;
	d.appendChild(img);
	var al = G.dom.create("a");
	al.href = a.furl;

	al.title = a.titulo;
	al.innerHTML = a.titulo;
	td.appendChild(al);

	td = G.dom.create("td");
	tr.appendChild(td);
	td.className = "t-r";
	td.innerHTML = a.estadoArticulo;
	td = G.dom.create("td");
	tr.appendChild(td);
	td.className = "t-r";
	td.innerHTML = a.tiempo;
	td = G.dom.create("td");
	tr.appendChild(td);
	td.className = "t-r";
	td.innerHTML = a.precio;
	return tr;
}

function crearArticuloEnVenta(ab, a, an) {
	var tr = G.dom.create("tr");
	var td = G.dom.create("td");
	tr.appendChild(td);
	td.className = "td-item";
	var d = G.dom.create("div");
	d.className = "imagen td-imagen";
	td.appendChild(d);
	var img = G.dom.create("img");
	img.src = a.imagen;
	img.alt = a.titulo;
	var vw = 60;
	var vh = 60;
	var w = parseInt(a.width, 10);
	w = isNaN(w) ? 0 : w;
	var h = parseInt(a.height, 10);
	h = isNaN(h) ? 0 : h;

	var nw = vw;
	var nh = Math.ceil(nw * h / w);
	if (nh > vh) {
		nh = vh;
		nw = Math.ceil(nh * w / h);
	}
	img.width = nw;
	img.height = nh;
	d.appendChild(img);
	var al = G.dom.create("a");
	al.href = a.furl;

	al.title = a.titulo;
	al.innerHTML = a.titulo;
	td.appendChild(al);

	td = G.dom.create("td");
	tr.appendChild(td);
	td.className = "t-r";
	td.innerHTML = a.seguidores;
	td = G.dom.create("td");
	tr.appendChild(td);
	td.className = "t-r";
	if (a.tipo == "Fijo") {
		td.innerHTML = a.cantidadOfertas;
	} else if (a.tipo == "Cantidad") {
		td.innerHTML = a.cantidad;
	} else {
		td.innerHTML = a.cantidadPujas;
	}
	td = G.dom.create("td");
	tr.appendChild(td);
	td.className = "t-r";
	td.innerHTML = a.tiempo;
	td = G.dom.create("td");
	tr.appendChild(td);
	td.className = "t-r";
	td.innerHTML = a.precio;
	return tr;
}

function crearArticuloComprado(ab, a, an) {
	var tr = G.dom.create("tr");
	var td = G.dom.create("td");
	tr.appendChild(td);
	td.className = "td-item";
	var d = G.dom.create("div");
	d.className = "imagen td-imagen";
	td.appendChild(d);
	var img = G.dom.create("img");
	img.src = a.imagen;
	img.alt = a.titulo;
	var vw = 60;
	var vh = 60;
	var w = parseInt(a.width, 10);
	w = isNaN(w) ? 0 : w;
	var h = parseInt(a.height, 10);
	h = isNaN(h) ? 0 : h;

	var nw = vw;
	var nh = Math.ceil(nw * h / w);
	if (nh > vh) {
		nh = vh;
		nw = Math.ceil(nh * w / h);
	}
	img.width = nw;
	img.height = nh;
	d.appendChild(img);
	var al = G.dom.create("a");
	al.href = a.furl;

	al.title = a.titulo;
	al.innerHTML = a.titulo;
	td.appendChild(al);

	td = G.dom.create("td");
	td.className = "t-r";
	if (!ab || (ab && ab.comprador !== a.comprador)) {
		td.innerHTML = a.estado1;
	}
	tr.appendChild(td);
	td = G.dom.create("td");
	td.className = "t-r";
	if (!ab || (ab && ab.comprador !== a.comprador)) {
		td.innerHTML = a.estado2;
	}
	tr.appendChild(td);
	td = G.dom.create("td");
	td.className = "t-r";
	if (!ab || (ab && ab.comprador !== a.comprador)) {
		td.innerHTML = a.estado3;
	}
	tr.appendChild(td);
	td = G.dom.create("td");
	td.className = "t-r";
	td.innerHTML = a.estado4;
	tr.appendChild(td);
	td = G.dom.create("td");
	td.className = "t-r";
	td.innerHTML = a.totalMonto;
	tr.appendChild(td);
	if (an && an.comprador === a.comprador) {
		tr.className = "no-border";
	}

	return tr;
}

function crearArticuloVendido(ab, a, an) {
	var tr = G.dom.create("tr");
	var td = G.dom.create("td");
	tr.appendChild(td);
	td.className = "td-item";
	var d = G.dom.create("div");
	d.className = "imagen td-imagen";
	td.appendChild(d);
	var img = G.dom.create("img");
	img.src = a.imagen;
	img.alt = a.titulo;
	var vw = 60;
	var vh = 60;
	var w = parseInt(a.width, 10);
	w = isNaN(w) ? 0 : w;
	var h = parseInt(a.height, 10);
	h = isNaN(h) ? 0 : h;

	var nw = vw;
	var nh = Math.ceil(nw * h / w);
	if (nh > vh) {
		nh = vh;
		nw = Math.ceil(nh * w / h);
	}
	img.width = nw;
	img.height = nh;
	d.appendChild(img);
	var al = G.dom.create("a");
	al.href = a.furl;

	al.title = a.titulo;
	al.innerHTML = a.titulo;
	td.appendChild(al);
	if (a.tipo == "Cantidad") {
		td.innerHTML += a.cantidad;
	}

	td = G.dom.create("td");
	td.className = "t-r";
	if (!ab || (ab && ab.comprador !== a.comprador)) {
		td.innerHTML = a.estado1;
	}
	tr.appendChild(td);
	td = G.dom.create("td");
	td.className = "t-r";
	if (!ab || (ab && ab.comprador !== a.comprador)) {
		td.innerHTML = a.estado2;
	}
	tr.appendChild(td);
	td = G.dom.create("td");
	td.className = "t-r";
	if (!ab || (ab && ab.comprador !== a.comprador)) {
		td.innerHTML = a.estado3;
	}
	tr.appendChild(td);
	td = G.dom.create("td");
	td.className = "t-r";
	td.innerHTML = a.estado4;
	tr.appendChild(td);
	td = G.dom.create("td");
	td.className = "t-r";
	td.innerHTML = a.totalMonto;
	tr.appendChild(td);
	if (an && an.comprador === a.comprador) {
		tr.className = "no-border";
	}

	return tr;
}

function crearArticulo(a) {
	var d = G.dom.create("div");
	d.className = "item clearfix";
	var al = G.dom.create("a");
	al.href = a.furl;
	al.title = a.titulo;
	var s = G.dom.create("span");
	s.className = "imagen";
	G.util.parse({
		backgroundColor : "white",
		backgroundImage : "url(" + a.imagen + ")",
		backgroundRepeat : "no-repeat",
		backgroundPosition : "top right",
		backgroundAttachment : "scroll",
		width : "140px",
		height : a.height + "px"
	}, s.style);
	al.appendChild(s);
	d.appendChild(al);
	var d1 = G.dom.create("div");
	d1.className = "meta";
	d.appendChild(d1);
	var p = G.dom.create("p");
	var st = G.dom.create("strong");
	p.appendChild(st);
	d1.appendChild(p);

	if (a.tipo == "Fijo" || a.tipo == "Cantidad") {
		st.innerHTML = a.precio;
	} else {
		st.innerHTML = a.mayorPuja;
	}
	p = G.dom.create("p");
	d1.appendChild(p);
	if (a.tipo == "Fijo") {
		p.innerHTML = a.textoOferta;
	}
	if (a.tipo == "Cantidad") {
		p.innerHTML = a.textoOferta;
	} else if (a.tipo == "Subasta") {
		var x = parseInt(a.cP, 10);
		if (x > 0) {
			p.innerHTML = a.cantidadPujas;
		}
	}
	p = G.dom.create("p");
	p.className = "grey";
	d1.appendChild(p);
	p.innerHTML = a.tiempo;
	var ul = G.dom.create("ul");
	var li = G.dom.create("li");
	var h2 = G.dom.create("h2");
	al = G.dom.create("a");
	al.href = a.furl;
	al.title = a.titulo;
	al.innerHTML = a.titulo;
	h2.appendChild(al);
	li.appendChild(h2);
	ul.appendChild(li);
	li = G.dom.create("li");
	li.className = "grey";
	li.innerHTML = a.pais_nombre;
	ul.appendChild(li);
	d.appendChild(ul);

	return d;
}
function aceptarOferta(o, a) {
	return requestOferta.call(this, "articulo/aceptarOferta", o, a, true);
}
function rechazarOferta(o, a) {
	return requestOferta.call(this, "articulo/rechazarOferta", o, a);
}

function requestOferta(url, o, a, aceptar) {
	var t = this;
	if (url) {
		$.ajax({
			url : url,
			data : {
				oferta : o,
				articulo : a
			},
			type : "post",
			success : function() {
				if (aceptar) {
					location.href = location.href.split("#").shift();
				} else {
					var tipo = $(t).data("articulo");
					if (tipo == "Fijo") {
						tipo = "ofertas";
					} else {
						tipo = "pujas";
					}
					$.nmManual("articulo/modal/" + tipo + "/ofertas/" + a);
				}
			}
		});
	}
	return false;
}
function verMasArticulos(tipo, p, s) {
	if (!p) {
		p = 1;
	}
	p = parseInt(p, 10);
	p = isNaN(p) ? 1 : p;
	var c = G.url._GET("criterio");
	var co = {
		pagina : p + 1
	};
	if (c) {
		co["criterio"] = c;
	}
	if (s) {
		co["section"] = s;
	}
	if (usuarioID) {
		co["usuario"] = usuarioID;
	}
	co["orden"] = G.url._GET("orden");
	co["ubicacion"] = G.url._GET("ubicacion");
	co["categoria"] = G.url._GET("categoria");
	var d = false;
	switch (tipo) {
	case "home":
		d = "home/verMas";
		break;

	default:
		break;
	}

	if (d) {
		$("p.ver-mas img").show();
		$("p.ver-mas a").hide();
		$.ajax({
			url : d,
			type : "post",
			data : co,
			dataType : "json",
			success : function(data) {
				if (data && data.articulos) {
					var da = data.articulos;
					for ( var i = 0; i < da.length; i++) {
						var a = crearArticulo(da[i]);
						var d = $("div.last-child");
						d.removeClass("last-child").after(a);
						$(a).addClass("last-child");
						$(a).hide().slideDown("slow");
					}
					var ap = $("p.ver-mas a:first");
					var oc = ap.attr("onclick").replace(/.*([\d]+).*/, "$1");
					oc = parseInt(oc, 10);
					oc = isNaN(oc) ? 1 : oc;
					ap.attr("onclick", "return verMasArticulos('home','"
							+ (oc + 1) + "','" + (s ? s : "all") + "');");
					var f = parseInt(data["final"], 10);
					f = isNaN(f) ? 0 : f;
					$("#contadorFinal").html(f);
					var t = parseInt(data["total"], 10);
					t = isNaN(t) ? 0 : t;
					if (f >= t) {
						$("p.ver-mas img").hide();
						ap.hide();
						$("p.ver-mas a:last").show();
					} else {
						$("p.ver-mas img").hide();
						ap.show();
					}
				}
			},
			failure : function() {
				$("p.ver-mas img").hide();
				$("p.ver-mas a:first").show();
			}
		});
	}
	return false;
}
function verMas(url, funcion, crear, vfinal, vtotal, p, s) {
	if (!p) {
		p = 1;
	}
	p = parseInt(p, 10);
	p = isNaN(p) ? 1 : p;
	var co = {
		inicio : p
	};
	if (s) {
		co["section"] = s;
	}
	$("p.ver-mas img").show();
	$("p.ver-mas a").hide();
	$.ajax({
		url : url,
		type : "post",
		data : co,
		dataType : "json",
		success : function(data) {
			if (data && data.articulos) {
				var da = data.articulos;
				for ( var i = 0; i < da.length; i++) {
					if (crear && crear.call) {
						var a = crear(da[i - 1], da[i], da[i + 1]);
						var d = $("tr.last-child");
						d.removeClass("last-child").after(a);
						$(a).addClass("last-child");
						$(a).hide().slideDown("slow");
					}
				}
				$('.nmodal').nyroModal();
				var ap = $("p.ver-mas a:first");
				ap.attr("onclick", "return " + funcion + "('" + data[vfinal]
						+ "','" + (s ? s : "all") + "');");
				var f = parseInt(data[vfinal], 10);
				f = isNaN(f) ? 0 : f;
				$("#contadorFinal").html(f);
				var t = parseInt(data[vtotal], 10);
				t = isNaN(t) ? 0 : t;
				if (f >= t) {
					$("p.ver-mas img").hide();
					ap.hide();
					$("p.ver-mas a:last").show();
				} else {
					$("p.ver-mas img").hide();
					ap.show();
				}
			}
		},
		failure : function() {
			$("p.ver-mas img").hide();
			$("p.ver-mas a:first").show();
		}
	});
	return false;
}
function verMasArticulosComprados(p, s) {
	return verMas("usuario/verMasArticulosComprados",
			"verMasArticulosComprados", crearArticuloVendido, "finalComprados",
			"totalComprados", p, s);
}
function verMasArticulosVendidos(p, s) {
	return verMas("usuario/verMasArticulosVendidos", "verMasArticulosVendidos",
			crearArticuloVendido, "finalVendidos", "totalVendidos", p, s);
}

function verMasArticulosEnCompra(p, s) {
	return verMas("usuario/verMasArticulosEnCompra", "verMasArticulosEnCompra",
			crearArticuloEnCompra, "finalEnCompra", "totalEnCompra", p, s);
}
function verMasArticulosEnVenta(p, s) {
	return verMas("usuario/verMasArticulosEnVenta", "verMasArticulosEnVenta",
			crearArticuloEnVenta, "finalEnVenta", "totalEnVenta", p, s);
}
function verMasArticulosNoComprados(p, s) {
	return verMas("usuario/verMasArticulosNoComprados",
			"verMasArticulosNoComprados", crearArticuloNoComprados,
			"finalNoComprados", "totalNoComprados", p, s);
}
function verMasArticulosNoVendidos(p, s) {
	return verMas("usuario/verMasArticulosNoVendidos",
			"verMasArticulosNoVendidos", crearArticuloNoVendidos,
			"finalNoVendidos", "totalNoVendidos", p, s);
}

function cambiarOrdenBusqueda() {
	cambiarURLGET("orden", this.value, profile);
}

function cambiarUbicacionBusqueda() {
	cambiarURLGET("ubicacion", this.value, profile);
}
function cambiarCriterioBusqueda(profile) {
	return cambiarURLGET("criterio", this.value, profile);
}
function cambiarBusquedaCategoria(categoria) {
	return cambiarURLGET("categoria", categoria, profile);
}

function resetBusqueda() {
	cambiarURLGET("criterio", "");
}
var usuarioID = false;
var profile = false;
function cambiarURLGET(variable, valor, profile) {
	var url = location.href;
	var vars = G.url._GET();
	if (!profile) {
		var m = G.dom.$$$("base", 0);
		url = G.url._setGET(vars, false, m.href);
	} else if (usuarioID) {
		url = G.url._setGET("usuario", usuarioID, url);
	}
	url = G.url._setGET("pagina", 1,url);
	location.href = G.url._setGET(variable, valor, url);
	return false;
}

function enviarMensajePrivado() {
	$.ajax({
		url : "usuario/guardarMensaje",
		data : {
			receptor : this.receptor.value,
			mensaje : this.mensaje.value,
			articulo : this.articulo.value
		},
		type : "post",
		success : function() {
			$.nmManual("home/modal/mensaje-enviado/myuser");
			setTimeout("$('.nyroModalClose').click()", 4000);
		}
	});
}

function enviarMail() {
	$.ajax({
		url : "home/enviarMail",
		data : {
			asunto : this.asunto.value,
			nombre : this.nombre.value,
			email : this.email.value,
			mensaje : this.mensaje.value
		},
		type : "post",
		success : function() {
			$(".nyroModalClose").click();
		}
	});
}

function actualizarCosto() {
	var v = this.gastosEnvioEntrada.value.replace(",", ".");
	v = parseFloat(v);
	v = isNaN(v) ? 0 : v;
	var t = totalPrecio + v;
	$("#gastos_envio").html("+" + formato_moneda(v));
	$("#total").html(formato_moneda(t));
	$("#formGastos input[name='gastos_envio']").val(v);
}
function denunciarPago() {
	$.ajax({
		url : "articulo/denunciarPago",
		data : {
			paquete : this.paquete.value
		},
		type : "post",
		dataType : "json",
		success : function(data) {
			if (data.exito) {
				location.href = location.href.split("#").shift().split("?")
						.shift();
			} else {
				$('.nyroModalClose').click();
			}
		},
		failure : function() {
			$('.nyroModalClose').click();
		}
	});
	return false;
}

function verificarRetrasoEnvio() {
	$cb = $("#formRetrasoEnvio input:checked");
	if ($cb.length > 0) {
		$.ajax({
			url : "articulo/disputaRetrasoEnvio",
			data : {
				paquete : this.paquete.value
			},
			type : "post",
			dataType : "json",
			success : function(data) {
				if (data.exito) {
					location.href = location.href.split("#").shift().split("?")
							.shift();
				} else {
					$('.nyroModalClose').click();
				}
			},
			failure : function() {
				$('.nyroModalClose').click();
			}
		});
	} else {
		$(".nyroModalClose").click();
	}
	return false;
}

function denunciarRecibido() {
	$.ajax({
		url : "articulo/denunciarRecibido",
		data : {
			paquete : this.paquete.value
		},
		type : "post",
		dataType : "json",
		success : function(data) {
			if (data.exito) {
				location.href = location.href.split("#").shift().split("?")
						.shift();
			} else {
				$('.nyroModalClose').click();
			}
		},
		failure : function() {
			$('.nyroModalClose').click();
		}
	});
	return false;
}
function denunciarEnvio() {
	$.ajax({
		url : "articulo/denunciarEnvio",
		data : {
			paquete : this.paquete.value
		},
		type : "post",
		dataType : "json",
		success : function(data) {
			if (data.exito) {
				location.href = location.href.split("#").shift().split("?")
						.shift();
			} else {
				$('.nyroModalClose').click();
			}
		},
		failure : function() {
			$('.nyroModalClose').click();
		}
	});
	return false;
}
function denunciarGastosEnvio() {
	$.ajax({
		url : "articulo/denunciarGastosEnvio",
		data : {
			articulos : this.articulos.value,
			transacciones : this.transacciones.value
		},
		type : "post",
		dataType : "json",
		success : function(data) {
			if (data.exito) {
				location.href = location.href.split("#").shift().split("?")
						.shift();
			} else {
				$('.nyroModalClose').click();
			}
		},
		failure : function() {
			$('.nyroModalClose').click();
		}
	});
	return false;
}

function enviarGastosEnvio() {
	$.ajax({
		url : "articulo/enviarGastosEnvio",
		data : {
			articulos : this.articulos.value,
			transacciones : this.transacciones.value,
			gastos_envio : this.gastos_envio.value,
			paquete : (this.paquete ? this.paquete.value : false)
		},
		type : "post",
		dataType : "json",
		success : function(data) {
			if (data.exito) {
				location.href = location.href.split("#").shift().split("?")
						.shift();
			} else {
				$('.nyroModalClose').click();
			}
		},
		failure : function() {
			$('.nyroModalClose').click();
		}
	});
	return false;
}

function formato_moneda(num) {
	num = parseFloat(num);
	if (!isNaN(num)) {
		var ent = "" + Math.floor(num);
		num = "" + Math.round(num * 100);
		var dec = num.substring(num.length - 2);
		num = "";
		if (ent.length > 3) {
			for ( var i = ent.length; i >= 0; i -= 3) {
				var p = ".";
				if (i < 3) {
					p = "";
				}
				num = p + ent.substring(i - 3, i) + num;
			}
		} else {
			num += ent;
		}
		return num + "," + dec;
	}
	return "0,00";
}

function cambiarFormaPago() {
	$("#formNormalPago input[name='formaPago']").val(this.value);
	switch (this.value) {
	case "4":
		$(".fparrafo").hide();
		$("#formNormalPago").hide();
		$("#formPaypalPago").show();
		$("#footerPago").show();
		$.nmTop().resize(true);

		break;

	default:
		$("#formNormalPago .bt").hide();
		$(".fparrafo").hide();
		$("#formNormalPago input[name='boton" + this.value + "']").show();
		$("#parrafo" + this.value).show();
		$("#formPaypalPago").hide();
		$("#formNormalPago").show();
		$("#footerPago").show();
		$.nmTop().resize(true);
		break;
	}
}

function enviarPago() {
	$.ajax({
		url : "articulo/enviarPago",
		data : {
			formaPago : this.formaPago.value,
			paquete : this.paquete.value
		},
		type : "post",
		dataType : "json",
		success : function(data) {
			if (data.exito) {
				location.href = location.href.split("#").shift().split("?")
						.shift();
			} else {
				$('.nyroModalClose').click();
			}
		},
		failure : function() {
			$('.nyroModalClose').click();
		}
	});
	return false;
}
function confirmarRecepcion() {
	$cb = $("#formConfirmarRecepcion input:checked").val();
	if ($cb) {
		$.ajax({
			url : "articulo/confirmarRecepcion",
			data : {

				paquete : this.paquete.value,
				tipo : $cb
			},
			type : "post",
			dataType : "json",
			success : function(data) {
				if (data.exito) {
					location.href = location.href.split("#").shift().split("?")
							.shift();
				} else {
					$('.nyroModalClose').click();
				}
			},
			failure : function() {
				$('.nyroModalClose').click();
			}
		});
	}
	return false;
}
function confirmarEnvio() {
	$.ajax({
		url : "articulo/confirmarEnvio",
		data : {
			paquete : this.paquete.value
		},
		type : "post",
		dataType : "json",
		success : function(data) {
			if (data.exito) {
				location.href = location.href.split("#").shift().split("?")
						.shift();
			} else {
				$('.nyroModalClose').click();
			}
		},
		failure : function() {
			$('.nyroModalClose').click();
		}
	});
	return false;
}

function cambiarTipoTarifa() {
	$cb = $("#popUp input[name='tipo_cambio']:checked").val();
	if ($cb) {
		$.ajax({
			url : "usuario/cambiarTipoTarifa",
			data : {
				tipo : $cb
			},
			type : "post",
			dataType : "json",
			success : function(data) {
				if (data.exito) {
					$.nmManual("home/modal/tipo-tarifa-actualizada");
				} else {
					$('.nyroModalClose').click();
				}
			},
			failure : function() {
				$('.nyroModalClose').click();
			}
		});
	}
	return false;
}

function enviarDenuncia() {
	$cb = $("#popUp select[name='motivo']").val();
	if ($cb) {
		$.ajax({
			url : "home/denunciar",
			data : {
				usuario : $("#popUp input[name='usuario']").val(),
				articulo : $("#popUp input[name='articulo']").val(),
				descripcion : $("#popUp textarea[name='descripcion']").val(),
				motivo : $cb
			},
			type : "post",
			dataType : "json",
			success : function(data) {
				if (data.exito) {
					$.nmManual("home/modal/denuncia-enviado/myuser");
				}
				setTimeout("$('.nyroModalClose').click();", 4000);
			},
			failure : function() {
				$('.nyroModalClose').click();
			}
		});
	}
	return false;
}

// ////denunciar mensaje/////////
function enviarDenunciaMensaje() {
	$cb = $("#popUp select[name='motivo']").val();
	if ($cb) {
		$.ajax({
			url : "administrador/guardarbdreporte",
			data : {
				reportado : $("#popUp input[name='reportado']").val(),
				idmensaje : $("#popUp input[name='idmensaje']").val(),
				descripcion : $("#popUp textarea[name='descripcion']").val(),
				motivo : $cb
			},
			type : "post",
			dataType : "json",
			success : function(data) {
				if (data.exito) {
					$.nmManual("home/modal/denuncia-enviado/myuser");
				}
				setTimeout("$('.nyroModalClose').click();", 4000);
			},
			failure : function() {
				$('.nyroModalClose').click();
			}
		});
	}
	return false;
}
// ////fin denunciar mensaje/////

function cambiarGastos() {
	var di = $(this).data("input");
	var ei = $(".formA input[name='" + di + "']");
	var eie = $("#" + di + "Error");
	if (di && ei) {
		if (this.checked) {
			switch ($(this).data("input")) {
			case "gastos_pais":
				$("#gastosError").html("");
				break;
			case "gastos_continente":
				var c = ($(".formA input[type='checkbox'].c-b"));
				$("#gastosError").html("");
				for ( var i = 0; i < c.length - 2; i++) {
					var dxi = $(c[i]).data("input");
					$(c[i]).removeAttr("disabled").attr("checked", "checked");
					$(".formA input[name='" + dxi + "']").addClass("required")
							.addClass("min-value").removeAttr("disabled");
					$("#" + dxi + "Error").html("");
					$("#" + dxi + "Error").data("tipo-error", "");
				}
				break;
			case "gastos_todos":
				var c = ($(".formA input[type='checkbox'].c-b"));
				$("#gastosError").html("");
				for ( var i = 0; i < c.length - 1; i++) {
					var dxi = $(c[i]).data("input");
					$(c[i]).removeAttr("disabled").attr("checked", "checked");
					$(".formA input[name='" + dxi + "']").addClass("required")
							.addClass("min-value").removeAttr("disabled");
					$("#" + dxi + "Error").html("");
					$("#" + dxi + "Error").data("tipo-error", "");
				}
				break;
			}
			eie.html("");
			eie.data("tipo-error", "");
			ei.removeAttr("disabled");
			ei.addClass("required");
			ei.addClass("min-value");
			ei.focus();
		} else {
			switch ($(this).data("input")) {
			case "gastos_pais":
				var c = ($(".formA input[type='checkbox'].c-b"));
				$("#gastosError").html("");
				for ( var i = 1; i < c.length; i++) {
					var dxi = $(c[i]).data("input");
					$(c[i]).removeAttr("checked", "checked");
					$(".formA input[name='" + dxi + "']").removeClass(
							"required").removeClass("min-value").attr(
							"disabled", "disabled");
					$("#" + dxi + "Error").html("");
					$("#" + dxi + "Error").data("tipo-error", "");

				}
				break;
			case "gastos_continente":
				var c = ($(".formA input[type='checkbox'].c-b"));
				$("#gastosError").html("");
				for ( var i = 0; i < c.length; i++) {
					var dxi = $(c[i]).data("input");
					$(c[i]).removeAttr("checked", "checked");
					$(".formA input[name='" + dxi + "']").removeClass(
							"required").removeClass("min-value").attr(
							"disabled", "disabled");
					$("#" + dxi + "Error").html("");
					$("#" + dxi + "Error").data("tipo-error", "");
				}
				break;
			case "gastos_todos":
				var c = ($(".formA input[type='checkbox'].c-b"));
				$("#gastosError").html("");
				for ( var i = 0; i < c.length; i++) {
					var dxi = $(c[i]).data("input");
					$(c[i]).removeAttr("checked");
					$(".formA input[name='" + dxi + "']").removeClass(
							"required").removeClass("min-value").attr(
							"disabled", "disabled");
					$("#" + dxi + "Error").html("");
					$("#" + dxi + "Error").data("tipo-error", "");
				}
				break;
			}
			eie.html("");
			eie.data("tipo-error", "");
			ei.attr("disabled", "disabled");
			ei.removeClass("required");
			ei.removeClass("min-value");
		}
	}
}

function envioLocalCambio() {
	var f = $(this.form).find("#destino-envios");
	if (f.length > 0) {
		var c = f.find("input[type='checkbox']").not(this);
		if (this.checked) {
			c.attr("disabled", "disabled");
			f.find("input[type='text']").attr("disabled", "disabled")
					.removeClass("required");
			for ( var i = 0; i < c.length; i++) {
				var di = $(c[i]).data("input");
				$("#" + di + "Error").html("").data("tipo-error", "");
			}
		} else {
			c.removeAttr("disabled");
			for ( var i = 0; i < c.length; i++) {
				if (c[i].checked) {
					var di = $(c[i]).data("input");
					$(".formA input[name='" + di + "']").removeAttr("disabled")
							.addClass("required");
				}
			}
		}
	}
}

function modificarCantidad() {
	$.ajax({
		url : "articulo/modificarCantidad",
		type : "post",
		data : {
			articulo : this.articulo.value,
			cantidad : this.cantidadModificar.value
		},
		dataType : "json",
		success : function(response) {
			if (response.exito) {
				location.href = location.href.split("#").shift();
			} else {
				$(".nyroModalClose").click();
			}
		}
	});
}
function cambiarDestino() {
	var v = this.value;
	switch (v) {
	case "1":
		$(".tipo-precio-box").slideUp("slow").fadeOut("slow");
		$(".formA form input[type='file']").removeClass("required");
		$("#excelError").html("").data("tipo-error", "");
		break;
	case "2":
		$(".tipo-precio-box").slideDown("slow").fadeIn("slow");
		$(".formA form input[type='file']").addClass("required");
		$("#excelError").html("").data("tipo-error", "");
		break;
	}
}

function cancelarEnviosPendientes(id) {
	$.ajax({
		url : "home/cancelarEnviosPendientes",
		data : {
			id : id
		},
		dataType : "json",
		type : "post",
		success : function() {
			location.href = location.href.split("#").shift();
		}
	});
	return false;
}

function procesarRespuestaMensaje() {
	if (this !== window) {
		var s = $(this).data("submit");
		var f = new Function(s + "(arguments[0])");
		var doc = this.contentWindow.document;
		if (doc) {
			var body = G.dom.$$$("body", 0, doc);
			if (body) {
				f(body.innerHTML);
			}
		}
	}
}

function irPagina(p) {
	setTimeout("location.href=G.url._setGET('pagina'," + p + ")");
	return false;
}