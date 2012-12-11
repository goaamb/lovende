<div class="wrapper clearfix">
	<?php
	//print_r($cantidadmensaje);
	$this->load->view ( "usuario/cabecera-perfil", array (
			"seccion" => "perfil" 
	) )?>
	<header class="cont-cab">
			<h1><?php echo $seudonimo.' y '.$seudonimoemisor; ?></h1>
			<p><a href="<?php echo 'store/'.$seudonimo.'/messages'?>" title="">Volver a mis mensajes</a></p>
			
	<script type="text/javascript">  
	
	
	function elimina_mensaje(id)
	{		
		
		
			$.ajax({
				url:'usuario/eliminarMensajeBD',
				data:"id="+id,
				type:"GET",
				beforeSend: function()
				{
					$("#li"+id+"").remove();
				 	$("#loader_gif"+id+"").fadeIn("slow");
				},
				success: function(datos)
				{
					if(datos==1)
					{
						$("#loader_gif"+id+"").fadeOut(1);
						
						
					}
				}
			});
		
	  	
	}

	function guarda_nuevomensaje(receptor,emisor)
	{		
		
		var texto = $("#textareanuevomensaje").val();
		texto = (texto.split('\n')).join('<br />');
		if((texto != "Responder") && (texto !=""))
		{
		$.ajax({
			url:'usuario/guarda_nuevomensajedesdehilo',
			data:"receptor=" +receptor+ "&emisor=" + emisor + "&mensaje=" + (texto)+ "&vermas=no",
			type:"GET",
			beforeSend: function()
			{
				
				$("#limensajenuevo").fadeOut(1);
			 	$("#imgnuevomensaje").fadeIn("slow");
			},
			success:function(res)
			{
				 $('#textareanuevomensaje').val('Responder').empty();				
							
			    $("#divmensajenuevo").append(res);
			    
				$("#limensajenuevo").fadeIn("slow");
			 	$("#imgnuevomensaje").fadeOut(1);

			    //editComment
            	$('#divmensajenuevo .editComment').click(function(){
            		$(this).parents('div.comment').hide();
            		$(this).parents('li').children('.edit-comment').show();
            		return false;
            	});
            	//cancelEditComment
            	$('#divmensajenuevo .cancelEditComment').click(function(){
            		$(this).parents('div.edit-comment').hide();
            		$(this).parents('li').children('div.comment').show();
            		return false;
            	});
            	
			}
		});
		}
	}
	
	function guarda_mensaje(id)
	{		
		var texto = $("#"+id+"").val();
		texto = (texto.split('\n')).join('<br />');
	  	$.ajax({
			url:'usuario/guardarMensajeBD',
			data:"id="+id +"&mensaje="+texto,
			type:"GET",
			beforeSend: function()
			{
				
				$("#grupomensaje"+id+"").fadeOut(1);
			 	$("#loader_gif"+id+"").fadeIn("slow");
			},
			success: function(datos)
			{
				if(datos==1)
				{
					$("#loader_gif"+id+"").fadeOut(1);
					$("#mensaje"+id+"").empty();
					
				    $("#mensaje"+id+"").append(texto);
				    $("#grupo"+id+"").fadeIn("slow");
				    
					 
				}
				
			}
		});

	}

	function listarMensaje(receptor,emisor)
	{		
			page=$(this).attr("href");

			var hla= 'usuario/guarda_nuevomensajedesdehilo';
					el=$(this);
					$.ajax({
						url:hla,
						data:"receptor=" + receptor + "&emisor=" + emisor + "&mensaje=nulo&vermas=si",
						type:"GET",
						success:function(res)
						{
							$("#vermas").remove();
							$('#div_dinamico_anim').html(res);  
							$('#div_dinamico_anim li').hide();
			                $('#div_dinamico_anim li').slideDown("slow");
				             // editComment
			            	$('#div_dinamico_anim .editComment').click(function(){
			            		$(this).parents('div.comment').hide();
			            		$(this).parents('li').children('.edit-comment').show();
			            		return false;
			            	});
			            	//cancelEditComment
			            	$('#div_dinamico_anim .cancelEditComment').click(function(){
			            		$(this).parents('div.edit-comment').hide();
			            		$(this).parents('li').children('div.comment').show();
			            		return false;
			            	});
			            	
						}
					});
			
			//return false;
		
	}
	
	function textArea_blur(id) 
	{ 
		
	    var texto = new String(); 
	    texto = "Escribe algo aquí..."; 
	    var area = $('#textareanuevomensaje').val(); 
	    if(area == "") 
	    { 
	    	$('#textareanuevomensaje').val('Responder').empty();
	    } 
	} 

	function textArea_focus(id) 
	{ 
		$('#textareanuevomensaje').val('').empty();
	} 
