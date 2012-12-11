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
		<h1><?=traducir ( "Entrar en Lovende" )?></h1>
		<p>
			<?=traducir ( "¿Todavía no estás registrado?" )?> <a
				href="register<?=(isset($urlBack)?"/$urlBack":"")?>"
				title="<?=traducir ( "registrate en Lovende" )?>"><?=traducir ( "Regístrate" )?></a>
		</p>
	</header>

	<div class="formB">
		<?=form_open("login",array("id"=>"formLogin"))?>
		<input type="hidden" name="__accion" value="login"><input
			type="hidden" name="urlBack"
			value="<?=isset($urlBack)?$urlBack."==":""?>" /> <span
			class="errorTxt"><?=isset($error)?$error:"" ?></span>
		<p>
			<label for=""><?=traducir ( "Seudónimo (nombre con el que se te identifica en
				Lovende)" )?></label> <input type="text"
				class="texto w225 required seudonimo"
				data-error-required="<?=traducir ( "Añade el seudónimo." )?>"
				name="seudonimo" value="<?=set_value("seudonimo"); ?>" /> <span
				class="errorTxt"><?=isset($errorSeudonimo)?$errorSeudonimo:"" ?></span> <?=form_error("seudonimo")?>
			</p>
		<p>
			<label for=""><?=traducir ( "Contraseña actual" )?> <span
				class="dark-grey">|</span> <a href="forgot"
				title="<?=traducir ( "Recordar contraseña" )?>"><?=traducir ( "¿Olvidaste tu contraseña?" )?></a></label>
			<input type="password" class="texto w225 required" name="password"
				data-error-required="<?=traducir ( "Añade la contraseña." )?>" /> <span
				class="errorTxt"><?=isset($errorPassword)?$errorPassword:"" ?></span> <?=form_error("password")?>
			</p>
		<p>
			<input type="checkbox" name="recuerdame" /> <?=traducir ( "Recordarme" )?>
		</p>
		<div class="ver-mas">
			<div class="clearfix registro-dual">
				<input type="submit" value="<?=traducir ( "Entrar" )?>" class="bt"
					name="login" /> <span class="o"><?=traducir ( "o" )?></span>
				<?php $this->load->view("usuario/facebook_login")?>
			</div>
		</div>
		</form>
	</div>
</div>
<!--wrapper-->