<div id="popUp">
	<div class="formA">
		<form action="" method="post">
			<header>
				<h1>Contacta con nosotros</h1>
				<p>
					Asunto:<strong><?=$asunto?></strong>
				</p>
			</header>
			<div class="wrap">
				<div class="line">
					<label for="">Nombre:</label>
					<p>
						<input class="w100" name="nombre" />
					</p>
				</div>
				<div class="line">
					<label for="">Email:</label>
					<p>
						<input class="w100" name="email" />
					</p>
				</div>
				<div class="line">
					<label for="">Mensaje:</label>
					<p>
						<textarea rows="4" cols="" class="w100" name="mensaje"></textarea>
					</p>
				</div>
			</div>
			<!--wrap-->
			<footer>
				<p class="actions">
					<input type="hidden" name="asunto" value="<?=$asunto?>" /> <input
						type="button" class="bt" value="Enviar"
						onclick="enviarMail.call(this.form)" /> <span class="mhm">o</span>
					<a class="nyroModalClose">cancelar</a>
				</p>
			</footer>
		</form>
	</div>
</div>
<style>
#popUp .wrap {
	padding: 15px;
	width: 50%;
	max-height: 300px;
	overflow: auto;
	margin:0 auto;
}
</style>
<script type="text/javascript">
	$(function() {
 	 	$('.nmodal').nyroModal();
	});
</script>