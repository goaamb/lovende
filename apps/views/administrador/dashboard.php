<?php
	//var_dump($reporte);
	//echo $this->myuser;
	//print_r($reporte);
	$muestranow = $this->configuracion->variables ( "cantidadPaginacion" );
?>
<div>
<?php 

$this->load->model ( "administrador_model", "administracion" );
$this->load->model ( "articulo_model", "articulo" );

if($reporte)
{
		foreach($reporte as $row)
		{
			if(($this->administracion->vermensajeXusuario($row->denunciante))== 0)
			{
				$data['seudonimo'] = $row->denuncianteseudonimo;
		
				
		
				//$this->load->view ( $view, $data);
			}
			else
			{
				$seudonimo = $row->denuncianteseudonimo;
				$view = "messageprofile/$seudonimo/ADMINLOVENDE/inboxadmin";
				//redirect( $view);
			}
		}
}
?>

</div>
<div class="content">
	<div class="wrapper clearfix">
		<header class="cont-cab">
			<div class="forms">
				<p>
					<span class="label-on-field">
						<label>Search by username</label>
						<input type="text" class="texto" onkeypress="buscar(event,value,'usuario')"/>
					</span>
					<span class="label-on-field">
						<label>Search by report ID</label>
						<input type="text" class="texto" onkeypress="buscar(event,value,'reporte')"/>
					</span>
					<span class="label-on-field">
						<label>Search by page ID</label>
						<input type="text" class="texto" onkeypress="buscar(event,value,'pageid')"/>
					</span>
				</p>
				
			</div>
			<h1>Dashboard</h1>
			<p>Reports | <a href="administration/billing" title="Billing">Billing</a> | <a href="administration/admipm" title="PM Admin">PM Admin</a> | <a href="administration/newsletter" title="@ Admin">@ Admin</a> | <a href="http://www.lovende.es/home/importarExcel" title="XLS Import">XLS Import</a> | <a href="http://www.lovende.es/home/importarCategorias" title="Add Ctg">Add Ctg</a> | <a href="http://www.lovende.es/home/categorias" title="Dl Ctg">Dl Ctg</a> | <a href="https://www.google.com/analytics/web/?pli=1#report/visitors-overview/a34423273w61970837p63501450/" title="Traffic">Traffic</a> | <a href="https://www.google.com/webmasters/tools/dashboard?hl=es&siteUrl=http%3A%2F%2Fwww.lovende.es%2F" title="Web Tools">Web Tools</a> | <a href="http://www.facebook.com/pages/Lovende/471203802892372" title="Fb">Fb</a> | <a href="https://twitter.com/Lovende_es" title="Tw">Tw</a> | <a href="https://plus.google.com/108545380659244060869/posts?hl=es" title="+G">+G</a> | <a href="https://developers.facebook.com/apps/" title="Fb Tools">Fb Tools</a> | <a href="http://www.lovende.es/cpanel" title="CP Admin">CP Admin</a> </p>
		
		</header>

