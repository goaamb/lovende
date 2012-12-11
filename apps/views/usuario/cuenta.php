<header class="cont-cab">
	<h1>Editar información de la cuenta</h1>
	<p>La información aquí contenida es privada</p>
</header>
<div class="formB">
	<?=form_open("edit/account",array("id"=>"formCuenta"));?>
		<p>
		<label for="">Seudónimo (nombre con el que se te identifica en
			Lovende)</label> <input type="text" class="texto w225 seudonimo"
			name="seudonimo"
			value="<?=set_value("seudonimo",$usuario->seudonimo);?>" /> <span
			class="errorTxt"><?=(isset($errorSeudonimo)?$errorSeudonimo:"");?></span>
	</p>
	<p>
		<label for="">Contraseña actual <spanclass"dark-grey">|</span> <a
				href="forgot" title="recuperar tu contraseña">¿Olvidaste tu
				contraseña?</a></label> <input type="password" class="texto w225"
			name="password" /> <span class="errorTxt"><?=(isset($errorPassword)?$errorPassword:"");?></span>
	</p>
	<div class="clearfix d-b">
		<p class="apaisado">
			<label for="">Nueva contraseña</label> <input type="password"
				class="texto w225" name="nuevoPassword" />
		</p>
		<p class="apaisado">
			<label for="">Repetir nueva contraseña</label> <input type="password"
				class="texto w225" name="repetirPassword" /> <span class="errorTxt"><?=(isset($errorNuevoPassword)?$errorNuevoPassword:"");?></span>
		</p>
	</div>
	<p>
		<label for="">Email</label> <input type="mail"
			value="<?=set_value("email",$usuario->email)?>" name="email"
			class="texto w225" /> <span class="errorTxt"><?=(isset($errorEmail)?$errorEmail:"");?></span>
	</p>
	<p>
		<input type="checkbox" value="1"
			<?=my_set_checkbox("notificaciones","1", ($usuario->notificaciones?false:true));?>
			name="notificaciones" /> No quiero recibir notificaciones de Lovende
		en mi e-mail
	</p>
	<p class="ver-mas">
		<input type="hidden" name="__accion" value="editar-cuenta" /> <input
			type="submit" value="Actualizar datos" class="bt" />
	</p>
	<?=form_close();?>
</div>