<div class="wrapper clearfix">
<?php
$cmh = $this->input->cookie ( "marquesinaHome" );
if (! $cmh) {
	?>
	<section id="welcome-warning" class="clearfix">
		<header>
			<h1><?=traducir("Bienvenido a Lovende");?></h1>
			<h2><?=traducir("Compra y vende por subasta y precio fijo");?></h2>
		</header>
		<div class="cont">
			<ul>
				<li><?=traducir("Pon en venta tus artículos <strong>GRATIS</strong>");?></li>
				<li><?=traducir("Compra y vende de forma <strong>FÁCIL</strong>");?></li>
				
				<li><?=traducir ( "Sólo pagas comisión por venta<br /> realizada con
					éxito," );?> <a href="fees" title="<?=traducir("Ver nuestras tarifas");?>"><?=traducir("VER TARIFAS");?></a></li>
			</ul>
			<br />
			<div class="actions">
				<p>
					<a href="register" class="bigger"><?=traducir("Registrarme");?></a>
					<span class="mhs"><?=traducir("o");?></span>
				</p>
				<?php $this->load->view("usuario/facebook_login")?>
			</div>
		</div>
		<a href="#" class="closeThis closeThisAction"
			data-close="welcome-warning" data-action="cerrarMarquesinaHome"><?=traducir("Cerrar");?></a>
	</section>
<?php }?>
	<?php $this->load->view("home/lateral")?>

	<div class="main-col">
			<?php $this->load->view("usuario/listararticulos")?>
	</div>
	<!--main-col-->
</div>