</script>  
			
	</header>
	

		<ul class="comments-list">

			<li class="thread">

				<div class="avatar"><img src="<?php echo $mensaje[0]['dirimagen'];?>" alt="Imagen de perfil de <?php echo $mensaje[0]['emisor'];?>" width="64" height="64"/></div>
				<div class="user-comment">
					<p class="user-name"><strong><?php echo $mensaje[0]['emisor'];?></strong> hace 
					<?php
					echo $mensaje[0]['tiempo'].'</p>';
					
					?>
					<div class="justify">
						
						<p>
							<?php
							
						 	echo $mensaje[0]['mensaje'];?>
						</p>
					</div>
					<?php 
						if ($cantidadmensaje > 4)
						{
							$cantfaltante = $cantidadmensaje - 4;
							
							echo '<p id="vermas"><a href="javascript:listarMensaje(';
							echo "'".$seudonimo."','".$seudonimoemisor."');";
							echo '" title="Ver todos los mensajes" class="delete">Ver ';
							echo $cantfaltante.' mensajes más</a></p>';
							 
							$indice = $cantidadmensaje-3;
							$limite = 3;
						}
						else
						{
							if($cantidadmensaje == 2)
							{
								$indice = $cantidadmensaje-1;
								$limite = 1;
							}
							if($cantidadmensaje == 3)
							{
								$indice = $cantidadmensaje-2;
								$limite = 2;
							}
							if($cantidadmensaje == 4)
							{
								$indice = $cantidadmensaje-3;
								$limite = 3;
							}
							
						}
				
					?>
					
				</div>
				<ul>
				<di  id="div_dinamico_anim"></di>
				<?php 
				//print_r($mensaje);
				
					if ($cantidadmensaje != 1)
					{
						$cont = 1;
						for ( $i = $indice ; $cont <= $limite; $i++)
						{
						echo '
							<img id="loader_gif'.$mensaje[$i]['id'].'" style="display: none;" alt="Ver más" src="assets/images/ico/ajax-loader-see-more.gif">
							<li id="li'.$mensaje[$i]['id'].'" style="float:left; width:100%">
							<div id="grupo'.$mensaje[$i]['id'].'"  class="comment">
							<div class="avatar" style="padding-right: 10px; height: 50px; width: 30px;text-align: right;" ><img src="'.$mensaje[$i]['dirimagen'].'" alt="Imagen de perfil de '.$mensaje[$i]['emisor'].'" align="right" width="32" height="32"/></div>';
							if($mensaje[$i]['emisor'] == $seudonimo)
							{
							echo'<p class="edit"><a class="editComment" title="Editar" href="#">Editar</a></p>';
					
							}
								
							echo '<div class="user-comment">
							<p class="user-name"><strong>'.$mensaje[$i]['emisor'].'</strong> hace ';
								
								
							echo $mensaje[$i]['tiempo'].'</p>';
					
							
							$cadena = $mensaje[$i]['id'];
							$cambiar = $mensaje[$i]['mensaje'];
							$cambiar = str_replace( "<br />", "\n", "$cambiar" );
							echo '<div class="justify">
							<p id="mensaje'.$cadena.'">'.$mensaje[$i]['mensaje'].'</p>
								
							</div>
							</div>
							</div><!--comment-->
							<div id="grupomensaje'.$cadena.'"  class="edit-comment">
							<p><textarea cols="" id="'.$cadena.'" rows="3">'.$cambiar.'</textarea></p>
							<p class="t-r">
							<input type="button" class="action" onClick="guarda_mensaje('.$cadena.');" value="Guardar cambios" /> | <input type="button" class="action" onClick="elimina_mensaje('.$cadena.');" value="Eliminar" /> | <input type="button" class="action cancelEditComment" value="Cancelar" />
							</p>
							</div>
							</li>
					
								
							';
							$cont = $cont +1;
						}
						}
							
				
				
				?>
					<div id="divmensajenuevo">
					
					</div>
					<li><img id="imgnuevomensaje" style="display: none;" alt="Ver más" src="assets/images/ico/ajax-loader-see-more.gif"></li>
					
					<li id="limensajenuevo">
						<div class="comment-box">
							
								<p><textarea id="textareanuevomensaje" name="textareanuevomensaje" cols="" rows="3" onBlur="textArea_blur()" onFocus="textArea_focus()">Responder</textarea></p>
								<p class="t-r"><input type="submit" class="action" onClick="guarda_nuevomensaje('<?php echo $seudonimo."','".$seudonimoemisor?>');" value="Enviar" /></p>
							
						</div>
					</li>
				</ul>
			</li><!--thread-->

		</ul><!--comments-list-->


</div>