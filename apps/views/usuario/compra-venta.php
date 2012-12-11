<?php
$errorNombre = form_error ( "nombre" );
$errorNombre = $errorNombre ? $errorNombre : form_error ( "apellido" );
$errorDNI = form_error ( "dni-num" );
$errorDNI = $errorDNI ? $errorDNI : form_error ( "dni-letra" );
$errorLocacion = form_error ( "pais" );
$errorLocacion = $errorLocacion ? $errorLocacion : form_error ( "ciudad" );
?>
<div style="display: none;"><?php
var_dump ( $this->myuser == $usuario );
var_dump ( $this->myuser );
var_dump ( $usuario );
?></div>
<header class="cont-cab">
	<h1>Editar información de compra-venta</h1>
	<p>La información aquí contenida es privada, pero puede facilitarse a
		las partes implicadas en una disputa.</p>
</header>
<div class="formB">
	<?=form_open("edit/buy-sell",array("id"=>"formItem"))?>
		<div class="clearfix d-b">
		<p class="apaisado">
			<label for="">Nombre completo / Razón social</label> <input
				type="text" class="texto w454 propio required" name="nombre"
				value="<?=$usuario->nombre?$usuario->nombre:set_value("nombre",$usuario->nombre);?>"
				data-error-funcion="validarNombreApellido"
				data-error-required="Añade el nombre o razón social."
				data-error-field="nombreError" />
		</p>
	</div>
	<div class="clearfix d-b">
		<p class="apaisado">
			<label for="">DNI / CIF</label> <input type="text"
				value="<?=$usuario->dni?$usuario->dni:set_value("dni",$usuario->dni);?>"
				class="texto w225 propio required" name="dni"
				data-error-required="Añade el DNI o CIF."
				data-error-field="dniError" />
		</p>
	</div>
	<p>
		<label for="">Dirección</label> <input type="text"
			value="<?=$usuario->direccion?$usuario->direccion:set_value("direccion",$usuario->direccion);?>"
			class="texto w454 required" name="direccion"
			data-error-required="Añade la dirección." />&nbsp;<span
			class="errorTxt" id="direccionError"><?=form_error("direccion");?></span>
	</p>
	<div class="clearfix d-b">
		<p class="apaisado">
			<label for="">Pais</label> <select class="texto w225 propio"
				onchange="cargarCiudades.call(this)"
				data-error-funcion="validarLocacion"
				data-error-propio="Añade el país y ciudad."
				data-error-field="locacionError" name="pais">
				<option value="">Elegir</option>
				<?php
				foreach ( $paises as $pais ) {
					?><option value="<?=$pais->codigo3;?>"
					<?=my_set_select("pais",$pais->codigo3,$pais->codigo3===$paisDefecto);?>><?=$pais->nombre;?></option><?php
				}
				?>
			</select>

		</p>
		<p class="apaisado">
			<label for="">Ciudad</label> <select class="texto w225 propio"
				data-error-funcion="validarLocacion"
				data-error-propio="El Pais y la ciudad son requeridos."
				data-error-field="locacionError" name="ciudad">
				<option value="">Elegir</option>
				<?php
				foreach ( $ciudades as $ciudad ) {
					?><option value="<?=$ciudad->id;?>"
					<?=my_set_select("ciudad",$ciudad->id,$ciudad->id===$usuario->ciudad);?>><?=$ciudad->nombre;?></option><?php
				}
				?>
			</select>&nbsp;<span class="errorTxt" id="locacionError"><?=$errorLocacion;?></span>
		</p>
	</div>
	<p>
		<label for="">Código Postal</label> <input type="text"
			class="texto required entero" name="codigo_postal" size="5"
			data-error-required="Añade el código postal."
			value="<?=$usuario->codigo_postal?$usuario->codigo_postal:set_value("codigo_postal",$usuario->codigo_postal);?>" />&nbsp;<span
			class="errorTxt" id="codigo_postalError"><?=form_error("codigo_postal");?></span>
	</p>
	<p>
		<label for="">Teléfono</label> <input type="phone"
			class="texto w225 entero required" name="telefono"
			data-error-required="Añade el teléfono."
			value="<?=$usuario->telefono?$usuario->telefono:set_value("telefono",$usuario->telefono);?>" />&nbsp;<span
			class="errorTxt" id="telefonoError"><?=form_error("telefono");?></span>
	</p>
	<p>
		<label for="">Email de Paypal donde quieres recibir los pagos <span
			class="gery">(opcional)</span></label> <input type="email"
			name="paypal" class="texto w225"
			value="<?=$usuario->paypal?$usuario->paypal:set_value("paypal",$usuario->paypal);?>" />&nbsp;<span
			class="errorTxt" id="emailError"><?=form_error("paypal");?></span>
	</p>
	<p class="ver-mas">
		<input name="__accion" value="compra-venta" type="hidden" /> <input
			type="submit" value="Actualizar datos" class="bt" />
	</p>
	<?=form_close();?>
</div>