<?php
$headerTitle = isset ( $headerTitle ) ? $headerTitle : (isset ( $this->configuracion ) ? $this->configuracion->variables ( "defaultHeaderTitle" ) : "");
$headerDescripcion = isset ( $headerDescripcion ) ? $headerDescripcion : (isset ( $this->configuracion ) ? $this->configuracion->variables ( "defaultHeaderDescription" ) : "");
$headerKeywords = isset ( $headerKeywords ) ? $headerKeywords : (isset ( $this->configuracion ) ? $this->configuracion->variables ( "defaultHeaderKeywords" ) : "");
$extraMeta = isset ( $extraMeta ) ? $extraMeta : "";
$publi = ($this->configuracion->variables ( "publicidad" ) == "Si");
$totalVentas = isset ( $totalVentas ) ? $totalVentas : 0;
$totalCompras = isset ( $totalCompras ) ? $totalCompras : 0;
$totalSeguimientos = isset ( $totalSeguimientos ) ? $totalSeguimientos : 0;
$totalMensajes = isset ( $totalMensajes ) ? $totalMensajes : 0;
$totalCuentas = isset ( $totalCuentas ) ? $totalCuentas : 0;
$soloPendientes = isset ( $soloPendientes ) ? $soloPendientes : 0;
$totalNoVistos = $totalCompras + $totalVentas + $totalSeguimientos + $totalMensajes + $totalCuentas;
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title><?=$headerTitle?></title>
<base href="<?=base_url();?>" />
<meta name="description" content="<?=$headerDescripcion?>" />
<meta name="keywords" content="<?=$headerKeywords?>" />
<?=$extraMeta?>
<link rel="stylesheet" type="text/css" href="assets/css/reset.css" />
<link rel="stylesheet" type="text/css" href="assets/css/style.css" />
<link rel="stylesheet" type="text/css" href="assets/css/extra.css" />
<link rel="stylesheet" type="text/css" href="assets/css/nyroModal.css" />
<link rel="stylesheet" type="text/css"
	href="assets/css/mediaqueries.css" />
<link rel="author" href="humans.txt" />
<link rel="stylesheet" type="text/css" href="assets/css/style2.css" />
<link rel="shortcut icon" href="assets/icon/favicon.ico" />
<link href="assets/icon/favicon.ico" rel="shortcut icon"
	type="image/x-icon" />
<!--[if lte IE 7]><link rel="stylesheet" type="text/css" href="assets/css/ie7.css" /><![endif]-->
<script type="text/javascript" src="assets/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="assets/js/general.js"></script>
<script type="text/javascript" src="assets/js/piscolabis.framework.js"></script>
<script type="text/javascript" src="assets/js/jquery.tablesorter.min.js"></script>
<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<script type="text/javascript" src="assets/js/easySlider1.7.g.js"></script>
<script type="text/javascript"
	src="assets/js/jquery.nyroModal.custom.js"></script>
<!--[if IE 6]>
	<script type="text/javascript" src="assets/js/jquery.nyroModal-ie6.min.js">
<![endif]-->
<script type="text/javascript" src="assets/js/goaamb/G.js"></script>
<script type="text/javascript" src="assets/js/valid.js"></script>
<script type="text/javascript" src="assets/js/general2.js"></script>
<script type="text/javascript">
	SeudonimoUsuario='<?php if(isset($usuario) && $usuario){ print "$usuario->seudonimo";}?>';
	$(function() {
 	 	$('.nmodal').nyroModal();
	});
</script>
<!--jcarrusel home -->
<script type="text/javascript" src="assets/js/jquery.jcarousel.min.js"></script>
<script type="text/javascript">
$(function() {
    $('#carruselHome').jcarousel({scroll: 5});
});
</script>
<!--fin jcarrusel home -->
</head>
<body>


