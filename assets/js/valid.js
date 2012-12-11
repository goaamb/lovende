jQuery.expr[':'].regex = function(elem, index, match) {
	var matchParams = match[3].split(','), validLabels = /^(data|css):/, attr = {
		method : matchParams[0].match(validLabels) ? matchParams[0].split(':')[0]
				: 'attr',
		property : matchParams.shift().replace(validLabels, '')
	}, regexFlags = 'ig', regex = new RegExp(matchParams.join('').replace(
			/^\s+|\s+$/g, ''), regexFlags);
	return regex.test(jQuery(elem)[attr.method](attr.property));
}
function verificarMaximo(tama, texto) {
	if (this.value) {
		var s = this.value.length;
		var l = tama - s;
		$("#" + texto).html("Tiene " + l + " caracteres.");
	}
}

G.valid.isAlpha = function(t) {
	return G.valid.validar(t, /^[a-zA-Z]+$/);
};
function formItemSubmit() {
	var d = $(this).data("submit");
	var r = validarFormulario.call(this);
	if (d) {
		try {
			var f = new Function("return " + d + "();");
			r = f() && r;
		} catch (e) {
		}
	}
	return r;
}
function validarFormulario() {
	var req = $(this).find(".required");
	var valid = true;
	for ( var i = 0; i < req.length; i++) {
		if ($.trim(req[i].value) === "") {
			createErrorField.call(req[i], "El Campo es Requerido", "required",
					"span", "errorTxt");
			valid = valid && false;
		} else {
			createErrorField.call(req[i], "", "required");
		}
	}
	req = $(this).find(".decimal");
	for ( var i = 0; i < req.length; i++) {
		valid = decimalField.call(req[i]) && valid;
	}
	req = $(this).find(".entero");
	for ( var i = 0; i < req.length; i++) {
		valid = enteroField.call(req[i]) && valid;
	}
	req = $(this).find(".alpha");
	for ( var i = 0; i < req.length; i++) {
		valid = alphaField.call(req[i]) && valid;
	}
	req = $(this).find(".compare");
	for ( var i = 0; i < req.length; i++) {
		valid = compareField.call(req[i]) && valid;
	}
	req = $(this).find(".min-length");
	for ( var i = 0; i < req.length; i++) {
		valid = minLengthField.call(req[i]) && valid;
	}
	req = $(this).find(".min-value");
	for ( var i = 0; i < req.length; i++) {
		valid = minValueField.call(req[i]) && valid;
	}
	req = $(this).find(".max-value");
	for ( var i = 0; i < req.length; i++) {
		valid = maxValueField.call(req[i]) && valid;
	}
	req = $(this).find(".propio");
	for ( var i = 0; i < req.length; i++) {
		var d = $(req[i]).data("error-funcion");
		var f = new Function("return " + d + ".call(this)");
		var r = true;
		try {
			r = f.call(req[i]);
		} catch (e) {
		}
		if (!r) {
			createErrorField.call(req[i], "x", "propio", "span", "errorTxt");
			valid = valid && false;
		} else {
			createErrorField.call(req[i], "", "propio");
		}
	}
	return valid;
}

function createErrorField(mensaje, tipo, tag, clase, display) {
	if (mensaje && tipo) {
		$nvm = $(this).data(tipo + "-modal");
		if ($nvm) {
			$.nmManual($nvm);
			return;
		}
	}
	if (this.name) {
		if (!tag) {
			tag = "span";
		}
		var f = this.name + "Error";
		var eF = $(this).data("error-field");
		if (eF) {
			f = eF;
		}
		var e = G.dom.$(f);
		if (!e) {
			e = G.dom.create(tag, f);
			insertAfter(this, e);
		}
		if (clase) {
			e.className += " " + clase;
		}
		var t = $(e).data("tipo-error");
		if (!t) {
			t = [];
		} else {
			t = t.split(" ");
		}
		var h = $(e).html();
		if (!h) {
			h = [];
		} else {
			h = h.split(",&nbsp;");
		}
		var enc = false;
		if (display) {
			e.style.display = display;
		}
		for ( var i = 0; i < t.length; i++) {
			if (t[i] == tipo) {
				enc = i;
				continue;
			}
		}
		if ($.trim(mensaje) !== "") {
			var me = $(this).data("error-" + tipo);
			if (me) {
				mensaje = me;
			}
		}
		if ($.trim(mensaje) === "") {
			if (enc !== false) {
				t.splice(enc, 1);
				h.splice(enc, 1);
			}
		} else {
			if (enc === false) {
				t.push(tipo);
				h.push(mensaje);
			} else {

			}
		}
		$(e).html(h.join(",&nbsp;"));
		$(e).data("tipo-error", t.join(" "));
	}
}
function insertAfter(rn, nn) {
	rn.parentNode.insertBefore(nn, rn.nextSibling);
}

