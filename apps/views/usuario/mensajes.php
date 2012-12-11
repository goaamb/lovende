
<div class="wrapper clearfix">
	<?php
	//print_r($this->myuser);
	//print_r($mensaje);
	//var_dump($mensaje);
	
	$this->load->view ( "usuario/cabecera-perfil", array (
			"seccion" => "perfil" 
	) )?>

	<header class="cont-cab">
		<h1><?php if (count($mensaje) >0){echo "Mis mensajes";}else{echo "Mensajes";}?></h1>
		<p>
		<?php
		//verlink
		
			 if($verlink == 'no')
			 {
			 	$link = 'messageprofile/'.$seudonimo.'/0/unread';
			 	$descripcionlink = 'Ver sólo mensajes no leídos';
			 }	
			 else
			 {
			 	$link = 'store/'.$seudonimo.'/messages';
			 	$descripcionlink = 'Ver todos los mensajes';
			 }	
			?>
			<a href="delete.php" class="delete">Eliminar seleccionados</a> | <a
				href="<?php echo $link;?>"
				title="<?php echo $descripcionlink;?>"><?php echo $descripcionlink;?></a> | <a href="delete.php"
				class="deletee">marcar como no leídos</a> | <?php echo count ( $mensaje );?>
			mensajes, mostrando del <strong><?php if (count($mensaje)>0){echo 1;}else{ echo 0;}?></strong>
			al <strong><?php
			
			if (count ( $mensaje ) > 0) {
				echo count ( $mensaje );
			} else {
				echo 0;
			}
			?></strong>
			<?php
		 
			?>
		</p>
		
		
		
		<!--<a href="#" title="vender artículo" class="action"><strong>+ Vender artículo</strong></a>-->
		<script type="text/javascript">
		
$(function(){
	$("a.delete").click(function(){
		page=$(this).attr("href");
		ids=new Array();
		a=0;
		$("input.chk:checked").each(function(){
			ids[a]=$(this).val();
			a++;
		});
				
		//if(confirm("Esta seguro de eliminar los mensajes seleccionado?")){
			
				el=$(this);
				$.ajax({
					url:'usuario/eliminarMensaje',
					data:"id="+ids,
					type:"GET",
					beforeSend: function()
					{
						$("#tablaajax").fadeOut(0);
					 	$("#pimagenajax").fadeIn("slow");
					},
					success:function(res)
					{
						if(res==1)
						{
							$("input.chk:checked").each(function(){
								$(this).parent().parent().remove();
							});
						}
						if(res==3)
						{
							location.href="login" ;
						}
						$("#pimagenajax").fadeOut(0);
					 	
					},
					complete: function(){
						$("#tablaajax").fadeIn("slow");
					   }
				});
		//}
		return false;
	});

	$("a.deletee").click(function(){
		page=$(this).attr("href");
		ids=new Array();
		a=0;
		$("input.chk:checked").each(function(){
			ids[a]=$(this).val();
			a++;
		});
				
		//if(confirm("Esta seguro de realizar esta operacion?")){
			
				el=$(this);
				$.ajax({
					url:'usuario/cambiarEstadoMensajeUnico',
					data:"id="+ids,
					type:"GET",
					beforeSend: function()
					{
						$("#tablaajax").fadeOut(0);
					 	$("#pimagenajax").fadeIn("slow");
					},
					success:function(res)
					{
						if(res==1)
						{
							location.reload(true);
						}
						if(res==3)
						{
							location.href="login" ;
						}

						$("#pimagenajax").fadeOut(0);
					},
					complete: function(){
						$("#tablaajax").fadeIn("slow");
					   }
				});
		//}
		return false;
	});
});

function listamensaje(seudonimo,limite)
{
	var parametros = { seudonimo: seudonimo , limite: limite }
	
	$.ajax({
		url:'usuario/listamensaje',
		data:parametros,
		type:"POST",
		
			success:function(res)
			{
				 $("#listanueva").append(res);
				 
				 cambiarvermas(limite,seudonimo);
			}
		
		  });
	
}
function cambiarvermas(limite,seudonimo)
{
	var parametros = { seudonimo: seudonimo , limite: limite }
	$.ajax({
		url:'usuario/vermas',
		data:parametros,
		type:"POST",
		
			success:function(res)
			{
				$("#linkvermas").empty();
				$("#linkvermas").append(res);
			}
		
		  });
	
	
}
</script>
	</header>
<?php if (count($mensaje) >0)
			{
				?>
	<p id="pimagenajax" align=center style="display: none;"><br/><img id="loader_gifajax" alt="Realizar accion" src="assets/images/ico/ajaxLoader.gif"> <br/><br/><br/></p>	
	<table id="tablaajax" >
		<tbody id="listanueva">
		
			<?php
				$cont= 1;
			$i = 0;
					foreach ( $mensaje as $row ) 
					{
						echo '<tr class="';
						if ($row ['estado'] == 'Pendiente') {
						echo ' red';
						}
						if ($i == count ( $mensaje ) - 1) {
							echo ' last-child';
						}
						
						//verbaneo
						$varseudonimo = '';
							if($row['estadousuario'] != 'Baneado')
							{
								$varseudonimo = $row['seudonimo'];
							}
							else
							{
								$varseudonimo = "<strike>".$row['seudonimo']."</strike>";
							}
							if($varseudonimo == "ADMIN-LOVENDE")
							{
								$varseudonimo = "El equipo de Lovende";
							}
						//finverbaneo
						
						echo '">';
						echo '<td width="15"><input type="checkbox" value="'.$row['valores'].'" name="chk[]" class="chk" /></td>
							<td class="td-item"><a href="'.$row['direccion'].'"><img src="'.$row['dirimagen'].'"
							alt="Avatar de '.$row['seudonimo'].'" class="imagen" style="padding-left: 0px; padding-right: 0px;" width="64"/> <strong>';
						echo $varseudonimo."</strong></a>";
						echo ' <span
							class="when">hace ';
						
						echo $row['tiempo'].'</span><br />';
						
						if ($row['nomarticulo'])
						{
						 echo 'Desde <a href="';
						 echo 'product/'.$row['idarticulo'].'"';
							echo 'title="'.$row['nomarticulo'].'">'.$row['nomarticulo'].'</a><br />';	
						}
						
						 
						
						echo $row['mensaje'];
					
						echo '</td>
							</tr>';
						$cont++;
						$i++;
					}
			
			?>
		
			
		</tbody>
	</table>

	<p id="linkvermas" class="ver-mas">
	<?php
	if ($cont > $this->configuracion->variables ( "cantidadPaginacion" )) {
		$limite = 1;
		
		?>
		<a
			href="javascript:listamensaje('<?php echo $seudonimo."','".$limite;?>')"
			title='Ver más ventas'>Ver más</a><?php
	}
}
else
{ ?>
</br>
	<div class="justify">
		<p ALIGN=center><FONT SIZE=3 FACE="arial">No se encontraron mensajes</font></p>
		<br>
		<br>
	</div>
	<p id="linkvermas" class="ver-mas">
	<?php 
}
?>
	</p>
</div>