<?php if($reporte)
{?>
		<div class="post">
		<?php if($vista =="seeall"){?>
			<p class="dark-grey">See all | <a href="administration/pending" title="See pending">only pending (<?php echo $pendiente;?>)</a> | <a href="administration/unmarked" title="See unmarked">only unmarked (<?php echo $nomarcado;?>)</a></p>
		<?php }?>
		<?php if($vista =="pending"){?>
			<p class="dark-grey"><a href="administration/dashboard" title="See pending">See all</a> | only pending (<?php echo $pendiente;?>)  | <a href="administration/unmarked" title="See unmarked">only unmarked (<?php echo $nomarcado;?>)</a></p>
		<?php }?>
		<?php if($vista =="unmarked"){?>
			<p class="dark-grey"><a href="administration/dashboard" title="See pending">See all</a> | <a href="administration/pending" title="See pending">only pending (<?php echo $pendiente;?>)</a> | only unmarked (<?php echo $nomarcado;?>)</p>
		<?php }?>
		</div>
<?php }
?>	
		
		<?php if(($reporte))
				{?>
		<table class="border mbl" id="tabladedatos">
			<thead>
				<tr>
					<th>Report ID</th>
					<th>Ext</th>
					<th>Date</th>
					<th>Cause</th>
					<th>Reporter</th>
					<th>Reported</th>
					<th>Page ID</th>
					<th>Status</th>
					<th>Marked by / Date</th>
				</tr>
			</thead>
			<tbody id="listanueva">
				<?php 
				
				foreach($reporte as $row)
				{
					
					//mensajes denunciante
					$clasecss = '';
					$status = TRUE;
					if(($this->administracion->vermensajeXusuario($row->denunciante))== 0)
					{
						$clasecss = 'nmodal';
					}
					
					//fin mensajes
				?>
				<tr id=fila<?php echo $row->id;?> <?php if($row->estadopaquete == 'Disputa'){ echo 'class="bg-red"';}else{if($row->estado == "Finalizado"){echo 'class="bg-green"';}else{if($row->estado == "Pendiente"){echo 'class="bg-yellow"';}}}?>>
					<td><?php echo $row->id;?></td>
					<td>es</td>
					<td><?php $fecha = new DateTime($row->fecha);
					echo $fecha->format('d-m-Y'); ?></td>
					<td> <?php 
								
						if($row->descripcion != ''){?>
						<a href="#" class="actMenuB"><?php
						if($row->mensaje != '')
						{
							echo "PM ";			
						}						
						echo $row->asunto;?></a>
						<div class="act-menu-b w454">
							<div class="cont">
								<p><?php echo $row->descripcion;?></p>
							</div>
							<div class="arrow"></div>
						</div>
						<?php }else{
							if($row->asunto == "Artículo Recibido diferente de la descripción del anuncio")
							{ ?><a href="#" class="actMenuB"><?php echo "Disputa 1";?>
								</a>
								<div class="act-menu-b w454">
								<div class="cont">
									<p><?php echo $row->asunto;?></p>
								</div>
								<div class="arrow"></div>
								</div>
							
							
							<?php }
							else
							{ 
								 if($row->asunto == 'No Pagado')
								 {
								 	echo "Unpaid";	
								 	$status = FALSE;
								 }
								 else
								 {
								 	echo $row->asunto;	
								 }						
							}
						
						}?>
					</td>
					<td>
						<a href="#" class="actMenuB"><?php if($row->estadou=='Baneado'){ echo '<strike>'.$row->denuncianteseudonimo.'</strike>';}else{ echo $row->denuncianteseudonimo;}?></a> <span class="green">+<?php echo $row->denunciantevotopos;?></span> 
						<?php if($row->denunciantevotoneg > 0){?>
						<span class="red">-<?php echo $row->denunciantevotoneg;?></span>
						<?php }?>
						<div class="act-menu-b">
							<div class="cont">
								<ul>
									<li><a href="store/<?php echo $row->denuncianteseudonimo;?>" target="_blank">Go to</a></li>
									<li><a href="javascript:historial('<?php echo $row->denuncianteseudonimo."','usuario";?>')">History</a></li>
									<li><a href="administrador/mostrarmodalmensaje?id=
										<?php echo $row->denunciante.'&seu='.$row->denuncianteseudonimo.'&posi='.$row->denunciantevotopos.'&neg='.$row->denunciantevotoneg;?>" class="<?php echo $clasecss;?>">Send PM</a></li>
									<li>
										<a href="administrador/mostrarmodalvotos?id=
										<?php echo $row->denunciante.'&seu='.$row->denuncianteseudonimo.'&posi='.$row->denunciantevotopos.'&neg='.$row->denunciantevotoneg;?>" 
										class="nmodal">Vote</a>
									</li>
									<?php if($row->estadou != 'Baneado'){?><li><a href="administrador/mostrarmodalbaneo?usuario=<?php echo $row->denunciante; ?>&seudonimo=<?php echo $row->denuncianteseudonimo;?>&tipo=Baneado&posi=<?php echo $row->denunciantevotopos;?>&neg=<?php echo $row->denunciantevotoneg;?>" class="nmodal">Ban</a></li><?php }else{?>
									<li><a href="administrador/mostrarmodalbaneo?usuario=<?php echo $row->denunciante; ?>&seudonimo=<?php echo $row->denuncianteseudonimo;?>&tipo=Activo&posi=<?php echo $row->denunciantevotopos;?>&neg=<?php echo $row->denunciantevotoneg;?>" class="nmodal">Unban</a></li><?php  }?>
								</ul>
							</div>
							<div class="arrow"></div>
						</div>
					</td>
					<td>
					<?php 
						$usuariocadena = $row->seuusuario;
						$votopositivo = $row->posiusuario;
						$votonegativo = $row->negusuario;
						$idusuario = $row->idusuario;
						$estadovar = $row->estadou2;
						if($row->seuusuarioarticulo != '')
							{
								$usuariocadena = $row->seuusuarioarticulo;
								$votopositivo = $row->posiusuarioarticulo;
								$votonegativo = $row->negusuarioarticulo;
								$idusuario = $row->idusuarioarticulo;
								$estadovar = $row->estadou3;
							}
							
						$clasecss = '';
						
						if(($this->administracion->vermensajeXusuario($idusuario))== 0)
						{
							$clasecss = 'nmodal';
						}
					?>
						<a href="#" class="actMenuB"><?php if($estadovar=='Baneado'){ echo '<strike>'.$usuariocadena.'</strike>';}else{ echo $usuariocadena;}?></a> <span class="green">+<?php echo $votopositivo;?></span> 
						<?php if($votonegativo > 0){?>						
						<span class="red">-<?php echo $votonegativo;?></span>
						<?php }?>
						
						<div class="act-menu-b">
							<div class="cont">
								<ul>
									<li><a href="store/<?php echo $usuariocadena;?>" target="_blank">Go to</a></li>
									<li><a href="javascript:historial('<?php echo $usuariocadena."','usuario";?>')">History</a></li>
									<li><a href="administrador/mostrarmodalmensaje?id=<?php echo $idusuario.'&seu='.$usuariocadena.'&posi='.$votopositivo.'&neg='.$votonegativo;?>" class="<?php echo $clasecss;?>">Send PM</a></li>
									<li><a href="administrador/mostrarmodalvotos?id=<?php echo $idusuario.'&seu='.$usuariocadena.'&posi='.$votopositivo.'&neg='.$votonegativo;?>" class="nmodal">Vote</a></li>
									<?php if($estadovar != 'Baneado'){?><li><a href="administrador/mostrarmodalbaneo?usuario=<?php echo $idusuario; ?>&seudonimo=<?php echo $usuariocadena;?>&tipo=Baneado&posi=<?php echo $votopositivo;?>&neg=<?php echo $votonegativo;?>&idreporte=<?php echo $row->id;?>" class="nmodal">Ban</a></li><?php }else{?>
									<li><a href="administrador/mostrarmodalbaneo?usuario=<?php echo $idusuario; ?>&seudonimo=<?php echo $usuariocadena;?>&tipo=Activo&posi=<?php echo $votopositivo;?>&neg=<?php echo $votonegativo;?>&idreporte=<?php echo $row->id;?>" class="nmodal">Unban</a></li><?php  }?>
								</ul>
							</div>
							<div class="arrow"></div>
						</div>
					</td>
					<td>
					<?php 
						
					$varbanedo = false;
					$banderapaquete = false;
						if($row->paquete != '')
						{
							
							
							//PArtir vadena
							
							
							
							$cadenapaquete = array();
							if($row->articulopaq != '')
							{
								$cantidad = substr_count($row->articulopaq, ',');
								if($cantidad > 0 )
								{
									$miarray = explode (',', $row->articulopaq);
									for($i = 0 ; $i<=$cantidad; $i++)
									{
										$cadenapaquete[] = $miarray[$i];
									}
								}
								else
								{
									$cadenapaquete[] = $row->articulopaq;
								}
							}
							
							if($row->transacciones != '')
							{
								foreach ( explode ( ",", $row->transacciones ) as $t ) 
								{				
									$tran = $this->articulo->darTransaccion($t);	
									if($tran)
									{
										$ar = ($this->articulo->darArticulo ( $tran->articulo ) );
										$cadenapaquete[] = $ar->id;
									}			
									
								}
							}
							
							//var_dump($cadenapaquete);
							
							//fin partir cadena
							$banderapaquete = true;
							$ok = 0;
							for($i = 0 ; $i< count($cadenapaquete); $i++)
							{
								$coma = ',';
								if($i == count($cadenapaquete)-1)
								{
									$coma = '';	
								}
								$idpagina = $cadenapaquete[$i];
								$pagina = "product/$idpagina";
								
								$datosXarticulo = $this->articulo->darArticulo($idpagina);
								
								$varbanedo = $datosXarticulo->estado;
								$estadocambiar = 'disable';
								
								if($varbanedo == 'Baneado')
								{
									$estadocambiar = 'enable';
									$idpagina = "<strike>$idpagina</strike>";
								}
								
								$linkpageid = "administrador/pageidenabledisable?id=$datosXarticulo->id&estadoanterior=$datosXarticulo->estado_anterior&accion=$estadocambiar&tipo=articulo";//"product/$cadenapaquete[$i]";
								
								?>
								<span>
									<a href="#" class="actMenuB"><?php echo $idpagina;?> </a> <?php echo $coma;?>
									<div class="act-menu-b">
										<div class="cont">
											<ul>
												<li><a href="<?php echo $pagina?>" target="_blank">Go to</a></li>
													<?php if($varbanedo == 'Baneado')
													{ ?>
												<li><a href="<?php echo $linkpageid;?>" class="nmodal">Enable</a></li>
													<?php }else{?><li><a href="<?php echo $linkpageid;?>" class="nmodal">Disable</a></li><?php }?>
											</ul>
										</div>
									<div class="arrow"></div>
									</div>
								</span>
								<?php 
								$ok++;
							}
							
						}
						else
						{
							if($row->idtitulo != '')
							{
								$pagina = "product/$row->idtitulo";
								$idpagina = "$row->idtitulo";
								?>
								<a href="#" class="actMenuB"><?php if($row->estado_articulo != "Baneado"){ echo $idpagina;
								$linkpageid = "administrador/pageidenabledisable?id=$row->idtitulo&estadoanterior=$row->estadoarticuloanterior&accion=disable&tipo=articulo";

								}
								else {echo "<strike>$idpagina</strike>";
								$linkpageid = "administrador/pageidenabledisable?id=$row->idtitulo&estadoanterior=$row->estadoarticuloanterior&accion=enable&tipo=articulo";
								$varbanedo = true;
								}?></a>
								<?php
							}
							else
							{	
								if($row->mensaje != '')
								{
									/*$pagina = $row->mensaje;
										$idpagina = "--";
										$linkpageid = "#";
										echo $idpagina;
									*/
									
									$pagina = "administrador/mensajereportador?id=$row->mensaje";
									$idpagina = "$row->mensaje";
									//$linkpageid = "#";
									 ?>
									<a href="#" class="actMenuB"><?php if($row->denuncia != ''){ echo $idpagina;
									$linkpageid = "administrador/pageidenabledisable?id=$row->mensaje&tipo=mensaje&accion=disable";
									}
									else
									{
										$linkpageid = "administrador/pageidenabledisable?id=$row->mensaje&tipo=mensaje&accion=enable";
										echo "<strike>$idpagina</strike>";
										$varbanedo = true;
									}
									?></a>
									<?php 
									
								}
								else
								{
									
										$pagina = "store/$row->seuusuario";
										$idpagina = "--";
										$linkpageid = "#";
										echo $idpagina;
										/*?>
										<a href="#" class="actMenuB"><?php echo $idpagina;?></a>
										<?php*/
									
								}
							}
						}
						if($idpagina == '')
						{
							$idpagina = '--';
							$pagina = 'administration/dashboard';
							$linkpageid = "#";
							echo $idpagina;
						}
						
						if($banderapaquete == false)
						{
					?>					
							<div class="act-menu-b">
								<div class="cont">
									<ul>
										<li><a href="<?php echo $pagina;?>" target="_blank">Go to</a></li>
										<?php if($varbanedo == true)
										{ ?>
										<li><a href="<?php echo $linkpageid;?>" class="nmodal">Enable</a></li>
										<?php }else{?><li><a href="<?php echo $linkpageid;?>" class="nmodal">Disable</a></li><?php }?>
									</ul>
								</div>
								<div class="arrow"></div>
							</div>
					<?php }?>	
					</td>
					<td>
					<?php if($status != FALSE){?>
						<a href="#" class="actMenuB">Mark as</a>
						<div class="act-menu-b">
							<div class="cont">
								<ul>
									<li><a href="javascript:cambiarestado('<?php echo $row->id."','Finalizado";?>')">Done</a></li>
									<li><a href="javascript:cambiarestado('<?php echo $row->id."','Pendiente";?>')">Pending</a></li>
									<li><a href="javascript:cambiarestado('<?php echo $row->id."','Procesando";?>')">Unmark</a></li>
								</ul>
							</div>
							<div class="arrow"></div>
						</div>
					<?php }else{ echo "--";}?>
					</td>
					<td>
					<?php
					$mostramarcadopor = '';
					$direccionmostramarcadopor = '';
							$fechaultimo = new DateTime($row->datemark);
							
								if($row->markby != '')
								{
									$mostramarcadopor = $row->markby. " ".$fechaultimo->format('d-m-Y');
									?><a href="store/<?php echo $row->markby;?>">
									<?php echo $mostramarcadopor;?>
					 				</a>
					 			<?php 
								}
								else
								{
									echo "--";
								}
							?>
					
					 </td>
				</tr>
				
				<?php
				}
				?>
			</tbody>
		</table>
		
		<?php if($cantidadtotal > $this->configuracion->variables ( "cantidadPaginacion" )){?>
		<p class="ver-mas" id="linkvermas"><a title="Ver más" href="javascript:vermaslista('<?php echo $cantidadtotal."','0','".$muestranow."','".$vista;?>')">Ver más</a></p>
		<?php }}
		else
		{?>
			</br>
			<div class="justify">
				<p ALIGN=center><FONT SIZE=2><b>No se encontraron reportes</b></font></p>
			</div>
		<?php }
		
		?>
		<script>
			$('.actMenuB').actMenuB();
		</script>

	</div><!--wrapper-->
