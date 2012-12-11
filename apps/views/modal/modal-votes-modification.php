<?php 
//print_r($negcompra);
?>
<script type="text/javascript">
function isNumberKey(evt)
{
   var charCode = (evt.which) ? evt.which : event.keyCode
   if (charCode > 31 && (charCode < 48 || charCode > 57))
      return false;

   return true;
}



function buscarvotos(id,positivo,negativo)
{
	var valor1 = document.getElementById('txt1').value;
	var valor2 = document.getElementById('txt2').value;
	var valor3 = document.getElementById('txt3').value;
	var valor4 = document.getElementById('txt4').value;
	
	var parametros = {id:id , posiventa:valor1, posicompra:valor2, negventa:valor3, negcompra:valor4, positotal:positivo, negtotal:negativo};

	$.ajax({
		url:'administrador/guardarbdvotos',
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
				<h1>Votes modification</h1>
				<p>User <a href="store/<?php echo $seudonimo;?>" title="Ver perfil de Mundocombo"><strong><?php echo $seudonimo;?></strong></a> <span class="green">+<?php echo $posi;?></span> 
				<?php if($neg>0){?>
					<span class="red">-<?php echo $neg;?></span>
				<?php  }?></p>
			</header>
			<div class="wrap">
				<table class="no-border">
					<thead>
						<tr>
							<th>Type</th>
							<th class="t-r">Total</th>
							<th class="t-r">New</th>
						</tr>
					</thead>
					<tbody>
						<tr class="odd">
							<td class="green v-m">Selling positives</td>
							<td class="green t-r v-m">+<?php echo $posiventa;?></td>
							<td class="t-r"><input type="text" class="texto t-r" size="10" value="0" id="txt1" onkeypress="return isNumberKey(event)"/></td>
						</tr>
						<tr>
							<td class="green v-m">Buying positives</td>
							<td class="green t-r v-m">+<?php echo $posicompra;?></td>
							<td class="t-r"><input type="text" class="texto t-r" size="10" value="0" id="txt2" onkeypress="return isNumberKey(event)"/></td>
						</tr>
						<tr class="odd">
							<td class="red v-m">Selling negatives</td>
							<td class="red t-r v-m">-<?php echo $negventa;?></td>
							<td class="t-r"><input type="text" class="texto t-r" size="10" value="0" id="txt3" onkeypress="return isNumberKey(event)"/></td>
						</tr>
						<tr>
							<td class="red v-m">Buying negatives</td>
							<td class="red t-r v-m">-<?php echo $negcompra;?></td>
							<td class="t-r"><input type="text" class="texto t-r" size="10" value="0" id="txt4" onkeypress="return isNumberKey(event)"/></td>
						</tr>
					</tbody>
				</table>
			</div><!--wrap-->
			<footer>
				<p class="actions">
					<input type="submit" class="bt" value="Send" onclick="buscarvotos('<?php echo $id."','".$posi."','".$neg;?>')">
					<span class="mhm">or</span>
					<a class="nyroModalClose">cancel</a>
				</p>
			</footer>
		</form>
	</div><!--formA-->
</div>

<script type="text/javascript">
	$(function() {
 	 	$('.nmodal').nyroModal();
	});
</script>