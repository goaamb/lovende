<div id="popUp">
	<div class="formA">
		<form class="clearfix d-b" method="post" id="formConfirmarEnvio"
			onsubmit="return confirmarEnvio.call(this);">
			<header>
				<h1><?=traducir("Confirmar envío de Newsletter")?></h1>
			</header>
			<div class="wrap" style="padding-bottom: 0px;">
				<p class="justify">Estáis seguro de que quieres enviar este newsletter?</p>
			</div>
			<!--wrap-->
			<footer>
				<p class="actions">
					<input type="button" class="bt"
						value="<?=traducir("Confirmar envío")?>"
						onclick="$('#formNewsletter').submit();" /> <span class="mhm">o</span>
					<a class="nyroModalClose">cancelar</a>
				</p>
			</footer>
		</form>
	</div>
</div>
<script type="text/javascript">
	envioMasivo=true;
	$(function() {
 	 	$('.nmodal').nyroModal();
	});
</script>