</div><!--content-->

<script type="text/javascript">

function buscar(e, valor, tipo) {
	  tecla = (document.all) ? e.keyCode : e.which;
	  if (tecla==13)
	  {
		  //window.locationf="administrador/buscar?valor=" + valor + "&tipo=" + tipo ;
		  location.href="administrador/buscar?valor=" + valor + "&tipo=" + tipo ;
	  }
	}

function historial(valor, tipo) 
	{	  
		  location.href="administrador/buscar?valor=" + valor + "&tipo=" + tipo ;	  
	}

function cambiarestado(reporte,estado)
{
	
	var parametros = { reporte: reporte , estado: estado}
	$.ajax({
		url:'administrador/cambiarestado',
		data:parametros,
		type:"POST",
		
			success:function(res)
			{

				
				
				if(estado == 'Finalizado')
				{
					var elemento = document.getElementById('fila'+reporte);
					elemento.className = "bg-green";
					$("#fila" + reporte).empty();
					$("#fila" + reporte).append(res);
				}
				if(estado == 'Pendiente')
				{
					var elemento = document.getElementById('fila'+reporte);
					elemento.className = "bg-yellow";
					$("#fila" + reporte).empty();
					$("#fila" + reporte).append(res);
				}
				if(estado == 'Procesando')
				{
					var elemento = document.getElementById('fila'+reporte);
					elemento.className = "";
					$("#fila" + reporte).empty();
					$("#fila" + reporte).append(res);
					
				}
				
				
				$('.actMenuB'+ reporte).actMenuB();
				$('.nmodal'+ reporte).nyroModal();
			}
		
		  });	
	
}

function vermaslista(cantidadtotal, desde, muestranow, vista)
{
	var parametros = { cantidadtotal: cantidadtotal , desde: desde ,muestranow:muestranow ,vista:vista}
	$.ajax({
		url:'administrador/listarmas',
		data:parametros,
		type:"POST",
		
			success:function(res)
			{
				
				$("#listanueva").append(res);
				
				cambiarvermas(cantidadtotal, desde, muestranow, vista);
			}
		
		  });	
}

function cambiarvermas(cantidadtotal, desde, muestranow, vista)
{
	var parametros = { cantidadtotal: cantidadtotal , desde: desde ,muestranow:muestranow ,vista:vista}
	$.ajax({
		url:'administrador/cambiarvermas',
		data: parametros,
		type:"POST",

			success:function(res)
			{
				$(".actMenuB" + vista + desde).actMenuB();
				$('.nmodal'+  vista + desde).nyroModal();
				$("#linkvermas").empty();
				$("#linkvermas").append(res);
				
			}
		});
}




</script>