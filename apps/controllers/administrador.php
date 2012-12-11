<?php
require_once 'basecontroller.php';
class Administrador extends BaseController {
	public function __construct() {
		parent::__construct ();
		$this->load->helper ( 'url' );
		$this->load->model ( "administrador_model", "administracion" );
		$this->load->model ( 'usuario_model', 'objusuario' );
		$this->load->model ( "articulo_model", "articulo" );
	}
	public function index($pagina = false) {
		if ($this->myuser) {
			if ($this->myuser->tipo == 'Administrador') {
				$data = array ();
				switch ($pagina) {
					case "dashboard" :
						$data ['vista'] = 'seeall';
						$data ['cantidadtotal'] = $this->administracion->contarreporte ();
						$data ['pendiente'] = $this->administracion->contarreporte ( 'Pendiente' );
						$data ['nomarcado'] = $this->administracion->contarreporte ( 'Procesando' );
						$data ['reporte'] = $this->administracion->datos ( false, false, false, 0, $this->configuracion->variables ( "cantidadPaginacion" ) );
						$view = "administrador/dashboard";
						break;
					case "pending" :
						$data ['vista'] = 'pending';
						$data ['cantidadtotal'] = $this->administracion->contarreporte ();
						$data ['pendiente'] = $this->administracion->contarreporte ( 'Pendiente' );
						$data ['nomarcado'] = $this->administracion->contarreporte ( 'Procesando' );
						$data ['reporte'] = $this->administracion->datos ( 'Pendiente', false, false, 0, $this->configuracion->variables ( "cantidadPaginacion" ) );
						$view = "administrador/dashboard";
						break;
					case "unmarked" :
						$data ['vista'] = 'unmarked';
						$data ['cantidadtotal'] = $this->administracion->contarreporte ();
						$data ['pendiente'] = $this->administracion->contarreporte ( 'Pendiente' );
						$data ['nomarcado'] = $this->administracion->contarreporte ( 'Procesando' );
						$data ['reporte'] = $this->administracion->datos ( 'Procesando', false, false, 0, $this->configuracion->variables ( "cantidadPaginacion" ) );
						$view = "administrador/dashboard";
						break;
					case "billing" :
						$data ['vista'] = 'seeall';
						$fechaclasificado = date ( "m" ) . "-" . date ( "Y" );
						$data ['cantidadtotal'] = $this->administracion->contarfactura ( false, $fechaclasificado );
						$data ['curso'] = count ( $this->administracion->devolverfactura ( 'En curso', false, false, $fechaclasificado ) );
						$data ['pendiente'] = $this->administracion->contarfactura ( 'Pendiente', $fechaclasificado );
						$data ['pagado'] = $this->administracion->contarfactura ( 'Pagado', $fechaclasificado );
						
						$data ['reporte'] = $this->administracion->devolverfactura ( false, false, false, $fechaclasificado );
						$data ['grupofechas'] = $this->administracion->devuelvegrupofechas ();
						$data ['valordefecto'] = $fechaclasificado;
						
						$view = "administrador/billing";
						break;
					case "curse" :
						$data ['vista'] = 'curse';
						$fechaclasificado = date ( "m" ) . "-" . date ( "Y" );
						$data ['cantidadtotal'] = $this->administracion->contarfactura ( false, $fechaclasificado );
						$data ['curso'] = count ( $this->administracion->devolverfactura ( 'En curso', false, false, $fechaclasificado ) );
						$data ['pendiente'] = $this->administracion->contarfactura ( 'Pendiente', $fechaclasificado );
						$data ['pagado'] = $this->administracion->contarfactura ( 'Pagado', $fechaclasificado );
						
						$data ['reporte'] = $this->administracion->devolverfactura ( 'En curso', $valor = false, $tipo = false, $fechaclasificado );
						$data ['valordefecto'] = $fechaclasificado;
						
						$view = "administrador/billing";
						break;
					case "facpending" :
						$data ['vista'] = 'pending';
						$fechaclasificado = date ( "m" ) . "-" . date ( "Y" );
						$data ['cantidadtotal'] = $this->administracion->contarfactura ( false, $fechaclasificado );
						$data ['curso'] = count ( $this->administracion->devolverfactura ( 'En curso', false, false, $fechaclasificado ) );
						$data ['pendiente'] = $this->administracion->contarfactura ( 'Pendiente', $fechaclasificado );
						$data ['pagado'] = $this->administracion->contarfactura ( 'Pagado', $fechaclasificado );
						
						$data ['reporte'] = $this->administracion->devolverfactura ( 'Pendiente', $valor = false, $tipo = false, $fechaclasificado );
						$data ['valordefecto'] = $fechaclasificado;
						$view = "administrador/billing";
						break;
					case "paid" :
						$data ['vista'] = 'paid';
						$fechaclasificado = date ( "m" ) . "-" . date ( "Y" );
						$data ['cantidadtotal'] = $this->administracion->contarfactura ( false, $fechaclasificado );
						$data ['curso'] = count ( $this->administracion->devolverfactura ( 'En curso', false, false, $fechaclasificado ) );
						$data ['pendiente'] = $this->administracion->contarfactura ( 'Pendiente', $fechaclasificado );
						$data ['pagado'] = $this->administracion->contarfactura ( 'Pagado', $fechaclasificado );
						
						$data ['reporte'] = $this->administracion->devolverfactura ( 'Pagado', $valor = false, $tipo = false, $fechaclasificado );
						$data ['valordefecto'] = $fechaclasificado;
						$view = "administrador/billing";
						break;
					
					case "admipm" :
						$data ['idadmin'] = $this->myuser->id;
						$data ['mensaje'] = $this->administracion->mensajeadmin ();
						$view = "administrador/admipm";
						break;
					
					case "messagereport" :
						$data ['mensaje'] = $this->administracion->devolvermensajeX ( $pagina );
						$view = "administrador/mensajedenuncia";
						break;
					default :
						$data ['vista'] = 'seeall';
						$data ['cantidadtotal'] = $this->administracion->contarreporte ();
						$data ['pendiente'] = $this->administracion->contarreporte ( 'Pendiente' );
						$data ['nomarcado'] = $this->administracion->contarreporte ( 'Procesando' );
						$data ['reporte'] = $this->administracion->datos ( false, false, false, 0, $this->configuracion->variables ( "cantidadPaginacion" ) );
						$view = "administrador/dashboard";
						break;
				}
				$this->loadGUI ( $view, $data );
			} else {
				redirect ( "login", "refresh" );
			}
		} else {
			redirect ( "login", "refresh" );
			return;
		}
	}
	public function mensajereportador() {
		$data ['mensaje'] = $this->administracion->devolvermensajeX ( $this->input->get ( 'id' ) );
		$view = "administrador/mensajedenuncia";
		$this->loadGUI ( $view, $data );
	}
	public function buscar() {
		$data ['vista'] = 'seeall';
		$data ['pendiente'] = $this->administracion->contarreporte ( 'Pendiente' );
		$data ['nomarcado'] = $this->administracion->contarreporte ( 'Procesando' );
		$data ['cantidadtotal'] = $this->administracion->contarreporte ();
		
		$valor = $this->input->get ( 'valor' );
		$tipo = $this->input->get ( 'tipo' );
		
		$data ['reporte'] = $this->administracion->datos ( false, $valor, $tipo, 0, $this->configuracion->variables ( "cantidadPaginacion" ) );
		
		$view = "administrador/dashboard";
		$this->loadGUI ( $view, $data );
	}
	public function mostrarmodalvotos() {
		$id = $this->input->get ( 'id' );
		$data ['id'] = $id;
		
		$data ['seudonimo'] = $this->input->get ( 'seu' );
		
		$data ['posi'] = $this->input->get ( 'posi' );
		$data ['neg'] = $this->input->get ( 'neg' );
		
		$resultado = $this->administracion->devolverdatousuario ( $id, 'Positivo', 'Venta' );
		foreach ( $resultado as $row ) {
			if ($row->cantidad != '') {
				$data ['posiventa'] = $row->cantidad;
			} else {
				$data ['posiventa'] = 0;
			}
		}
		
		$resultado = $this->administracion->devolverdatousuario ( $id, 'Positivo', 'Compra' );
		foreach ( $resultado as $row ) {
			if ($row->cantidad != '') {
				$data ['posicompra'] = $row->cantidad;
			} else {
				$data ['posicompra'] = 0;
			}
		}
		
		$resultado = $this->administracion->devolverdatousuario ( $id, 'Negativo', 'Venta' );
		foreach ( $resultado as $row ) {
			if ($row->cantidad != '') {
				$data ['negventa'] = $row->cantidad;
			} else {
				$data ['negventa'] = 0;
			}
		}
		
		$resultado = $this->administracion->devolverdatousuario ( $id, 'Negativo', 'Compra' );
		foreach ( $resultado as $row ) {
			if ($row->cantidad != '') {
				$data ['negcompra'] = $row->cantidad;
			} else {
				$data ['negcompra'] = 0;
			}
		}
		
		// $data['negventa'] =
		// $this->administracion->devolverdatousuario($id,'Negativo','Venta');
		// $data['negcompra'] =
		// $this->administracion->devolverdatousuario($id,'Negativo','Compra');
		
		$view = "modal/modal-votes-modification";
		$this->load->view ( $view, $data );
	}
	public function guardarbdvotos() {
		$id = $this->input->post ( 'id' );
		
		$posiventa = $this->input->post ( 'posiventa' );
		$posicompra = $this->input->post ( 'posicompra' );
		$negventa = $this->input->post ( 'negventa' );
		$negcompra = $this->input->post ( 'negcompra' );
		
		$positivo = $this->input->post ( 'positotal' );
		$negativo = $this->input->post ( 'negtotal' );
		
		$this->administracion->guardarvoto ( $id, $positivo, $negativo, $posiventa, $posicompra, $negventa, $negcompra );
		
		echo '1';
	}
	public function cambiarestado() {
		$id = $this->input->post ( 'reporte' );
		$estado = $this->input->post ( 'estado' );
		$usuario = $this->myuser->id;
		$this->administracion->cambiarestado ( $id, $estado, $usuario );
		
		$reporteX = $this->administracion->devolverXrepoter ( $id );
		
		if ($reporteX) {
			foreach ( $reporteX as $row ) {
				$clasecss = '';
				$status = TRUE;
				if (($this->administracion->vermensajeXusuario ( $row->denunciante )) == 0) {
					$clasecss = 'nmodal';
				}
				?>
<td><?php echo $row->id;?></td>
<td>es</td>
<td><?php
				
				$fecha = new DateTime ( $row->fecha );
				echo $fecha->format ( 'd-m-Y' );
				?></td>
<td> <?php if($row->descripcion != ''){?>
						<a href="#" class="actMenuB<?php echo $row->id;?>"><?php
					
					if ($row->mensaje != '') {
						echo "PM ";
					}
					
					echo $row->asunto;
					?></a>
	<div class="act-menu-b w454">
		<div class="cont">
			<p><?php echo $row->descripcion;?></p>
		</div>
		<div class="arrow"></div>
	</div>
						<?php
				} else {
					if ($row->asunto == "Artículo Recibido diferente de la descripción del anuncio") {
						?><a href="#" class="actMenuB<?php echo $row->id;?>"><?php echo "Disputa 1";?>
								</a>
	<div class="act-menu-b w454">
		<div class="cont">
			<p><?php echo $row->asunto;?></p>
		</div>
		<div class="arrow"></div>
	</div>
							
							
							<?php
					} else {
						if ($row->asunto == 'No Pagado') {
							echo $row->asunto;
							$status = FALSE;
						} else {
							echo $row->asunto;
						}
					}
				}
				?>
					</td>
<td><a href="#" class="actMenuB<?php echo $row->id;?>"><?php if($row->estadou=='Baneado'){ echo '<strike>'.$row->denuncianteseudonimo.'</strike>';}else{ echo $row->denuncianteseudonimo;}?></a>
	<span class="green">+<?php echo $row->denunciantevotopos;?></span> 
						<?php if($row->denunciantevotoneg > 0){?>
						<span class="red">-<?php echo $row->denunciantevotoneg;?></span>
						<?php }?>
						<div class="act-menu-b">
		<div class="cont">
			<ul>
				<li><a href="store/<?php echo $row->denuncianteseudonimo;?>"
					target="_blank">Go to</a></li>
				<li><a
					href="javascript:historial('<?php echo $row->denuncianteseudonimo."','usuario";?>')">History</a></li>
				<li><a
					href="administrador/mostrarmodalmensaje?id=
										<?php echo $row->denunciante.'&seu='.$row->denuncianteseudonimo.'&posi='.$row->denunciantevotopos.'&neg='.$row->denunciantevotoneg;?>"
					class="<?php echo $clasecss;?>">Send PM</a></li>
				<li><a
					href="administrador/mostrarmodalvotos?id=
										<?php echo $row->denunciante.'&seu='.$row->denuncianteseudonimo.'&posi='.$row->denunciantevotopos.'&neg='.$row->denunciantevotoneg;?>"
					class="nmodal<?php echo $row->id;?>">Vote</a></li>
									<?php if($row->estadou != 'Baneado'){?><li><a
					href="administrador/mostrarmodalbaneo?usuario=<?php echo $row->denunciante; ?>&seudonimo=<?php echo $row->denuncianteseudonimo;?>&tipo=Baneado&posi=<?php echo $row->denunciantevotopos;?>&neg=<?php echo $row->denunciantevotoneg;?>"
					class="nmodal<?php echo $row->id;?>">Ban</a></li><?php }else{?>
									<li><a
					href="administrador/mostrarmodalbaneo?usuario=<?php echo $row->denunciante; ?>&seudonimo=<?php echo $row->denuncianteseudonimo;?>&tipo=Activo&posi=<?php echo $row->denunciantevotopos;?>&neg=<?php echo $row->denunciantevotoneg;?>"
					class="nmodal<?php echo $row->id;?>">Unban</a></li><?php  }?>
								</ul>
		</div>
		<div class="arrow"></div>
	</div></td>
<td>
					<?php
				$usuariocadena = $row->seuusuario;
				$votopositivo = $row->posiusuario;
				$votonegativo = $row->negusuario;
				$idusuario = $row->idusuario;
				$estadovar = $row->estadou2;
				if ($row->seuusuarioarticulo != '') {
					$usuariocadena = $row->seuusuarioarticulo;
					$votopositivo = $row->posiusuarioarticulo;
					$votonegativo = $row->negusuarioarticulo;
					$idusuario = $row->idusuarioarticulo;
					$estadovar = $row->estadou3;
				}
				
				$clasecss = '';
				
				if (($this->administracion->vermensajeXusuario ( $idusuario )) == 0) {
					$clasecss = 'nmodal' . $row->id;
				}
				?>
						<a href="#" class="actMenuB<?php echo $row->id;?>"><?php if($estadovar=='Baneado'){ echo '<strike>'.$usuariocadena.'</strike>';}else{ echo $usuariocadena;}?></a>
	<span class="green">+<?php echo $votopositivo;?></span> 
						<?php if($votonegativo > 0){?>						
						<span class="red">-<?php echo $votonegativo;?></span>
						<?php }?>
						
						<div class="act-menu-b">
		<div class="cont">
			<ul>
				<li><a href="store/<?php echo $usuariocadena;?>" target="_blank">Go
						to</a></li>
				<li><a
					href="javascript:historial('<?php echo $usuariocadena."','usuario";?>')">History</a></li>
				<li><a
					href="administrador/mostrarmodalmensaje?id=<?php echo $idusuario.'&seu='.$usuariocadena.'&posi='.$votopositivo.'&neg='.$votonegativo;?>"
					class="<?php echo $clasecss;?>">Send PM</a></li>
				<li><a
					href="administrador/mostrarmodalvotos?id=<?php echo $idusuario.'&seu='.$usuariocadena.'&posi='.$votopositivo.'&neg='.$votonegativo;?>"
					class="nmodal<?php echo $row->id;?>">Vote</a></li>
									<?php if($estadovar != 'Baneado'){?><li><a
					href="administrador/mostrarmodalbaneo?usuario=<?php echo $idusuario; ?>&seudonimo=<?php echo $usuariocadena;?>&tipo=Baneado&posi=<?php echo $votopositivo;?>&neg=<?php echo $votonegativo;?>&idreporte=<?php echo $row->id;?>"
					class="nmodal<?php echo $row->id;?>">Ban</a></li><?php }else{?>
									<li><a
					href="administrador/mostrarmodalbaneo?usuario=<?php echo $idusuario; ?>&seudonimo=<?php echo $usuariocadena;?>&tipo=Activo&posi=<?php echo $votopositivo;?>&neg=<?php echo $votonegativo;?>&idreporte=<?php echo $row->id;?>"
					class="nmodal<?php echo $row->id;?>">Unban</a></li><?php  }?>
								</ul>
		</div>
		<div class="arrow"></div>
	</div>
</td>
<td>
					<?php
				
				$varbanedo = false;
				
				if ($row->paquete != '') {
					$pagina = "product/$row->articulopaq";
					// PArtir vadena
					if ($row->articulopaq != '') {
					}
					// fin partir cadena
					$idpagina = "$row->articulopaq ,$row->transacciones";
					$linkpageid = "#";
					?>
							<a href="#" class="actMenuB<?php echo $row->id;?>"><?php echo $idpagina;?></a>
							<?php
				} else {
					if ($row->idtitulo != '') {
						$pagina = "product/$row->idtitulo";
						$idpagina = "$row->idtitulo";
						?>
								<a href="#" class="actMenuB<?php echo $row->id;?>"><?php
						
						if ($row->estado_articulo != "Baneado") {
							echo $idpagina;
							$linkpageid = "administrador/pageidenabledisable?id=$row->idtitulo&estadoanterior=$row->estadoarticuloanterior&accion=disable&tipo=articulo";
						} else {
							echo "<strike>$idpagina</strike>";
							$linkpageid = "administrador/pageidenabledisable?id=$row->idtitulo&estadoanterior=$row->estadoarticuloanterior&accion=enable&tipo=articulo";
							$varbanedo = true;
						}
						?></a>
								<?php
					} else {
						if ($row->mensaje != '') {
							/*
							 * $pagina = $row->mensaje; $idpagina = "--";
							 * $linkpageid = "#"; echo $idpagina;
							 */
							
							$pagina = "administrador/mensajereportador?id=$row->mensaje";
							$idpagina = "$row->mensaje";
							// $linkpageid = "#";
							?>
									<a href="#" class="actMenuB<?php echo $row->id;?>"><?php
							
							if ($row->denuncia != '') {
								echo $idpagina;
								$linkpageid = "administrador/pageidenabledisable?id=$row->mensaje&tipo=mensaje&accion=disable";
							} else {
								$linkpageid = "administrador/pageidenabledisable?id=$row->mensaje&tipo=mensaje&accion=enable";
								echo "<strike>$idpagina</strike>";
								$varbanedo = true;
							}
							?></a>
									<?php
						} else {
							
							$pagina = "store/$row->seuusuario";
							$idpagina = "--";
							$linkpageid = "#";
							echo $idpagina;
						}
					}
				}
				if ($idpagina == '') {
					$idpagina = '--';
					$pagina = 'administration/dashboard';
					$linkpageid = "#";
					echo $idpagina;
				}
				?>
						
						<div class="act-menu-b">
		<div class="cont">
			<ul>
				<li><a href="<?php echo $pagina;?>" target="_blank">Go to</a></li>
									<?php
				
				if ($varbanedo == true) {
					?>
									<li><a href="<?php echo $linkpageid;?>"
					class="nmodal<?php echo $row->id;?>">Enable</a></li>
									<?php }else{?><li><a href="<?php echo $linkpageid;?>"
					class="nmodal<?php echo $row->id;?>">Disable</a></li><?php }?>
								</ul>
		</div>
		<div class="arrow"></div>
	</div>
</td>
<td>
					<?php if($status != FALSE){?>
						<a href="#" class="actMenuB<?php echo $row->id;?>">Mark as</a>
	<div class="act-menu-b">
		<div class="cont">
			<ul>
				<li><a
					href="javascript:cambiarestado('<?php echo $row->id."','Finalizado";?>')">Done</a></li>
				<li><a
					href="javascript:cambiarestado('<?php echo $row->id."','Pendiente";?>')">Pending</a></li>
				<li><a
					href="javascript:cambiarestado('<?php echo $row->id."','Procesando";?>')">Unmark</a></li>
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
				$fechaultimo = new DateTime ( $row->datemark );
				
				if ($row->markby != '') {
					$mostramarcadopor = $row->markby . " " . $fechaultimo->format ( 'd-m-Y' );
					?><a href="store/<?php echo $row->markby;?>">
									<?php echo $mostramarcadopor;?>
					 				</a>
					 			<?php
				} else {
					echo "--";
				}
				?>
					
					 </td>

<?php
			}
		}
	}
	public function listarmas() {
		// cantidadtotal: cantidadtotal , desde: desde ,muestranow:muestranow}
		$cantidadtotal = $this->input->post ( 'cantidadtotal' );
		$desde = $this->input->post ( 'desde' ) + $this->configuracion->variables ( "cantidadPaginacion" );
		$desde2 = $this->input->post ( 'desde' );
		$muestranow = $this->input->post ( 'muestrashow' );
		$vista = $this->input->post ( 'vista' );
		$vista2 = $this->input->post ( 'vista' );
		
		if ($vista == 'seeall') {
			$vista = false;
		}
		if ($vista == 'pending') {
			$vista = 'Pendiente';
		}
		if ($vista == 'unmarked') {
			$vista = 'Procesando';
		}
		
		$reporte = $this->administracion->datos ( $vista, false, false, $desde, $this->configuracion->variables ( "cantidadPaginacion" ) );
		if (($reporte)) {
			foreach ( $reporte as $row ) {
				// mensajes denunciante
				$clasecss = '';
				$status = TRUE;
				if (($this->administracion->vermensajeXusuario ( $row->denunciante )) == 0) {
					$clasecss = 'nmodal' . $vista2 . $desde2;
				}
				
				// fin mensajes
				?>
<tr id=fila <?php echo $row->id;?>
	<?php if($row->estadopaquete == 'Disputa'){ echo 'class="bg-red"';}else{if($row->estado == "Finalizado"){echo 'class="bg-green"';}else{if($row->estado == "Pendiente"){echo 'class="bg-yellow"';}}}?>>
	<td><?php echo $row->id;?></td>
	<td>es</td>
	<td><?php
				
				$fecha = new DateTime ( $row->fecha );
				echo $fecha->format ( 'd-m-Y' );
				?></td>
	<td> <?php if($row->descripcion != ''){?>
						<a href="#" class="actMenuB<?php echo $vista2.$desde2; ?>"><?php
					if ($row->mensaje != '') {
						echo "PM ";
					}
					echo $row->asunto;
					?></a>
		<div class="act-menu-b w454">
			<div class="cont">
				<p><?php echo $row->descripcion;?></p>
			</div>
			<div class="arrow"></div>
		</div>
						<?php
				} else {
					if ($row->asunto == "Artículo Recibido diferente de la descripción del anuncio") {
						?><a href="#" class="actMenuB<?php echo $vista2.$desde2; ?>"><?php echo "Disputa 1";?>
								</a>
		<div class="act-menu-b w454">
			<div class="cont">
				<p><?php echo $row->asunto;?></p>
			</div>
			<div class="arrow"></div>
		</div>
							
							
							<?php
					} else {
						if ($row->asunto == 'No Pagado') {
							echo $row->asunto;
							$status = FALSE;
						} else {
							echo $row->asunto;
						}
					}
				}
				?>
					</td>
	<td><a href="#" class="actMenuB<?php echo $vista2.$desde2; ?>"><?php if($row->estadou=='Baneado'){ echo '<strike>'.$row->denuncianteseudonimo.'</strike>';}else{ echo $row->denuncianteseudonimo;}?></a>
		<span class="green">+<?php echo $row->denunciantevotopos;?></span> 
						<?php if($row->denunciantevotoneg > 0){?>
						<span class="red">-<?php echo $row->denunciantevotoneg;?></span>
						<?php }?>
						<div class="act-menu-b">
			<div class="cont">
				<ul>
					<li><a href="store/<?php echo $row->denuncianteseudonimo;?>"
						target="_blank">Go to</a></li>
					<li><a
						href="javascript:historial('<?php echo $row->denuncianteseudonimo."','usuario";?>')">History</a></li>
					<li><a
						href="administrador/mostrarmodalmensaje?id=
										<?php echo $row->denunciante.'&seu='.$row->denuncianteseudonimo.'&posi='.$row->denunciantevotopos.'&neg='.$row->denunciantevotoneg;?>"
						class="<?php echo $clasecss;?>">Send PM</a></li>
					<li><a
						href="administrador/mostrarmodalvotos?id=
										<?php echo $row->denunciante.'&seu='.$row->denuncianteseudonimo.'&posi='.$row->denunciantevotopos.'&neg='.$row->denunciantevotoneg;?>"
						class="nmodal<?php echo $vista2.$desde2; ?>">Vote</a></li>
									<?php if($row->estadou != 'Baneado'){?><li><a
						href="administrador/mostrarmodalbaneo?usuario=<?php echo $row->denunciante; ?>&seudonimo=<?php echo $row->denuncianteseudonimo;?>&tipo=Baneado&posi=<?php echo $row->denunciantevotopos;?>&neg=<?php echo $row->denunciantevotoneg;?>"
						class="nmodal<?php echo $vista2.$desde2; ?>">Ban</a></li><?php }else{?>
									<li><a
						href="administrador/mostrarmodalbaneo?usuario=<?php echo $row->denunciante; ?>&seudonimo=<?php echo $row->denuncianteseudonimo;?>&tipo=Activo&posi=<?php echo $row->denunciantevotopos;?>&neg=<?php echo $row->denunciantevotoneg;?>"
						class="nmodal<?php echo $vista2.$desde2; ?>">Unban</a></li><?php  }?>
								</ul>
			</div>
			<div class="arrow"></div>
		</div></td>
	<td>
					<?php
				$usuariocadena = $row->seuusuario;
				$votopositivo = $row->posiusuario;
				$votonegativo = $row->negusuario;
				$idusuario = $row->idusuario;
				$estadovar = $row->estadou2;
				if ($row->seuusuarioarticulo != '') {
					$usuariocadena = $row->seuusuarioarticulo;
					$votopositivo = $row->posiusuarioarticulo;
					$votonegativo = $row->negusuarioarticulo;
					$idusuario = $row->idusuarioarticulo;
					$estadovar = $row->estadou3;
				}
				
				$clasecss = '';
				
				if (($this->administracion->vermensajeXusuario ( $idusuario )) == 0) {
					$clasecss = 'nmodal';
				}
				?>
						<a href="#" class="actMenuB<?php echo $vista2.$desde2; ?>"><?php if($estadovar=='Baneado'){ echo '<strike>'.$usuariocadena.'</strike>';}else{ echo $usuariocadena;}?></a>
		<span class="green">+<?php echo $votopositivo;?></span> 
						<?php if($votonegativo > 0){?>						
						<span class="red">-<?php echo $votonegativo;?></span>
						<?php }?>
						
						<div class="act-menu-b">
			<div class="cont">
				<ul>
					<li><a href="store/<?php echo $usuariocadena;?>" target="_blank">Go
							to</a></li>
					<li><a
						href="javascript:historial('<?php echo $usuariocadena."','usuario";?>')">History</a></li>
					<li><a
						href="administrador/mostrarmodalmensaje?id=<?php echo $idusuario.'&seu='.$usuariocadena.'&posi='.$votopositivo.'&neg='.$votonegativo;?>"
						class="<?php echo $clasecss;?>">Send PM</a></li>
					<li><a
						href="administrador/mostrarmodalvotos?id=<?php echo $idusuario.'&seu='.$usuariocadena.'&posi='.$votopositivo.'&neg='.$votonegativo;?>"
						class="nmodal<?php echo $vista2.$desde2; ?>">Vote</a></li>
									<?php if($estadovar != 'Baneado'){?><li><a
						href="administrador/mostrarmodalbaneo?usuario=<?php echo $idusuario; ?>&seudonimo=<?php echo $usuariocadena;?>&tipo=Baneado&posi=<?php echo $votopositivo;?>&neg=<?php echo $votonegativo;?>&idreporte=<?php echo $row->id;?>"
						class="nmodal<?php echo $vista2.$desde2; ?>">Ban</a></li><?php }else{?>
									<li><a
						href="administrador/mostrarmodalbaneo?usuario=<?php echo $idusuario; ?>&seudonimo=<?php echo $usuariocadena;?>&tipo=Activo&posi=<?php echo $votopositivo;?>&neg=<?php echo $votonegativo;?>&idreporte=<?php echo $row->id;?>"
						class="nmodal<?php echo $vista2.$desde2; ?>">Unban</a></li><?php  }?>
								</ul>
			</div>
			<div class="arrow"></div>
		</div>
	</td>
	<td><?php
				
				$varbanedo = false;
				$banderapaquete = false;
				if ($row->paquete != '') {
					
					// PArtir vadena
					
					$cadenapaquete = array ();
					if ($row->articulopaq != '') {
						$cantidad = substr_count ( $row->articulopaq, ',' );
						if ($cantidad > 0) {
							$miarray = explode ( ',', $row->articulopaq );
							for($i = 0; $i <= $cantidad; $i ++) {
								$cadenapaquete [] = $miarray [$i];
							}
						} else {
							$cadenapaquete [] = $row->articulopaq;
						}
					}
					
					if ($row->transacciones != '') {
						foreach ( explode ( ",", $row->transacciones ) as $t ) {
							$tran = $this->articulo->darTransaccion ( $t );
							if ($tran) {
								$ar = ($this->articulo->darArticulo ( $tran->articulo ));
								$cadenapaquete [] = $ar->id;
							}
						}
					}
					
					// var_dump($cadenapaquete);
					
					// fin partir cadena
					$banderapaquete = true;
					$ok = 0;
					for($i = 0; $i < count ( $cadenapaquete ); $i ++) {
						$coma = ',';
						if ($i == count ( $cadenapaquete ) - 1) {
							$coma = '';
						}
						$idpagina = $cadenapaquete [$i];
						$pagina = "product/$idpagina";
						
						$datosXarticulo = $this->articulo->darArticulo ( $idpagina );
						
						$varbanedo = $datosXarticulo->estado;
						$estadocambiar = 'disable';
						
						if ($varbanedo == 'Baneado') {
							$estadocambiar = 'enable';
							$idpagina = "<strike>$idpagina</strike>";
						}
						
						$linkpageid = "administrador/pageidenabledisable?id=$datosXarticulo->id&estadoanterior=$datosXarticulo->estado_anterior&accion=$estadocambiar&tipo=articulo"; // "product/$cadenapaquete[$i]";
						
						?><span> <a href="#"
			class="actMenuB<?php echo $vista2.$desde2; ?>"><?php echo $idpagina;?> </a> <?php echo $coma;?><div
				class="act-menu-b">
				<div class="cont">
					<ul>
						<li><a href="<?php echo $pagina?>" target="_blank">Go to</a></li><?php
						
						if ($varbanedo == 'Baneado') {
							?><li><a href="<?php echo $linkpageid;?>"
							class="nmodal<?php echo $vista2.$desde2; ?>">Enable</a></li><?php }else{?><li><a
							href="<?php echo $linkpageid;?>"
							class="nmodal<?php echo $vista2.$desde2; ?>">Disable</a></li><?php }?></ul>
				</div>
				<div class="arrow"></div>
			</div>
	</span><?php
						$ok ++;
					}
				} else {
					if ($row->idtitulo != '') {
						$pagina = "product/$row->idtitulo";
						$idpagina = "$row->idtitulo";
						?>
								<a href="#" class="actMenuB<?php echo $vista2.$desde2; ?>"><?php
						
						if ($row->estado_articulo != "Baneado") {
							echo $idpagina;
							$linkpageid = "administrador/pageidenabledisable?id=$row->idtitulo&estadoanterior=$row->estadoarticuloanterior&accion=disable&tipo=articulo";
						} else {
							echo "<strike>$idpagina</strike>";
							$linkpageid = "administrador/pageidenabledisable?id=$row->idtitulo&estadoanterior=$row->estadoarticuloanterior&accion=enable&tipo=articulo";
							$varbanedo = true;
						}
						?></a>
								<?php
					} else {
						if ($row->mensaje != '') {
							/*
							 * $pagina = $row->mensaje; $idpagina = "--";
							 * $linkpageid = "#"; echo $idpagina;
							 */
							
							$pagina = "administrador/mensajereportador?id=$row->mensaje";
							$idpagina = "$row->mensaje";
							// $linkpageid = "#";
							?>
									<a href="#" class="actMenuB<?php echo $vista2.$desde2; ?>"><?php
							
							if ($row->denuncia != '') {
								echo $idpagina;
								$linkpageid = "administrador/pageidenabledisable?id=$row->mensaje&tipo=mensaje&accion=disable";
							} else {
								$linkpageid = "administrador/pageidenabledisable?id=$row->mensaje&tipo=mensaje&accion=enable";
								echo "<strike>$idpagina</strike>";
								$varbanedo = true;
							}
							?></a>
									<?php
						} else {
							
							$pagina = "store/$row->seuusuario";
							$idpagina = "--";
							$linkpageid = "#";
							echo $idpagina;
						}
					}
				}
				if ($idpagina == '') {
					$idpagina = '--';
					$pagina = 'administration/dashboard';
					$linkpageid = "#";
					echo $idpagina;
				}
				
				if ($banderapaquete == false) {
					?><div class="act-menu-b">
			<div class="cont">
				<ul>
					<li><a href="<?php echo $pagina;?>" target="_blank">Go to</a></li><?php
					
					if ($varbanedo == true) {
						?><li><a href="<?php echo $linkpageid;?>"
						class="nmodal<?php echo $vista2.$desde2; ?>">Enable</a></li>
										<?php }else{?><li><a href="<?php echo $linkpageid;?>"
						class="nmodal<?php echo $vista2.$desde2; ?>">Disable</a></li><?php }?></ul>
			</div>
			<div class="arrow"></div>
		</div><?php }?></td>
	<td><?php if($status != FALSE){?>
						<a href="#" class="actMenuB<?php echo $vista2.$desde2; ?>">Mark as</a>
		<div class="act-menu-b">
			<div class="cont">
				<ul>
					<li><a
						href="javascript:cambiarestado('<?php echo $row->id."','Finalizado";?>')">Done</a></li>
					<li><a
						href="javascript:cambiarestado('<?php echo $row->id."','Pendiente";?>')">Pending</a></li>
					<li><a
						href="javascript:cambiarestado('<?php echo $row->id."','Procesando";?>')">Unmark</a></li>
				</ul>
			</div>
			<div class="arrow"></div>
		</div><?php
				} else {
					echo "--";
				}
				?></td>
	<td><?php
				$mostramarcadopor = '';
				$direccionmostramarcadopor = '';
				$fechaultimo = new DateTime ( $row->datemark );
				
				if ($row->markby != '') {
					$mostramarcadopor = $row->markby . " " . $fechaultimo->format ( 'd-m-Y' );
					?><a href="store/<?php echo $row->markby;?>">
									<?php echo $mostramarcadopor;?>
					 				</a>
					 			<?php
				} else {
					echo "--";
				}
				?></td>
</tr><?php
			}
		}
	}
	public function cambiarvermas() {
		$cantidadtotal = $this->input->post ( 'cantidadtotal' );
		$desde = $this->input->post ( 'desde' ) + $this->configuracion->variables ( "cantidadPaginacion" );
		$muestranow = $this->input->post ( 'muestranow' );
		$vista = $this->input->post ( 'vista' );
		if ($cantidadtotal >= $desde) {
			?>
<a title="Ver más"
	href="javascript:vermaslista('<?php echo $cantidadtotal."','".$desde."','".$muestranow."','".$vista;?>')">Ver
	más</a>
<?php
		}
	}
	public function guardarmensaje() {
		$id = $this->input->post ( 'id' );
		$mensaje = nl2br ( $this->input->post ( 'mensaje' ) );
		$mensaje = $this->load->view ( "mail/mail-base.php", array (
				"mensaje" => $mensaje 
		), true );
		$this->administracion->guardarnotificacion ( $id, $mensaje );
		redirect ( "administration/admipm", "refresh" );
	}
	public function buscarnotificacion() {
		$data ['vista'] = 'seeall';
		
		$valor = $this->input->get ( 'valor' );
		$tipo = $this->input->get ( 'tipo' );
		
		$data ['idadmin'] = $this->myuser->id;
		$data ['mensaje'] = $this->administracion->mensajeadmin ( $valor );
		
		$view = "administrador/admipm";
		$this->loadGUI ( $view, $data );
	}
	
	// mensaje PM
	public function mostrarmodalmensaje() {
		$id = $this->input->get ( 'id' );
		$data ['id'] = $id;
		
		if (($this->administracion->vermensajeXusuario ( $id )) == 0) {
			$data ['seudonimo'] = $this->input->get ( 'seu' );
			
			$data ['posi'] = $this->input->get ( 'posi' );
			$data ['neg'] = $this->input->get ( 'neg' );
			$view = "administrador/modal/enviar-mensaje-privado";
			
			$this->load->view ( $view, $data );
		} else {
			$seudonimo = $this->input->get ( 'seu' );
			$view = "messageprofile/$seudonimo/ADMINLOVENDE/inboxadmin";
			redirect ( $view );
		}
	}
	public function enviarmensajepm() {
		$id = $this->input->post ( 'id' );
		
		$texto = $this->input->post ( 'textoenviar' );
		
		$this->administracion->guardarmensajepm ( $id, $texto );
		
		echo '1';
	}
	
	// factura
	public function buscarfactura() {
		$data ['vista'] = 'seeall';
		$fechaclasificado = date ( "m" ) . "-" . date ( "Y" );
		$data ['cantidadtotal'] = $this->administracion->contarfactura ( false, $fechaclasificado );
		$data ['curso'] = $this->administracion->contarfactura ( 'En curso', $fechaclasificado );
		$data ['pendiente'] = $this->administracion->contarfactura ( 'Pendiente', $fechaclasificado );
		$data ['pagado'] = $this->administracion->contarfactura ( 'Pagado', $fechaclasificado );
		
		$valor = $this->input->get ( 'valor' );
		
		$data ['reporte'] = $this->administracion->devolverfactura ( false, $valor, $this->input->get ( "tipo" ), $fechaclasificado );
		$data ['valordefecto'] = $fechaclasificado;
		$view = "administrador/billing";
		$this->loadGUI ( $view, $data );
	}
	public function buscarfacturaXfecha() {
		$data ['vista'] = 'seeall';
		$fechaclasificado = $this->input->get ( 'valor' );
		$data ['cantidadtotal'] = $this->administracion->contarfactura ( false, $fechaclasificado );
		$data ['curso'] = count ( $this->administracion->devolverfactura ( "En curso", false, false, $fechaclasificado ) );
		$data ['pendiente'] = $this->administracion->contarfactura ( 'Pendiente', $fechaclasificado );
		$data ['pagado'] = $this->administracion->contarfactura ( 'Pagado', $fechaclasificado );
		
		$data ['reporte'] = $this->administracion->devolverfactura ( false, false, false, $fechaclasificado );
		$data ['grupofechas'] = $this->administracion->devuelvegrupofechas ();
		$data ['valordefecto'] = $fechaclasificado;
		
		$view = "administrador/billing";
		$this->loadGUI ( $view, $data );
	}
	public function baneo() {
		$idobjeto = $this->input->post ( 'usuario' );
		$tipo = $this->input->post ( 'estado' );
		$tabla = $this->input->post ( 'tabla' );
		$idreporte = $this->input->post ( 'idreporte' );
		
		$this->administracion->banearcampos ( $idobjeto, $tipo, $tabla );
		
		$this->administracion->cambiarestado ( $idreporte, "Finalizado", $idobjeto );
		
		$darreporte = $this->administracion->darXreporte ( $idreporte );
		
		$idusuario = '';
		$usuarioemail = '';
		$motivo = '';
		$anuncio = '';
		$pass = '';
		$seudonimo = '';
		$descripcion = false;
		
		$idarticulo = false;
		// print_r($darreporte);
		
		if ($darreporte) {
			foreach ( $darreporte as $row ) {
				if ($row->paquete != '') {
					$idusuario = $row->idusuario;
					$usuarioemail = $row->seuusuario;
					$motivo = $row->asunto;
					$anuncio = $row->paquete;
					$idarticulo = $row->articulopaq;
					$pass = $row->u2password;
					$seudonimo = $row->seu2;
					$descripcion = false;
				} else {
					
					if ($row->idtitulo != '') {
						$idusuario = $row->idusuarioarticulo;
						$usuarioemail = $row->seuusuarioarticulo;
						$motivo = $row->asunto;
						$anuncio = $row->idtitulo;
						$idarticulo = $row->idarticulo;
						$pass = $row->u3password;
						$seudonimo = $row->seu3;
						$descripcion = false;
					} else {
						if ($row->mensaje != '') {
							$idusuario = $row->idusuarioarticulo;
							$usuarioemail = $row->seuusuario;
							$motivo = $row->asunto;
							$anuncio = false;
							$idarticulo = $row->mensaje;
							$pass = $row->u2password;
							$seudonimo = $row->seu2;
							$descripcion = false;
						} else {
							
							$idusuario = $row->idusuario;
							$usuarioemail = $row->seuusuario;
							$motivo = $row->asunto;
							$anuncio = false;
							$idarticulo = false;
							$pass = $row->u2password;
							$seudonimo = $row->seu2;
							$descripcion = $row->descripcion;
						}
					}
				}
			}
		}
		
		if ($tipo == 'Baneado') {
			$this->load->library ( "myemail" );
			$this->myemail->enviarTemplate ( $usuarioemail, "Cuenta de usuario suspendida", "administrador/mail/usuario-suspendido", array (
					"motivo" => $motivo,
					"anuncio" => $anuncio,
					"idarticulo" => $idarticulo,
					"descripcion" => $descripcion 
			) );
		} else {
			
			$this->load->model ( "usuario_model", "usuario" );
			$usuario = $this->usuario->darUsuarioXId ( $idusuario );
			if ($usuario) {
				$pass = $usuario->generarPassword ();
				$usuario->actualizarPassword ( $pass );
			}
			
			$this->load->library ( "myemail" );
			$this->myemail->enviarTemplate ( $usuarioemail, "Cuenta de usuario reactivada", "administrador/mail/usuario-reactivada", array (
					"seudonimo" => $seudonimo,
					"contrasena" => $pass 
			) );
		}
		
		echo "1";
	}
	
	// mensaje PM
	public function mostrarmodalbaneo() {
		$data ['idobjeto'] = $this->input->get ( 'usuario' );
		$data ['seudonimo'] = $this->input->get ( 'seudonimo' );
		$data ['posi'] = $this->input->get ( 'posi' );
		$data ['neg'] = $this->input->get ( 'neg' );
		$data ['tipo'] = $this->input->get ( 'tipo' );
		$data ['tabla'] = 'usuario';
		$data ['idreporte'] = $this->input->get ( 'idreporte' );
		
		if ($this->input->get ( 'tipo' ) == 'Baneado') {
			$view = "administrador/modal/modalban";
		} else {
			$view = "administrador/modal/modalunban";
		}
		
		$this->load->view ( $view, $data );
	}
	public function obtenerfacturaBD($fecha) {
		$this->db->select ( "fac.id, fac.codigo, fac.mes,usu.id as idusu, usu.seudonimo, fac.fecha, fac.monto_venta, fac.monto_stock, fac.monto_tarifa, fac.iva,
		 fac.estado, fac.tipo_tarifa, fac.paypal_id, usu.positivo, usu.negativo " );
		$this->db->join ( 'usuario as usu', 'usu.id = fac.usuario', 'left' );
		$this->db->where ( "fac.mes", $fecha );
		$this->db->order_by ( "fac.id asc" );
		$retorno = array ();
		
		$r = $this->db->get ( "factura as fac" )->result ();
		if ($r && is_array ( $r ) && count ( $r ) > 0) {
			foreach ( $r as $c ) {
				$retorno [$c->id] = array (
						"id" => $c->id,
						"codigo" => $c->codigo,
						"mes" => $c->mes,
						"usuario" => $c->seudonimo,
						"fecha" => $c->fecha,
						"monto_stock" => $c->monto_stock,
						"monto_venta" => $c->monto_venta,
						"monto_tarifa" => $c->monto_tarifa,
						"iva" => $c->iva,
						"estado" => $c->estado,
						"tipo_tarifa" => $c->tipo_tarifa 
				);
			}
		}
		return $retorno;
	}
	public function exportarexcel() {
		$fecha = $this->input->get ( 'fecha' );
		$c = $this->obtenerfacturaBD ( $fecha );
		$this->load->library ( "PHPExcel" );
		$objPHPExcel = $this->phpexcel;
		
		$h = $objPHPExcel->setActiveSheetIndex ( 0 );
		$objPHPExcel->getActiveSheet ()->setTitle ( 'Categorias' );
		
		$h->setCellValue ( "A1", "id" );
		$h->setCellValue ( "B1", "usuario" );
		$h->setCellValue ( "C1", "codigo" );
		$h->setCellValue ( "D1", "mes" );
		$h->setCellValue ( "E1", "fecha" );
		$h->setCellValue ( "F1", "monto_stock" );
		$h->setCellValue ( "G1", "monto_venta" );
		$h->setCellValue ( "H1", "monto_tarifa" );
		$h->setCellValue ( "I1", "iva" );
		$h->setCellValue ( "J1", "estado" );
		$h->setCellValue ( "K1", "tipo_tarifa" );
		
		$v = 2;
		$cc = "B";
		$this->imprimirfacturas ( $c, $h, $v, $cc );
		
		$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel2007' );
		$base = "files/" . rand () . ".xlsx";
		$dir = BASEPATH . "../$base";
		$objWriter->save ( $dir );
		redirect ( $base, "refresh" );
	}
	public function imprimirfacturas($ca, $h, &$v, $cc) {
		if (count ( $ca ) > 0) {
			foreach ( $ca as $id => $c ) {
				$v ++;
				if (count ( $c ["id"] ) > 0) {
					$h->setCellValue ( "A" . $v, $c ["id"] );
					$h->setCellValue ( "B" . $v, $c ["usuario"] );
					$h->setCellValue ( "C" . $v, $c ["codigo"] );
					$h->setCellValue ( "D" . $v, $c ["mes"] );
					$h->setCellValue ( "E" . $v, $c ["fecha"] );
					$h->setCellValue ( "F" . $v, $c ["monto_stock"] );
					$h->setCellValue ( "G" . $v, $c ["monto_venta"] );
					$h->setCellValue ( "H" . $v, $c ["monto_tarifa"] );
					$h->setCellValue ( "I" . $v, $c ["iva"] );
					$h->setCellValue ( "J" . $v, $c ["estado"] );
					$h->setCellValue ( "K" . $v, $c ["tipo_tarifa"] );
					
					$this->imprimirfacturas ( $c ["id"], $h, $v, chr ( ord ( $cc ) + 1 ) );
				} else {
					$h->setCellValue ( $cc . $v, $c ["usuario"] );
					$h->setCellValue ( chr ( ord ( $cc ) + 1 ) . "$v", $id );
				}
			}
		}
	}
	
	// guardar reporte en base de datos
	public function guardarbdreporte() {
		$exito = false;
		
		if ($this->myuser) {
			$reportador = $this->myuser->id;
			
			$reportado = $this->input->post ( 'reportado' );
			$idmensaje = $this->input->post ( 'idmensaje' );
			
			$descripcion = $this->input->post ( 'descripcion' );
			$motivo = $this->input->post ( 'motivo' );
			
			if ($motivo) {
				$this->administracion->guardarreporte ( $reportador, $reportado, $idmensaje, $descripcion, $motivo );
				$exito = true;
			}
		}
		
		$this->output->set_output ( json_encode ( array (
				"exito" => $exito 
		) ) );
	}
	
	// pageid
	public function pageidenabledisable() {
		// articulo
		if ($this->input->get ( 'tipo' ) == 'articulo') {
			$data ['idarticulo'] = $this->input->get ( 'id' );
			$data ['estadoanterior'] = $this->input->get ( 'estadoanterior' );
			$data ['accion'] = $this->input->get ( 'accion' );
			
			$this->load->model ( 'articulo_model' );
			
			$data ['articulo'] = $this->articulo_model->darArticulo ( $this->input->get ( 'id' ) );
			
			if ($this->input->get ( 'accion' ) == 'enable') {
				$view = "administrador/modal/modalenablepage";
			} else {
				$view = "administrador/modal/modaldisablepage";
			}
			
			$this->load->view ( $view, $data );
		}
		// mensaje
		if ($this->input->get ( 'tipo' ) == 'mensaje') {
			$data ['id'] = $this->input->get ( 'id' );
			$data ['accion'] = $this->input->get ( 'accion' );
			
			if ($this->input->get ( 'accion' ) == 'enable') {
				$view = "administrador/modal/modalenablepagemessage";
			} else {
				$view = "administrador/modal/modaldisablepagemessage";
			}
			$this->load->view ( $view, $data );
		}
	}
	public function pageidarticulo() {
		$idobjeto = $this->input->post ( 'idarticulo' );
		$estado = $this->input->post ( 'estado' );
		$accion = $this->input->post ( 'accion' );
		
		if ($this->input->post ( 'accion' ) == 'disable') {
			$this->administracion->baneararticulo ( $idobjeto, 'Baneado', $estado );
			
			$datosarticulos = $this->administracion->darXarticulo ( $idobjeto );
			
			foreach ( $datosarticulos as $row ) {
				$this->load->library ( "myemail" );
				$this->myemail->enviarTemplate ( $row->email, "Anuncio eliminado", "administrador/mail/anuncio-eliminado", array (
						"idarticulo" => $idobjeto,
						"articulo" => $row->titulo 
				) );
				
				$xx = array (
						"idarticulo" => $idobjeto,
						"articulo" => $row->titulo 
				);
				
				$this->enviarMensajeDisputa ( $row->usuario, "administrador/mail/anuncio-eliminado", $xx );
			}
		} else {
			$this->administracion->baneararticulo ( $idobjeto, $estado );
			
			$datosarticulos = $this->administracion->darXarticulo ( $idobjeto );
			
			foreach ( $datosarticulos as $row ) {
				$this->load->library ( "myemail" );
				$this->myemail->enviarTemplate ( $row->email, "Anuncio reactivado", "administrador/mail/anuncio-reactivado", array (
						"idarticulo" => $idobjeto,
						"articulo" => $row->titulo 
				) );
				
				$xx = array (
						"idarticulo" => $idobjeto,
						"articulo" => $row->titulo 
				);
				
				$this->enviarMensajeDisputa ( $row->usuario, "administrador/mail/anuncio-reactivado", $xx );
			}
		}
		
		echo "1";
	}
	public function pageidmensaje() {
		$idmensaje = $this->input->post ( 'idmensaje' );
		$accion = $this->input->post ( 'accion' );
		
		if ($this->input->post ( 'accion' ) == 'disable') {
			$this->administracion->banearmensaje ( $idmensaje, 'Baneado' );
		} else {
			$this->administracion->banearmensaje ( $idmensaje, 'Denunciado' );
		}
		
		echo "1";
	}
	function enviarMensajeDisputa($id, $template, $params) {
		$mensaje = $this->load->view ( $template, $params, true );
		$re = $this->db->select ( "id" )->where ( array (
				"tipo" => "Administrador" 
		) )->get ( "usuario", 1, 0 )->result ();
		if ($re && is_array ( $re ) && count ( $re ) > 0) {
			$this->db->insert ( "notificacion", array (
					"emisor" => $this->myuser->id,
					"receptor" => $id,
					"mensaje" => $mensaje,
					"fecha" => date ( "Y-m-d H:i:s" ) 
			) );
		}
	}
	
	// envio de emails pruebas
	public function emailprueba() {
		
		// $paquetedatos = $this->darPaquete ( 36 );
		if (1 == 1) {
			$this->db->update ( "reporte", array (
					"estado" => "Finalizado" 
			), array (
					"paquete" => 49,
					"asunto" => "Unmatch" 
			) );
		}
	}
}

?>
