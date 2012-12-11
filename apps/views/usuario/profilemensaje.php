<div class="wrapper clearfix">
	<?php
	// print_r($mensaje);
	// var_dump($mensaje);
	// print_r($this->myuser);
	$this->load->view ( "usuario/cabecera-perfil", array (
			"seccion" => "perfil" 
	) )?>
	<header class="cont-cab">
		<h1><?php echo $seudonimo.' y '.$seudonimoemisor; ?></h1>
		<p>
			<a href="<?php echo 'store/'.$seudonimo.'/messages'?>" title="">Volver
				a mis mensajes</a>
		</p>

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
					if(datos==3)
					{
						location.href="login" ;
					}
				}
			});
		
	  	
	}

	/*function guarda_nuevomensaje(receptor,emisor)
	{		
		
		var texto = $("#textareanuevomensaje").val();
		texto = (texto.split('\n')).join('<br />');

		var parametros = { receptor: receptor , emisor: emisor ,mensaje:(texto)}
		if((texto != "Responder") && (texto !=""))
		{
		$.ajax({
			url:'usuario/guarda_nuevomensaje',
			data:parametros,
			type:"POST",
			datatype: "json",
			beforeSend: function()
			{
				
				$("#limensajenuevo").fadeOut(1);
			 	$("#imgnuevomensaje").fadeIn("slow");
			},
			success:function(res)
			{
				if(res != 3)
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
				else
				{
					location.href="login" ;
				}
            	
			}
		});
		}
	}*/

	function guarda_nuevomensaje(receptor,emisor)
	{		
		var texto = $("#textareanuevomensaje").val();
		texto = (texto.split('\n')).join('<br />');
		if((texto != "Responder") && (G.util.trim(texto) !=""))
		{
		$("#formMensajes input[name='emisor']").val(emisor);
		$("#formMensajes input[name='receptor']").val(receptor);
		$("#formMensajes input[name='mensaje']").val($("#textareanuevomensaje").val());
		$("#formMensajes").submit();
		/*$.ajax({
			url:'usuario/guarda_nuevomensaje',
			data:parametros,
			type:"POST",
			datatype: "json",
			beforeSend: function()
			{
				
				$("#limensajenuevo").fadeOut(1);
			 	$("#imgnuevomensaje").fadeIn("slow");
			},
			success:function(res)
			{
				if(res != 3)
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
				else
				{
					location.href="login" ;
				}
            	
			}
		});*/
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
				if(datos == 3)
				{
					location.hred="login";
				}				
			}
		});

	}
	function successGuarda_NuevoMensaje(res){
		if(res != 3)
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
		else
		{
			location.href="login" ;
		}
    	}

	function listarMensaje(receptor,emisor)
	{
		
			page=$(this).attr("href");

			var hla= 'usuario/mostrarTotalMensaje';
					el=$(this);
					$.ajax({
						url:hla,
						data:"receptor="+receptor+"&emisor=" + emisor,
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
		var area = $('#textareanuevomensaje').val(); 
	    if(area == "Responder") 
	    { 
	    	$('#textareanuevomensaje').val('').empty();
	    }
	   
	} 
</script>

	</header>
	
<?php if($mensaje){?>

		<ul class="comments-list">

		<li class="thread">
			
			<?php
	
	if ($mensaje [0] ['seudonimo'] != $seudonimo) {
		?>
			<p class="edit">
				<a class="nmodal" title="Denunciar"
					href="home/modal/denunciar-mensaje2/denunciamensaje/<?php echo $mensaje[0]['emisor'];?>/<?php echo $mensaje[0]['id'];?>/<?php echo $mensaje[0]['receptor'];?>">Denunciar</a>
			</p>
			<?php
	}
	// verbaneo
	$varseudonimo = '';
	if ($mensaje [0] ['estadousuario'] != 'Baneado') {
		$varseudonimo = $mensaje [0] ['seudonimo'];
	} else {
		$varseudonimo = "<strike>" . $mensaje [0] ['seudonimo'] . "</strike>";
	}
	
	if ($varseudonimo == "Admin") {
		$varseudonimo = "El equipo de Lovende";
	}
	// finverbaneo
	
	?>
				<div class="avatar">
				<img src="<?php echo $mensaje[0]['dirimagen'];?>"
					alt="Imagen de perfil de <?php echo $mensaje[0]['seudonimo'];?>"
					width="64" />
			</div>
			<div class="user-comment">
				<p class="user-name">
					<strong><a href="store/<?=$mensaje[0]['seudonimo']?>"><?php echo $varseudonimo;?></a></strong> hace 
					<?php
	echo $mensaje [0] ['tiempo'] . '</p>';
	
	?>
					
					
				
				
				
				
				
				
				
				
				<div class="justify">

					<p>
							<?php
	if ($mensaje [0] ['nomarticulo']) {
		echo 'Desde <a href="';
		echo 'product/' . $mensaje [0] ['idarticulo'] . '"';
		echo 'title="' . $mensaje [0] ['nomarticulo'] . '">' . $mensaje [0] ['nomarticulo'] . '</a><br />';
	}
	echo $mensaje [0] ['mensaje'];
	?>
						</p>
					<br />
				</div>
					
					<?php
	if ($cantidadmensaje > 4) {
		$cantfaltante = $cantidadmensaje - 4;
		
		echo '<p id="vermas"><a href="javascript:listarMensaje(';
		echo "'" . $seudonimo . "','" . $seudonimoemisor . "');";
		echo '" title="Ver todos los mensajes" class="delete">Ver ';
		echo $cantfaltante . ' mensajes más</a></p>';
		
		$indice = $cantidadmensaje - 3;
		$limite = 3;
	} else {
		if ($cantidadmensaje == 2) {
			$indice = $cantidadmensaje - 1;
			$limite = 1;
		}
		if ($cantidadmensaje == 3) {
			$indice = $cantidadmensaje - 2;
			$limite = 2;
		}
		if ($cantidadmensaje == 4) {
			$indice = $cantidadmensaje - 3;
			$limite = 3;
		}
	}
	
	?>
					
				</div>

			<ul>
				<di id="div_dinamico_anim"></di>
				<?php
	// print_r($mensaje);
	
	if ($cantidadmensaje != 1) {
		$cont = 1;
		for($i = $indice; $cont <= $limite; $i ++) {
			echo '
							<img id="loader_gif' . $mensaje [$i] ['id'] . '" style="display: none;" alt="Ver más" src="assets/images/ico/ajax-loader-see-more.gif">
							<li id="li' . $mensaje [$i] ['id'] . '" style="float:left; width:100% ">
							<div id="grupo' . $mensaje [$i] ['id'] . '"  class="comment">
							<div class="avatar" style="padding-right: 10px; height: 50px; width: 30px;text-align: right;" ><img src="' . $mensaje [$i] ['dirimagen'] . '" alt="Imagen de perfil de ' . $mensaje [$i] ['seudonimo'] . '" align="right"/></div>';
			if ($mensaje [$i] ['seudonimo'] == $seudonimo) {
				echo '<p class="edit"><a class="editComment" title="Editar" href="#">Editar</a></p>';
			} else {
				?><p class="edit">
					<a class="nmodal" title="Denunciar"
						href="home/modal/denunciar-mensaje2/denunciamensaje/<?php echo $mensaje[$i]['emisor'];?>/<?php echo $mensaje[$i]['id'];?>/<?php echo $mensaje[$i]['receptor'];?>">Denunciar</a>
				</p>
							<?php
			}
			
			// verbaneo
			$varseudonimo = '';
			if ($mensaje [$i] ['estadousuario'] != 'Baneado') {
				$varseudonimo = $mensaje [$i] ['seudonimo'];
			} else {
				$varseudonimo = "<strike>" . $mensaje [$i] ['seudonimo'] . "</strike>";
			}
			
			if ($varseudonimo == "Admin") {
				$varseudonimo = "El equipo de Lovende";
			}
			// finverbaneo
			
			echo '<div class="user-comment">
							<p class="user-name"><strong><a href="store/' . $mensaje [$i] ['seudonimo'] . '">' . $varseudonimo . '</a></strong> hace ';
			
			echo $mensaje [$i] ['tiempo'] . '</p>';
			
			if ($mensaje [$i] ['nomarticulo']) {
				echo '<p>Desde <a href="';
				echo "product/" . $mensaje [$i] ['idarticulo'];
				echo '" title="">' . $mensaje [$i] ['nomarticulo'] . '</a></p>';
			}
			$cadena = $mensaje [$i] ['id'];
			$cambiar = $mensaje [$i] ['mensaje'];
			$cambiar = str_replace ( "<br />", "\n", "$cambiar" );
			echo '<div class="justify">
							<p id="mensaje' . $cadena . '">';
			print_r ( $mensaje [$i] ['mensaje'] );
			?>
							
							<?php
			
			echo '</p>
								
							</div>
							</div>
							</div><!--comment-->
							<div id="grupomensaje' . $cadena . '"  class="edit-comment">
							<p><textarea cols="" id="' . $cadena . '" rows="3">' . $cambiar . '</textarea></p>
							<p class="t-r">
							<input type="button" class="action" onClick="guarda_mensaje(' . $cadena . ');" value="Guardar cambios" /> | <input type="button" class="action" onClick="elimina_mensaje(' . $cadena . ');" value="Eliminar" /> | <input type="button" class="action cancelEditComment" value="Cancelar" />
							</p>
							</div>
							</li>
					
								
							';
			$cont = $cont + 1;
		}
	}
	
	?>
					<div id="divmensajenuevo"></div>
				<li><img id="imgnuevomensaje" style="display: none;" alt="Ver más"
					src="assets/images/ico/ajax-loader-see-more.gif"></li>

				<li id="limensajenuevo">
					<div class="comment-box">

						<p>
							<textarea id="textareanuevomensaje" name="textareanuevomensaje"
								cols="" rows="3" onBlur="textArea_blur()"
								onFocus="textArea_focus()">Responder</textarea>
						</p>
						<p class="t-r">
							<input type="submit" class="action"
								onClick="guarda_nuevomensaje('<?php echo $seudonimo."','".$seudonimoemisor?>');"
								value="Enviar" />
						</p>

					</div>
					<form id="formMensajes" target="iframeMensajes" method="post"
						style="display: none;" action="usuario/guarda_nuevomensaje">
						<input name="emisor" type="hidden" /> <input name="mensaje"
							type="hidden" /> <input name="receptor" type="hidden" />
					</form> <iframe src="about:blank" id="iframeMensajes"
						style="display: none;" name="iframeMensajes"
						onload="procesarRespuestaMensaje.call(this);"
						data-submit="successGuarda_NuevoMensaje"></iframe>
				</li>
			</ul>
		</li>
		<!--thread-->

	</ul>
	<!--comments-list-->
<?php }?>
</div>