$(reloadValidations);
function reloadValidations() {
	/*req = $("form .min-value");
	for ( var i = 0; i < req.length; i++) {
		$(req[i]).blur(minValueField);
	}
	req = $("form .max-value");
	for ( var i = 0; i < req.length; i++) {
		$(req[i]).blur(maxValueField);
	}
	req = $("form .min-length");
	for ( var i = 0; i < req.length; i++) {
		$(req[i]).blur(minLengthField);
	}*/
	var req = $("form .unique");
	for ( var i = 0; i < req.length; i++) {
		$(req[i]).blur(uniqueRequest);
	}
	req = $("form .compare");
	for ( var i = 0; i < req.length; i++) {
		$(req[i]).blur(compareField);
	}
	req = $("form .decimal");
	for ( var i = 0; i < req.length; i++) {
		$(req[i]).blur(decimalField);
	}
	req = $("form .entero");
	for ( var i = 0; i < req.length; i++) {
		$(req[i]).blur(enteroField);
	}
	req = $("form .imagecode");
	for ( var i = 0; i < req.length; i++) {
		$(req[i]).blur(imageCodeField);
	}
	$(".decimal").keypress(G.valid.floatNoDot);
	$(".entero").keypress(G.valid.int);
	$(".alpha").keypress(G.valid.alpha);
	$(".seudonimo").keypress(G.valid.seudonimo);
}
G.valid.floatNoDot = function(e) {
	var expreg = new RegExp("^[\\+\\-\\d,]$");
	return G.valid.test(e, expreg);
};
G.valid.alpha = function(e) {
	return G.valid.test(e, /^[a-zA-Z]$/);
};
G.valid.seudonimo = function(e) {
	return G.valid.test(e, /^[a-zA-Z0-9\._-]$/);
};

function compareField() {
	var fc = $(this.form[$(this).data("field-compare")]);
	if (fc) {
		if (fc.val() !== this.value) {
			createErrorField.call(this, "Ambos campos deben ser iguales",
					"compare", "span", "errorTxt");
			return false;
		}
		createErrorField.call(this, "", "compare");
	}
	return true;
}
function alphaField() {
	if ($.trim(this.value) !== "" && !G.valid.isAlpha(this.value)) {
		createErrorField.call(this, "El Campo debe ser Alfabetico", "alpha",
				"span", "errorTxt");
		return false;
	}
	createErrorField.call(this, "", "alpha");
	return true;
}
function enteroField() {
	if ($.trim(this.value) !== "" && !G.valid.isInt(this.value)) {
		createErrorField.call(this, "El Campo debe ser Entero", "entero",
				"span", "errorTxt");
		return false;
	}
	createErrorField.call(this, "", "entero");
	return true;
}
function decimalField() {
	this.value = this.value.replace(".", "");
	if ($.trim(this.value) !== "" && !G.valid.isFloat(this.value, ",")) {
		createErrorField.call(this, "El Campo debe ser Decimal", "decimal",
				"span", "errorTxt");
		return false;
	}
	createErrorField.call(this, "", "decimal");
	return true;
}

function minLengthField() {
	var m = $(this).data("min-length");
	if ($.trim(this.value) !== "" && this.value.length < m) {
		createErrorField.call(this, "El campo debe tener mas de " + m
				+ " caracteres", "min-length", "span", "errorTxt");
		return false;
	}
	createErrorField.call(this, "", "min-length");
	return true;
}
function minValueField() {
	var m = $(this).data("min-value");
	var t = $(this).data("min-value-tipo");
	var v = this.value.replace(",", ".");
	v = parseFloat(v);
	v = isNaN(v) ? 0 : v;
	if (t && t == "dom") {
		eval("m=" + m + ";");
	}
	m = parseFloat(m);
	m = isNaN(m) ? 0 : m;
	var e = $(this).data("min-value-equal");
	var comp = v < m;
	if (e) {
		comp = v <= m;
	}
	if ($.trim(v) !== "" && comp) {
		createErrorField.call(this, "El campo debe debe ser mayor a " + m,
				"min-value", "span", "errorTxt");
		return false;
	}
	createErrorField.call(this, "", "min-value");
	return true;
}
function maxValueField() {
	var m = $(this).data("max-value");
	var t = $(this).data("max-value-tipo");
	var v = this.value.replace(",", ".");
	v = parseFloat(v);
	v = isNaN(v) ? 0 : v;
	if (t && t == "dom") {
		eval("m=" + m + ";");
	}
	m = parseFloat(m);
	m = isNaN(m) ? 0 : m;
	var e = $(this).data("max-value-equal");
	var comp = v > m;
	if (e) {
		comp = v >= m;
	}
	if ($.trim(v) !== "" && comp) {
		createErrorField.call(this, "El campo debe debe ser menor a " + m,
				"max-value", "span", "errorTxt");
		return false;
	}
	createErrorField.call(this, "", "max-value");
	return true;
}
function imageCodeField() {
	var v = this.value;
	var th = this;
	if ($.trim(v) !== "") {
		$.ajax({
			url : "valid/imageCode",
			data : {
				value : v
			},
			type : "POST",
			dataType : "json",
			success : function(data) {
				if (data.verified) {
					createErrorField.call(th, "", "imagecode");
					return;
				}
				createErrorField.call(th, "El codigo es incorrecto",
						"imagecode", "span", "errorTxt");

			}
		});
	}
}
function uniqueRequest() {
	var t = $(this).data("unique-table");
	var f = $(this).data("unique-field");
	var v = this.value;
	var th = this;
	if (t && f && $.trim(v) !== "") {
		$.ajax({
			url : "valid/unique",
			data : {
				table : t,
				field : f,
				value : v
			},
			type : "POST",
			dataType : "json",
			success : function(data) {
				if (data.unique) {
					createErrorField.call(th, "", "unique");
					return;
				}
				createErrorField.call(th, data.error, "unique", "span",
						"errorTxt");

			}
		});
	}
}