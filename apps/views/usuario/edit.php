<link href="<?=base_url()?>assets/css/edit.css" rel="Stylesheet"
	type="text/css" />
<script type="text/javascript"
	src="<?=base_url()?>assets/js/usuario/usuario.js"></script>
<div class="wrapper clearfix">
	<aside>
		<ul>
			<li><a href="edit/profile" title="Información del perfil"
				<?php if($pos==1){?> class="edit-active" <?php }?>>Información del
					perfil</a></li>
			<li><a href="edit/account" title="Información de la cuenta"
				<?php if($pos==3){?> class="edit-active" <?php }?>>Información de la
					cuenta</a></li>
			<li><a href="edit/buy-sell" title="Información de compra-venta"
				<?php if($pos==2){?> class="edit-active" <?php }?>>Información de
					compra-venta</a></li>
		</ul>
	</aside>

	<div class="main-col">
		<section class="result-list"><?$this->load->view($view);?></section>
	</div>
</div>
