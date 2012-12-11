<link href="assets/css/perfil.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="assets/js/usuario/perfil.js"></script>
<div class="wrapper clearfix">
<?php 

$this->load->view("usuario/cabecera-perfil",array("seccion"=>"perfil"))?>
	<?php $this->load->view("home/lateral");?>
	<div class="main-col">
			<?php  $this->load->view("usuario/listararticulos")?>
	</div>
</div>