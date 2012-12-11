function Progreso(options) {
	this.options = {
		porcentaje : 0,
		padre : null,
		clase : null
	};
	G.util.parse(options, this.options);
	var o = this.options;
	if (typeof o.padre == "string") {
		o.padre = G.dom.$(o.padre);
	}
	if (o.padre) {
		var pb = G.dom.$(o.padre.id + "Progress");
		if (!pb) {
			pb = G.dom.create("div", o.padre.id + "Progress");
			pb.barra = G.dom.create("div", o.padre.id + "ProgressBar");
			pb.appendChild(pb.barra);
			o.padre.appendChild(pb);
		}
		this.progressBar = pb;
		if (o.clase) {
			pb.className = o.clase;
		}
		G.util.parse({
			position : "absolute",
			left : "10%",
			top : "45%",
			width : "80%",
			height : "10%",
			background : "transparent",
			border : "1px solid #565656",
			display : "block"
		}, pb.style);
		G.util.parse({
			position : "absolute",
			left : "0%",
			top : "0%",
			width : "0%",
			height : "100%",
			background : "#C00"
		}, pb.barra.style);
	}
	this.porcentaje = function(p) {
		p = parseInt(p, 10);
		p = (isNaN(p) ? 0 : p);
		this.options.porcentaje = p;
		var pb = this.progressBar;
		if (pb && pb.barra) {
			G.util.parse({
				width : p + "%"
			}, pb.barra.style);
		}
	};
	this.esconder = function() {
		var pb = this.progressBar;
		if (pb) {
			G.util.parse({
				display : "none"
			}, pb.style);
		}
	};
}
function fileQueueError(file, errorCode, message) {
	var c = $("#capaimagen" + this.settings.quien);
	c.removeClass("loading").removeClass("progress").removeAttr("style").find(
			"span").css("filter", "alpha(opacity=100)").css("opacity", "1");
	var st = c.find(".spanTxt");
	c.find(".block").hide();
	st.html(st.data("html"));
	$("#errorUploading").html(message);
	var p = new Progreso({
		padre : "capaimagen" + this.settings.quien
	});
	p.esconder();

}
function fileDialogComplete(numFilesSelected, numFilesQueued) {
	try {
		if (numFilesQueued > 0) {
			this.startUpload();
			var c = $("#capaimagen" + this.settings.quien);
			c.addClass("loading").find("span.spanObj").css("filter",
					"alpha(opacity=0)").css("opacity", "0");
			var st = c.find(".spanTxt");
			st.html("Subiendo").css("filter", "alpha(opacity=100)").css(
					"opacity", "1");
			c.find(".block").show();
			if (startUpload && startUpload.call) {
				startUpload();
			}
		}
	} catch (ex) {
	}
}

function uploadProgress(file, bytesLoaded) {

	try {
		var percent = Math.ceil((bytesLoaded / file.size) * 100);
		var p = new Progreso({
			padre : "capaimagen" + this.settings.quien
		});
		p.porcentaje(percent);
		$("#capaimagen" + this.settings.quien).removeClass("loading").addClass(
				"progress").css("background-image", "none");
	} catch (ex) {
	}
}
function uploadSuccess(file, serverData, response) {

	try {
		var json = {};
		eval("json=" + serverData + ";");
		if (imageReady) {
			imageReady(json);
		}
		$("#otrosErrores").html(response);
	} catch (ex) {
		var c = $("#capaimagen" + this.settings.quien);
		c.removeClass("loading").removeClass("progress").removeAttr("style")
				.find("span").css("filter", "alpha(opacity=100)").css(
						"opacity", "1");
		var st = c.find(".spanTxt");
		st.html(st.data("html"));
		c.find(".block").hide();
		$("#otrosErrores").html(serverData);
		$("#errorUploading")
				.html(
						"Error al Subir el archivo por favor vuelva a intentarlo mas tarde");
		var p = new Progreso({
			padre : "capaimagen" + this.settings.quien
		});
		p.esconder();
	}
}

function uploadComplete(file) {

}

function uploadError(file, errorCode, message) {
	var c = $("#capaimagen" + this.settings.quien);
	c.removeClass("loading").removeClass("progress").removeAttr("style").find(
			"span").css("filter", "alpha(opacity=100)").css("opacity", "1");
	c.find(".block").hide();
	var st = c.find(".spanTxt");
	st.html(st.data("html"));
	$("#errorUploading").html(message);
	var p = new Progreso({
		padre : "capaimagen" + this.settings.quien
	});
	p.esconder();
}
$(function() {
	if (G.nav.isIE && G.nav.version < 9) {
		var u = $(".uploader").hover(function() {
			if (!this.sombra) {
				var s = G.dom.create("div");
				this.sombra = s;
				s.className = "sombra";
				this.appendChild(s);
				G.util.parse({
					width : (this.offsetWidth - 2) + "px",
					height : (this.offsetHeight - 2) + "px"
				}, s.style);
			}
			this.style.overflow = "visible";
			this.sombra.style.display = "block";
		}, function() {
			if (this.sombra) {
				this.sombra.style.display = "none";
			}
		});
	}
});