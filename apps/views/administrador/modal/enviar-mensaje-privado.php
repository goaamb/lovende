<script type="text/javascript">
function isNumberKey(evt)
{
   var charCode = (evt.which) ? evt.which : event.keyCode
   if (charCode > 31 && (charCode < 48 || charCode > 57))
      return false;

   return true;
}



function enviarmensaje(id)
{
	var valor1 = document.getElementById('cajatexto').value;
	
	
	var parametros = {id:id , textoenviar:valor1};	
	$.ajax({
		url:'administrador/enviarmensajepm',
		data:parametros,
		type:"POST",
		
			success:function(res)
			{
				//alert("Hola");
				
			}
		
		  });
	
}
</script>

<div id="popUp">
	<div class="formA">
		<form action="" method="post">
			<header>
				<h1>Enviar mensaje privado</h1>
				<p>A <a href="store/<?php echo $seudonimo;?>" title="Ver perfil de <?php echo $seudonimo;?>"><strong><?php echo $seudonimo;?></strong></a> <span class="green">+<?php echo $posi;?></span> 
				<?php if($neg>0){?>
					<span class="red">-<?php echo $neg;?></span>
				<?php  }?>
				</p>
			</header>
			<div class="wrap">
				<div class="line">
					<label for="">Mensaje:</label>
					<p><textarea rows="4" cols="" class="w100" id="cajatexto"></textarea></p>
				</div>
			</div><!--wrap-->
			<footer>
				<p class="actions">
					<input type="submit" class="bt" value="Enviar" onclick="enviarmensaje('<?php echo $id;?>')"/>
					<span class="mhm">o</span>
					<a class="nyroModalClose">cancelar</a>
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