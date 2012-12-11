<?php
$realUrl = "login";
$uri = uri_string ( $_SERVER ["REQUEST_URI"] );
if (preg_match ( "/^$realUrl\/.*/", $uri ) === 0) {
	$urlBack = str_replace ( "=", "", base64_encode ( $uri ) );
}
?><script type="text/javascript"
	src="<?=base_url()?>assets/js/usuario/usuario.js"></script>
<div class="wrapper clearfix">

	<header class="cont-cab mbl">
		<h1>Entrar en Lovende</h1>
		<p>
			¿Todavía no estás registrado? <a
				href="register<?=(isset($urlBack)?"/$urlBack":"")?>"
				title="registrate en Lovende">Regístrate</a>
		</p>
	</header>

	<div class="formB">
		<?=form_open("login",array("id"=>"formLogin"))?>
		<input type="hidden" name="__accion" value="login"><input
			type="hidden" name="urlBack"
			value="<?=isset($urlBack)?$urlBack."==":""?>" /> <span
			class="errorTxt"><?=isset($error)?$error:"" ?></span>
		<p>
			<label for="">Seudónimo (nombre con el que se te identifica en
				Lovende)</label> <input type="text"
				class="texto w225 required seudonimo"
				data-error-required="Añade el seudónimo."
				name="seudonimo" value="<?=set_value("seudonimo"); ?>" /> <span
				class="errorTxt"><?=isset($errorSeudonimo)?$errorSeudonimo:"" ?></span> <?=form_error("seudonimo")?>
			</p>
		<p>
			<label for="">Contraseña actual <span class="dark-grey">|</span> <a
				href="forgot" title="Recordar contraseña">¿Olvidaste tu contraseña?</a></label>
			<input type="password" class="texto w225 required" name="password"
				data-error-required="Añade la contraseña." /> <span
				class="errorTxt"><?=isset($errorPassword)?$errorPassword:"" ?></span> <?=form_error("password")?>
			</p>
		<p>
			<input type="checkbox" name="recuerdame" /> Recordarme
		</p>
		<div class="ver-mas">
			<div class="clearfix registro-dual">
				<input type="submit" value="Entrar" class="bt" name="login" /> <span
					class="o">o</span>
				<?php $this->load->view("usuario/facebook_login")?>
			</div>
		</div>
		</form>
	</div>
</div>
<!--wrapper-->