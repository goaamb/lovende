

<div class="content">
	<div class="wrapper clearfix">
		<header class="cont-cab">
			
			<h1>Dashboard</h1>
			<p><a href="administration/dashboard" title="reports">Reports</a> | admin PM | <a href="administration/billing" title="billing">billing</a></p>
		</header>

		<ul class="comments-list">

			<li class="thread">
				
				<?php if($mensaje){
					//var_dump($mensaje);
					foreach ($mensaje as $row)
					{
						
				
				$this->load->model ( 'usuario_model', 'objusuario' );
				$idusuario = $this->objusuario->darUsuarioXId ( $row->emisor );
				// $usuario = $this->usuario_model->darUsuarioXId($id);
			
				$imagen = imagenPerfil ( $idusuario, "" );
				?>
				<div class="avatar"><img src=<?php echo $imagen;?> alt="Imagen de perfil de xxx" width="64" height="64"/></div>
				<div class="user-comment">
					<p class="user-name"><strong><?php echo $row->seudonimo;?></strong> hace 
					<?php 
					if ($row->anios >= 1) {
						echo $row->anios . ' aÃ±os';
					} else {
							if ($row->meses >= 1) {
								echo $row->meses . ' meses';
							} else {
									if ($row->semanas >= 1) {
										echo $row->semanas . ' semanas';
									} else {
											if ($row->dias >= 1) {
												echo $row->dias . ' dias';
											} else {
													if ($row->horas >= 1) {
														echo $row->horas . ' horas';
													} else {
															if ($row->minutos >= 1) {
																echo $row->minutos . ' minutos';
															} else {
																	echo $row->segundos . ' segundos';
																	}
															}
													}
											}
									}
							}
					
					?>
					
					</p>
					<div class="justify">
						<p><?php echo $row->mensaje;?></p>
						</br>
					</div>
					
				</div>
				<?php 
					}
				}?>
				

			</li><!--thread-->

		</ul><!--comments-list-->

		

		

	</div><!--wrapper-->
</div><!--content-->