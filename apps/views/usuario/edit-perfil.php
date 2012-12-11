<?php
$user = $this->mysession->userdata ( "LVSESSION" );
$imagenes = array ();
if ($usuario->imagen) {
	$imagenes [0] = $usuario->imagen;
} else {
	$imagenes [0] = $this->input->post ( "imagenes" );
}
if (! is_file ( BASEPATH . "../files/" . $usuario->id . "/" . $imagenes [0] )) {
	$imagenes [0] = false;
}

$descripcion = $usuario->descripcion ? $usuario->descripcion : set_value ( "descripcion" );
?><script type="text/javascript"
	src="<?=base_url()?>assets/js/uploadprogress.js"></script>
<script type="text/javascript"
	src="<?=base_url()?>assets/js/swfupload/swfupload.js"></script>
<link href="<?=base_url()?>assets/css/nuevo.css" type="text/css"
	rel="stylesheet" />
<link href="<?=base_url()?>assets/css/usuario.css" type="text/css"
	rel="stylesheet" />
<header class="cont-cab">
	<h1>Editar información del perfil</h1>
	<p>La información aquí contenida es pública</p>
</header>
<div class="formA">
	<div class="line">
		<label for="">Añadir nueva foto de perfil (público) <span
			class="dark-grey">|</span> <a href="#"
			onclick="return quitarImagen();">Quitar foto actual</a></label>
		<p id="errorUploading" class="errorTxt"></p>
		<p class="mvm">
				<?php $this->load->view("articulo/upload_photo",array("imagen"=>$imagenes[0],"classcapaphoto"=>"line portada","idcapaphoto"=>"capaimagenperfil","textocapaphoto"=>"foto perfil","quiencapaphoto"=>"perfil"));?>
			</p>
	</div>
		<?=form_open("edit",array("id"=>"formItem","method"=>"post"))?>
		<div class="line">
		<label for="" class="w100"> Descripción del perfil <span class="grey">(público)</span>
			<span class="f-r grey" id="maximoDescripcion"><?php if(strlen($descripcion)>0){?>Tiene <?=(400-strlen($descripcion))?> caracteres.<?php }else{?>Máximo 400 caracteres<?php }?></span>
		</label>
		<p>
			<textarea cols="" rows="5" class="w100" maxlength="400"
				name="descripcion"
				onkeyup="verificarMaximo.call(this,400,'maximoDescripcion');"><?=$descripcion?></textarea>
		</p>
	</div>
	<p class="ver-mas">
		<input type="hidden" name="imagenes"
			value="<?=set_value("imagenes")?>" /> <input type="hidden"
			name="__accion" value="editar-perfil" /> <input type="submit"
			value="Actualizar datos" class="bt" />
	</p>
	<?=form_close();?>
</div>
<!--formA-->
