<script type="text/javascript">
function banearusuario(id,tipo, tabla, idreporte )
{
	var parametros = {usuario:id, estado:tipo, tabla:tabla, idreporte:idreporte};
	
	$.ajax({
			url:'administrador/baneo',
			data: parametros,
			type:"POST",
				success:function(res)
				{
					location.href="administration/dashboard";
				}
		});
}


</script>
<div id="popUp">
	<div class="formA">
		<form action="" method="post">
			<header>
				<h1>Unban user</h1>
				<p>User: <a href="store/<?php echo $seudonimo;?>" title="Ver perfil de <?php echo $seudonimo;?>"><strong><del><?php echo $seudonimo;?></del></strong></a> <span class="green">+<?php echo $posi;?></span> <?php if($neg>0){?><span class="red">-<?php echo $neg;?></span><?php }?></p>
			</header>
			<div class="wrap">
				<p class="justify">If you unban this user he will receive an Email notification. Are you sure?</p>
				<br /><br />
			</div><!--wrap-->
			<footer>
				<p class="actions">
					<input type="submit" class="bt" value="Unban" onclick="banearusuario('<?php echo $idobjeto;?>','Activo','usuario','<?php echo $idreporte;?>')"/>
					<span class="mhm">or</span>
					<a class="nyroModalClose">cancel</a>
				</p>
			</footer>
		</form>
	</div>
</div>
<script type="text/javascript">
	$(function() {
 	 	$('.nmodal').nyroModal();
	});
</script>