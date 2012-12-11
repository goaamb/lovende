<?php 
	//idarticulo
	//estadoanterior
	//accion
	//var_dump($articulo);
?>

<script type="text/javascript">
function disable(id,estado,accion)
{
	var parametros = {idarticulo:id, estado:estado, accion:accion};
	
	$.ajax({
			url:'administrador/pageidarticulo',
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
				<h1>Enable page</h1>
				<p>Page: <span class="blue"><del><?php echo $articulo->titulo;?></del></span></p>
			</header>
			<div class="wrap">
				<p class="justify">If you enable this page the owner will receive an Email notification. Are you sure?</p>
				<br /><br />
			</div><!--wrap-->
			<footer>
				<p class="actions">
					<input type="submit" class="bt" value="Enable" onclick="disable('<?php echo $idarticulo;?>','<?php echo $articulo->estado_anterior;?>','enable')"/>
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