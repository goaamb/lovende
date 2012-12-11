<div class="wrapper clearfix">
	<?php
	//print_r($mensaje);
	$this->load->view ( "usuario/cabecera-perfil", array (
			"seccion" => "perfil" 
	) )?>
	<header class="cont-cab">
			<h1><?php echo $mensaje['seudonimo'] .' y '.$mensaje['emisor']; ?></h1>
			<p><a href="<?php echo 'store/'.$mensaje['seudonimo'].'/messages'?>" title="">Volver a mis mensajes</a></p>
			
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
			url:'usuario/guarda_nuevomensaje',
			data:"receptor="+receptor+"&emisor="+emisor +"&mensaje="+(texto),
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
		$('#textareanuevomensaje').val('').empty();
	} 
</script>  
			
	</header>

		<ul class="comments-list">

			<li class="thread">

				<div class="avatar"><img src="<?php echo $mensaje['imagen'];?>" alt="Imagen de perfil de <?php echo $mensaje['emisor'];?>" width="64" height="64"/></div>
				<div class="user-comment">
					<p class="user-name"><strong><?php echo $mensaje['emisor'];?></strong> hace 
					<?php
					echo $mensaje['tiempo'].'</p>';
					
					?>
					<div class="justify">
						
						<p>
							<?php							
						 	echo $mensaje['mensaje'];?>
						</p>
					</div>					
					
				</div>
				
			</li><!--thread-->

		</ul><!--comments-list-->

</div>