<body>
<?php
if ($publi) {
	?>
	<div class="top-publi">
		<script type="text/javascript"><!--
google_ad_client = "ca-pub-5921475361745899";
/* Lovende1 */
google_ad_slot = "6150527728";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
		<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script><?php
}
?>
		<div class="wrapper">
			<header class="header-new clearfix">
				<div class="topNav">
				<?php
				if ($usuario) {
					$imagen = imagenPerfil ( $usuario, "small" );
					?>
			<a class="showMiperfil-new"
						href="store/<?=$this->myuser->seudonimo?>" title="Ir a mi tienda"><?=traducir("Mi tienda");?> <span
						class="f11"><?=($totalNoVistos>0?"($totalNoVistos)":"")?></span></a> 
			<?php
					if ($usuario->tipo == 'Administrador') {
						?><a href="administration/dashboard"
						title="<?=traducir("ir a la administración");?>">Dashboard</a> <?php
					}
					?>
			<a
						href="<?=($usuario->estado==="Incompleto"?"home/modal/informacion-compra-venta":"product/nuevo");?>"
						title="<?=traducir("Vender artículo");?>"
						class="<?=($usuario->estado==="Incompleto"?"nmodal":"");?> sep"><?=traducir("Vender");?></a>
					<a href="logout" title="<?=traducir("Salir");?>" class="last"><?=traducir("Salir");?></a>
			<?php }else {?><a href="login"
						title="<?=traducir("Entra con tu cuenta");?>"><?=traducir("Entrar");?></a>
					<a href="register" title="<?=traducir("Registrate");?>"
						class="last"><?=traducir("Registrarse");?></a>
			<?php }?>
			</div>
				<!--topNav-->
				<?php
				if ($usuario) {
					?><div id="miperfil-desplegable-new">
					<header></header>
					<ul>
						<li><a href="store/<?=$usuario->seudonimo?>/sell" title=""><?=traducir("Ventas");?><?=$totalVentas>0?" ($totalVentas)":""?></a></li>
						<li><a href="store/<?=$usuario->seudonimo?>/self" title=""><?=traducir("Compras");?><?=$totalCompras>0?" ($totalCompras)":""?></a></li>
						<li><a href="store/<?=$usuario->seudonimo?>/following" title=""><?=traducir("Seguimientos");?><?=$totalSeguimientos>0?" ($totalSeguimientos)":""?></a></li>
						<li><a href="store/<?=$usuario->seudonimo?>/messages" title=""><?=traducir("Mensajes");?><?=$totalMensajes>0?" ($totalMensajes)":""?></a></li>
						<li><a href="store/<?=$usuario->seudonimo?>/billing" title=""><?=traducir("Cuentas");?><?=$totalCuentas>0?" ($totalCuentas)":""?></a></li>
					</ul>
				</div><?php
				}
				?>
			<div class="redBox">
					<h1>
						<a href="<?php site_url("/");?>"
							title="<?=traducir("Volver a la home");?>">Lovende</a>
					</h1>
					<div class="search-box">
						<form action="" method="get"
							onsubmit="return cambiarCriterioBusqueda.call(this.criterio);">
							<p class="label-on-field">
								<input type="text" class="texto OwnTextBox" name="criterio"
									data-text="<?=traducir("Buscar artículo");?>"
									data-class="OwnTextBoxNoData"
									value="<?=$this->input->get("usuario")?"":$this->input->get("criterio");?>" />
								<script type="text/javascript">
							var ot = $(".OwnTextBox");
							if (ot) {
								for ( var i = 0; i < ot.length; i++) {
									G.OwnTextBox.convert(ot[i]);
								}
							}</script>
								<span class="resetBusqueda"
									<?=(($this->input->get("usuario")?"":$this->input->get("criterio"))?"style='display:block';":"")?>
									onclick="resetBusqueda();">x</span>
							</p>
							<input type="submit" class="bt" value="Buscar" />
						</form>
					</div>
				</div>
				<!--redBox-->
				<div class="blueBt">
					<a
						href="<?=(isset($usuario) && $usuario && $usuario->estado==="Incompleto"?"home/modal/informacion-compra-venta":"product/nuevo");?>"
						class="<?=(isset($usuario) && $usuario && $usuario->estado==="Incompleto"?"nmodal":"");?>"
						title="<?=traducir("vende gratis")?>"><?=traducir("Poner en venta gratis")?></a>
				</div>
				<!--blueBt-->
			</header>
			<div class="content">