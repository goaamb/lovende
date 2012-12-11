<script type="text/javascript"
	src="<?=base_url()?>assets/js/usuario/usuario.js"></script>
<div class="wrapper clearfix">
	<header class="cont-cab mbl">
		<h1><?=traducir ( "Registrarse en Lovende" )?></h1>
		<p>
			<?=traducir ( "Regístrate hoy para pujar, comprar y vender en Lovende. ¿Ya estás
			registrado?" )?> <a href="login"
				title="<?=traducir ( "entra en tu cuenta" )?>"><?=traducir ( "Entrar" )?></a>
		</p>
	</header>

	<div class="formB">
		<?php
		print form_open ( 'register', array (
				"id" => "formLogin" 
		) );
		?><input type="hidden" name="__accion" value="registrar" />
		<p>
			<label for=""><?=traducir ( "Seudónimo (nombre con el que se te identifica en
				Lovende)" )?></label> <input type="text"
				class="texto w225 required unique seudonimo"
				data-unique-table="usuario" data-unique-field="seudonimo"
				data-error-required="<?=traducir ( "El Campo Seúdonimo es requerido" )?>"
				data-error-unique="<?=traducir ( "El Seúdonimo que quieres ingresar ya existe" )?>"
				value="<?php echo set_value('seudonimo'); ?>" name="seudonimo" /> <?php print form_error("seudonimo") ;?><span
				class="errorTxt"><?print (isset($errorSeudonimo)?$errorSeudonimo:"") ?></span>
		</p>
		<p>
			<label for=""><?=traducir ( "Contraseña" )?></label> <input
				type="password" name="password"
				class="texto w225 required min-length" data-min-length="8"
				data-error-min-length="<?=traducir ( "El campo Contraseña debe tener mas de 8 caracteres" )?>"
				data-error-required="<?=traducir ( "El Campo Contraseña es requerido" )?>"
				value="<?php echo set_value('password'); ?>" /> <?php print form_error("password") ;?>
		</p>
		<p>
			<label for=""><?=traducir ( "Repetir contraseña" )?></label> <input
				type="password" name="passconf"
				class="texto w225 required min-length compare" data-min-length="8"
				data-error-min-length="<?=traducir ( "El campo Repetir Contraseña debe tener mas de 8 caracteres" )?>"
				data-error-required="<?=traducir ( "El Campo Repetir Contraseña es requerido" )?>"
				data-field-compare="password"
				data-error-compare="<?=traducir ( "Ambos campos de Contraseña deben ser iguales" )?>"
				value="<?php echo set_value('passconf'); ?>" /> <?php print form_error("passconf") ;?>
		</p>
		<p>
			<label for="">Email</label> <input type="mail"
				class="texto w225 required unique" data-unique-table="usuario"
				data-unique-field="email"
				data-error-unique="<?=traducir ( "El Email que quieres ingresar ya existe" )?>"
				data-error-required="<?=traducir ( "El Campo Email es requerido" )?>"
				name="email" value="<?php echo set_value('email'); ?>" /> <?php print form_error("email") ;?><span
				class="errorTxt"><?print (isset($errorEmail)?$errorEmail:"") ?></span>
		</p>
		<p>
			<label for=""><?=traducir ( "Introduce el código de verificación" )?></label>
		
		
		<div><?php
		$captcha = $this->mysession->userdata ( "CAPTCHA" );
		if ($captcha) {
			print $captcha ["image"];
		}
		?></div>
		<input type="text" class="texto w225 required imagecode"
			data-error-required="<?=traducir ( "El Código de Verificación es requerido" )?>"
			data-error-imagecode="<?=traducir ( "El Codigo de la Imagen es incorrecta" )?>"
			name="codigo" value="" /> <?php print form_error("codigo") ;?>
		</p>
		<p class="mtl mbm">
			<?=traducir ( "Haciendo click en Registrarme o Entrar con Facebook aceptas los" )?> <a
				href="terms"><?=traducir ( "Términos de uso" )?></a> <?=traducir ( "y confirmas que eres mayor de 18
			años." )?>
		</p>
		<div class="ver-mas">
			<div class="clearfix registro-dual">
				<input type="submit" value="<?=traducir ( "Registrarme" )?>"
					class="bt" name="registrar" /> <span class="o"><?=traducir ( "o" )?></span>
				<?php $this->load->view("usuario/facebook_login");?>
			</div>
		</div>
		</form>
	</div>
</div>