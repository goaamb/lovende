<div class="wrapper clearfix">
	<header class="cont-cab mbl">
		<h1>¿Olvidaste tu contraseña?</h1>
		<p>Te enviaremos un email con tu nueva
			contraseña y un enlace para verificar tu petición.</p>
	</header>
	<div class="formB">
		<?php
		print form_open ( 'forgot' );
		?><input type="hidden" name="__accion" value="olvidar" /><span
			class="errorTxt"><?print (isset($error)?$error:"") ?></span>
		<p>
			<label for="">Email con el que te registraste en Lovende:</label> <input
				type="mail" class="texto w225" name="email"
				value="<?php echo set_value('email',($usuario?$usuario->email:"")); ?>" /> <?php print form_error("email") ;?>
		</p>
		<div class="ver-mas">
			<div class="clearfix registro-dual">
				<input type="submit" value="Enviar" class="bt" name="olvidar" />
			</div>
		</div>
		</form>
	</div>
</div>